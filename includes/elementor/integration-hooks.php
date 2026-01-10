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

	// Prevent double-initializing.
	if (did_action('spectre_icons_elementor_bootstrapped')) {
		return;
	}

	// Elementor not installed or not loaded yet.
	if (! did_action('elementor/loaded')) {
		add_action('elementor/loaded', 'spectre_icons_elementor_bootstrap', 20);
		return;
	}

	do_action('spectre_icons_elementor_bootstrapped');

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
add_action('plugins_loaded', 'spectre_icons_elementor_bootstrap', 20);

/**
 * Enqueue CSS for Elementor editor.
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_styles() {

	$css_path = SPECTRE_ICONS_URL . 'assets/css/admin/spectre-icons-admin.css';

	wp_enqueue_style(
		'spectre-icons-elementor',
		$css_path,
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

	// Avoid wp-auth-check throwing errors inside Elementor editor.
	if (wp_script_is('wp-auth-check', 'enqueued')) {
		wp_dequeue_script('wp-auth-check');
	}

	$js_path = SPECTRE_ICONS_URL . 'assets/js/elementor/spectre-icons-elementor.js';

	wp_enqueue_script(
		'spectre-icons-elementor-js',
		$js_path,
		array('jquery'),
		defined('SPECTRE_ICONS_VERSION') ? SPECTRE_ICONS_VERSION : '1.0.0',
		true
	);

	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$libraries   = array();

	foreach ($definitions as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		if (empty($def['manifest_path']) || ! file_exists($def['manifest_path'])) {
			continue;
		}

		$manifest_url = SPECTRE_ICONS_URL . 'assets/manifests/' . basename($def['manifest_path']);
		$prefix       = isset($def['class_prefix']) ? (string) $def['class_prefix'] : '';

		$style = 'filled';
		if (false !== strpos($slug, 'lucide')) {
			$style = 'outline';
		}

		$libraries[$slug] = array(
			'json'     => $manifest_url,
			'prefix'   => $prefix,
			'selector' => $prefix ? '[class*="' . $prefix . '"]' : '',
			'style'    => $style,
		);
	}

	// Provide icon preview config to JS.
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

	$config = spectre_icons_elementor_get_icon_preview_config();
	$cache  = ! empty($config);

	return $cache;
}

/**
 * Admin notice if manifests are missing.
 *
 * @return void
 */
function spectre_icons_elementor_missing_manifest_notice() {

	if (! current_user_can('manage_options')) {
		return;
	}

	// Only show notice on Elementor or plugin settings screens.
	$screen = get_current_screen();
	if (
		$screen &&
		false === strpos($screen->id, 'elementor') &&
		false === strpos($screen->id, 'spectre')
	) {
		return;
	}

	if (spectre_icons_elementor_manifests_available()) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__('Spectre Icons: No icon manifests found. Icons may not appear in Elementor until manifests are generated or installed.', 'spectre-icons');
	echo '</p></div>';
}

/**
 * Fallback enqueue for Elementor preview iframe when hooks are skipped.
 *
 * @return void
 */
function spectre_icons_elementor_enqueue_preview_assets() {
	// Nonce not required; this only gates preview asset loading by query flag.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if (! isset($_GET['elementor-preview'])) {
		return;
	}

	spectre_icons_elementor_enqueue_styles();
	spectre_icons_elementor_enqueue_icon_scripts();
}
