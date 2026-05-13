<?php
/**
 * Elementor integration hooks for Spectre Icons.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap integration ONLY when Elementor is present.
 *
 * @return void
 */
function spectre_icons_elementor_bootstrap() {
	static $bootstrapped = false;

	if ( $bootstrapped ) {
		return;
	}

	// Elementor not installed or not loaded yet.
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'spectre_icons_elementor_missing_elementor_notice' );
		add_action( 'elementor/loaded', 'spectre_icons_elementor_bootstrap', 20 );
		return;
	}

	// Version check for Elementor.
	if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0', '<' ) ) {
		add_action( 'admin_notices', 'spectre_icons_elementor_old_elementor_notice' );
		return;
	}

	$bootstrapped = true;

	$settings = new Spectre_Icons_Elementor_Settings();
	$manager  = Spectre_Icons_Elementor_Library_Adapter::instance( $settings );

	// Register Elementor icon tabs.
	add_filter(
		'elementor/icons_manager/additional_tabs',
		array( $manager, 'register_additional_tabs' )
	);

	// Enqueue CSS/JS.
	add_action( 'elementor/editor/before_enqueue_scripts', 'spectre_icons_elementor_enqueue_styles' );
	add_action( 'elementor/editor/before_enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts' );
	add_action( 'elementor/frontend/after_enqueue_styles', 'spectre_icons_elementor_enqueue_styles' );
	add_action( 'elementor/preview/enqueue_styles', 'spectre_icons_elementor_enqueue_styles' );
	add_action( 'elementor/preview/enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts' );
	add_action( 'wp_enqueue_scripts', 'spectre_icons_elementor_enqueue_preview_assets' );

	// Admin notice for missing manifests.
	add_action( 'admin_notices', 'spectre_icons_elementor_missing_manifest_notice' );

	// Clear Elementor's file cache once after each plugin version change so
	// icons never appear blank in the editor after an update.
	add_action( 'elementor/init', 'spectre_icons_maybe_flush_elementor_cache', 100 );
}
add_action( 'plugins_loaded', 'spectre_icons_elementor_bootstrap', 20 );

/**
 * Admin notice when Elementor version is too old.
 *
 * Scoped strictly to Plugins screen.
 *
 * @return void
 */
function spectre_icons_elementor_old_elementor_notice() {
	if (
		! is_admin() ||
		wp_doing_ajax() ||
		! current_user_can( 'activate_plugins' )
	) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'plugins' !== $screen->id ) {
		return;
	}

	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Spectre Icons requires Elementor 3.0.0 or higher. Please upgrade Elementor to use this plugin.', 'spectre-icons' );
	echo '</p></div>';
}

/**
 * Admin notice when Elementor is missing.
 *
 * Scoped strictly to Plugins screen.
 *
 * @return void
 */
function spectre_icons_elementor_missing_elementor_notice() {
	if (
		! is_admin() ||
		wp_doing_ajax() ||
		did_action( 'elementor/loaded' ) ||
		! current_user_can( 'activate_plugins' )
	) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'plugins' !== $screen->id ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Spectre Icons requires Elementor to be active.', 'spectre-icons' );
	echo '</p></div>';
}

