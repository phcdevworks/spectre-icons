<?php

/**
 * Elementor integration hooks for Spectre Icons.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Bootstrap integration ONLY when Elementor is present.
 *
 * @return void
 */
function spectre_icons_elementor_bootstrap() {
	static $bootstrapped = false;

	if ($bootstrapped) {
		return;
	}

	// Elementor not installed or not loaded yet.
	if (! did_action('elementor/loaded')) {
		add_action('admin_notices', 'spectre_icons_elementor_missing_elementor_notice');
		add_action('elementor/loaded', 'spectre_icons_elementor_bootstrap', 20);
		return;
	}

	$bootstrapped = true;

	$settings = new Spectre_Icons_Elementor_Settings();
	$manager  = Spectre_Icons_Elementor_Library_Manager::instance($settings);

	// Register Elementor icon tabs.
	add_filter(
		'elementor/icons_manager/additional_tabs',
		array($manager, 'register_additional_tabs')
	);

	// Enqueue CSS/JS.
	add_action('elementor/editor/before_enqueue_scripts', 'spectre_icons_elementor_enqueue_styles');
	add_action('elementor/editor/before_enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts');
	add_action('elementor/frontend/after_enqueue_styles', 'spectre_icons_elementor_enqueue_styles');
	add_action('elementor/preview/enqueue_styles', 'spectre_icons_elementor_enqueue_styles');
	add_action('elementor/preview/enqueue_scripts', 'spectre_icons_elementor_enqueue_icon_scripts');
	add_action('wp_enqueue_scripts', 'spectre_icons_elementor_enqueue_preview_assets');

	// Admin notice for missing manifests.
	add_action('admin_notices', 'spectre_icons_elementor_missing_manifest_notice');
}
add_action('init', 'spectre_icons_elementor_bootstrap', 20);

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
		did_action('elementor/loaded') ||
		! current_user_can('activate_plugins')
	) {
		return;
	}

	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (! $screen || 'plugins' !== $screen->id) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__('Spectre Icons requires Elementor to be active.', 'spectre-icons');
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
		defined('SPECTRE_ICONS_VERSION') ? SPECTRE_ICONS_VERSION : '1.0.0'
	);
}

/**
 * Enqueue JS for icon previews (editor UI).
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_icon_scripts() {

	// Prevent wp-auth-check from breaking Elementor iframe.
	if (wp_script_is('wp-auth-check', 'enqueued')) {
		wp_dequeue_script('wp-auth-check');
	}

	wp_enqueue_script(
		'spectre-icons-elementor-js',
		SPECTRE_ICONS_URL . 'assets/js/elementor/spectre-icons-elementor.js',
		array('jquery'),
		defined('SPECTRE_ICONS_VERSION') ? SPECTRE_ICONS_VERSION : '1.0.0',
		true
	);

	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$libraries   = array();

	foreach ($definitions as $slug => $def) {
		$slug = sanitize_key($slug);

		if ('' === $slug || empty($def['manifest_file'])) {
			continue;
		}

		$manifest_file = sanitize_file_name((string) $def['manifest_file']);
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = SPECTRE_ICONS_PATH . 'assets/manifests/' . $manifest_file;
		if (! file_exists($manifest_path)) {
			continue;
		}

		$prefix_raw = isset($def['class_prefix']) ? (string) $def['class_prefix'] : '';
		// Sanitize prefix for safe CSS selector usage (keep hyphen/underscore).
		$prefix = preg_replace('/[^a-z0-9\-_]/i', '', $prefix_raw);

		$libraries[$slug] = array(
			'json'     => SPECTRE_ICONS_URL . 'assets/manifests/' . $manifest_file,
			'prefix'   => $prefix,
			'selector' => $prefix ? '[class*="' . $prefix . '"]' : '',
			'style'    => (false !== strpos($slug, 'lucide')) ? 'outline' : 'filled',
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

	if (null !== $cache) {
		return $cache;
	}

	$cache = ! empty(spectre_icons_elementor_get_icon_preview_config());
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
		! current_user_can('manage_options')
	) {
		return;
	}

	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (
		! $screen ||
		! in_array($screen->id, array('plugins', 'settings_page_spectre-icons-elementor'), true)
	) {
		return;
	}

	if (spectre_icons_elementor_manifests_available()) {
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
	if (! isset($_GET['elementor-preview'])) {
		return;
	}

	spectre_icons_elementor_enqueue_styles();
	spectre_icons_elementor_enqueue_icon_scripts();
}
