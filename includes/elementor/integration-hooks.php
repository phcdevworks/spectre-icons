<?php
/**
 * Elementor integration hooks.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'spectre_icons_elementor_bootstrap' ) ) :
	/**
	 * Wire up Elementor services once WordPress loads.
	 */
	function spectre_icons_elementor_bootstrap() {
		$settings = Spectre_Icons_Elementor_Settings::instance();
		Spectre_Icons_Elementor_Library_Manager::instance( $settings );
	}
	add_action( 'plugins_loaded', 'spectre_icons_elementor_bootstrap' );
endif;

if ( ! function_exists( 'spectre_icons_elementor_enqueue_styles' ) ) :
	/**
	 * Enqueue icon styles for frontend and editor.
	 */
	function spectre_icons_elementor_enqueue_styles() {
		$version = spectre_icons_get_asset_version( 'assets/css/admin/spectre-icons-admin.css' );

		wp_enqueue_style(
			'spectre-icons-elementor',
			SPECTRE_ICONS_URL . 'assets/css/admin/spectre-icons-admin.css',
			array(),
			$version
		);
	}
	add_action( 'wp_enqueue_scripts', 'spectre_icons_elementor_enqueue_styles' );
	add_action( 'elementor/frontend/after_enqueue_styles', 'spectre_icons_elementor_enqueue_styles' );
	add_action( 'elementor/editor/after_enqueue_styles', 'spectre_icons_elementor_enqueue_styles' );
endif;

if ( ! function_exists( 'spectre_icons_elementor_enqueue_icon_scripts' ) ) :
	/**
	 * Enqueue JavaScript that injects inline SVGs wherever Elementor renders icons.
	 */
	function spectre_icons_elementor_enqueue_icon_scripts() {
		static $script_enqueued = false;

		if ( $script_enqueued ) {
			return;
		}

		$libraries = spectre_icons_elementor_get_icon_preview_config();

		if ( empty( $libraries ) ) {
			return;
		}

		$handle         = 'spectre-icons-elementor-admin';
		$script_version = spectre_icons_get_asset_version( 'assets/js/elementor/spectre-icons-elementor.js' );

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			wp_register_script(
				$handle,
				SPECTRE_ICONS_URL . 'assets/js/elementor/spectre-icons-elementor.js',
				array(),
				$script_version,
				true
			);
		}

		$config = array(
			'libraries' => $libraries,
		);

		wp_localize_script( $handle, 'SpectreIconsElementorConfig', $config );
		wp_localize_script( $handle, 'SpectreElementorIconsConfig', $config );

		wp_enqueue_script( $handle );
		$script_enqueued = true;
	}
	add_action( 'elementor/editor/after_enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts' );
	add_action( 'wp_enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts' );
	add_action( 'elementor/frontend/after_enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts' );
endif;

if ( ! function_exists( 'spectre_icons_elementor_missing_manifest_notice' ) ) :
	/**
	 * Display an admin warning when no manifests are available, as icons cannot render without them.
	 */
	function spectre_icons_elementor_missing_manifest_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_settings_screen  = isset( $_GET['page'] ) && 'spectre-icons' === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_elementor_editor = isset( $_GET['action'] ) && 'elementor' === $_GET['action']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $is_settings_screen && ! $is_elementor_editor ) {
			return;
		}

		if ( ! spectre_icons_elementor_manifests_available() ) {
			echo '<div class="notice notice-error"><p>';
			esc_html_e( 'Spectre Icons Elementor needs generated manifest files. Run "php bin/generate-icon-manifests.php" and upload the JSON files under assets/manifests/.', 'spectre-icons' );
			echo '</p></div>';
		}
	}
	add_action( 'admin_notices', 'spectre_icons_elementor_missing_manifest_notice' );
endif;

if ( ! function_exists( 'spectre_icons_elementor_manifests_available' ) ) :
	/**
	 * Utility: determine if at least one icon manifest exists.
	 *
	 * @return bool
	 */
	function spectre_icons_elementor_manifests_available() {
		static $has_manifests = null;

		if ( null !== $has_manifests ) {
			return $has_manifests;
		}

		$config        = spectre_icons_elementor_get_icon_preview_config();
		$has_manifests = ! empty( $config );

		return $has_manifests;
	}
endif;