/**
 * Enqueue CSS for Elementor editor + preview.
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_styles() {
	wp_enqueue_style(
		'spectre-icons-elementor',
		SPECTRE_ICONS_URL . 'assets/css/admin/spectre-icons-admin.css',
		array(),
		SPECTRE_ICONS_VERSION
	);

	// Hide disabled tabs dynamically via CSS to prevent UI flashing in Elementor's React interface.
	if ( function_exists( 'spectre_icons_get_library_definitions' ) && function_exists( 'spectre_icons_elementor_is_library_enabled' ) ) {
		$definitions = spectre_icons_get_library_definitions();
		$hidden_css  = '';

		foreach ( array_keys( $definitions ) as $slug ) {
			$slug_clean = sanitize_key( (string) $slug );
			if ( '' === $slug_clean ) {
				continue;
			}

			if ( ! spectre_icons_elementor_is_library_enabled( $slug_clean ) ) {
				$escaped_slug = esc_attr( $slug_clean );
				// Target Elementor's various tab and control attributes dynamically.
				$hidden_css .= sprintf(
					'[data-library="%1$s"], [data-tab="%1$s"], [data-icon-library="%1$s"], [data-name="%1$s"], [data-value="%1$s"], [data-id="%1$s"], [href*="%1$s"], [aria-controls*="%1$s"], [id*="%1$s"] { display: none !important; } ',
					$escaped_slug
				);
			}
		}

		if ( '' !== $hidden_css ) {
			wp_add_inline_style( 'spectre-icons-elementor', $hidden_css );
		}
	}
}

/**
 * Enqueue JS for icon previews (editor UI).
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_icon_scripts() {

	// Prevent wp-auth-check from breaking Elementor iframe.
	if ( wp_script_is( 'wp-auth-check', 'enqueued' ) ) {
		wp_dequeue_script( 'wp-auth-check' );
	}

	wp_enqueue_script(
		'spectre-icons-elementor-js',
		SPECTRE_ICONS_URL . 'assets/js/elementor/spectre-icons-elementor.js',
		array( 'jquery' ),
		SPECTRE_ICONS_VERSION,
		true
	);

	$definitions = spectre_icons_get_library_definitions();
	$libraries   = array();

	foreach ( $definitions as $slug => $def ) {
		$slug = sanitize_key( $slug );

		if ( '' === $slug || empty( $def['manifest_file'] ) ) {
			continue;
		}
		$manifest_file = sanitize_file_name( (string) $def['manifest_file'] );
		if ( '' === $manifest_file ) {
			continue;
		}

		$manifest_path = spectre_icons_resolve_manifest_path( $manifest_file );
		if ( ! $manifest_path ) {
			continue;
		}

		$prefix_raw = isset( $def['class_prefix'] ) ? (string) $def['class_prefix'] : '';
		$prefix     = preg_replace( '/[^a-z0-9\-_]/i', '', $prefix_raw );
		$label      = isset( $def['label'] ) ? (string) $def['label'] : $slug;

		$style = isset( $def['style'] ) ? (string) $def['style'] : '';
		if ( '' === $style ) {
			if ( false !== strpos( $slug, 'lucide' ) ) {
				$style = 'outline';
			} elseif ( false !== strpos( $slug, 'fontawesome' ) ) {
				$style = 'filled';
			}
		}

		$libraries[ $slug ] = array(
			'json'     => SPECTRE_ICONS_URL . 'assets/manifests/' . $manifest_file,
			'label'    => $label,
			'prefix'   => $prefix,
			'selector' => $prefix ? '[class*="' . $prefix . '"]' : '',
			'style'    => $style,
			'enabled'  => function_exists( 'spectre_icons_elementor_is_library_enabled' )
				? spectre_icons_elementor_is_library_enabled( $slug )
				: true,
		);
	}

	wp_localize_script(
		'spectre-icons-elementor-js',
		'SpectreIconsElementorConfig',
		array(
			'libraries' => $libraries,
		)
	);
}

/**
 * Whether any manifests are available.
 *
 * @return bool
 */
function spectre_icons_elementor_manifests_available() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	foreach ( spectre_icons_get_library_definitions() as $def ) {
		$file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
		if ( '' !== $file && null !== spectre_icons_resolve_manifest_path( $file ) ) {
			$cache = true;
			return $cache;
		}
	}

	$cache = false;
	return $cache;
}

/**
 * Admin notice if manifests are missing.
 *
 * Scoped to Plugins + this plugin’s settings page only.
 *
 * @return void
 */
function spectre_icons_elementor_missing_manifest_notice() {
	if (
		! is_admin() ||
		wp_doing_ajax() ||
		! current_user_can( 'manage_options' )
	) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if (
		! $screen ||
		! in_array( $screen->id, array( 'plugins', 'settings_page_spectre-icons-elementor' ), true )
	) {
		return;
	}

	if ( spectre_icons_elementor_manifests_available() ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__(
		'Spectre Icons: No icon manifests found. Icons may not appear in Elementor until manifests are generated or installed.',
		'spectre-icons'
	);
	echo '</p></div>';
}

/**
 * Fallback enqueue for Elementor preview iframe.
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_preview_assets() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['elementor-preview'] ) ) {
		return;
	}

	spectre_icons_elementor_enqueue_styles();
	spectre_icons_elementor_enqueue_icon_scripts();
}

/**
 * Clear Elementor's file cache once after each plugin version change.
 *
 * Runs on elementor/init (Elementor fully loaded) and is a no-op after the
 * first admin request following a version bump.  This prevents blank icon
 * previews in the Elementor editor after a plugin update.
 *
 * @return void
 */
function spectre_icons_maybe_flush_elementor_cache() {
	if ( get_option( 'spectre_icons_version' ) === SPECTRE_ICONS_VERSION ) {
		return;
	}

	// Record new version first so a fatal during the flush does not loop.
	update_option( 'spectre_icons_version', SPECTRE_ICONS_VERSION, false );

	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	$files_manager = isset( \Elementor\Plugin::$instance->files_manager )
		? \Elementor\Plugin::$instance->files_manager
		: null;

	if ( $files_manager && method_exists( $files_manager, 'clear_cache' ) ) {
		$files_manager->clear_cache();
	}
}
