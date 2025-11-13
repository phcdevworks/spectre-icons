<?php

/**
 * Plugin Name: Spectre Elementor Icons
 * Description: Adds a settings panel that controls which tabs are exposed in Elementor's icon picker.
 * Version: 0.1.0
 * Author: Spectre
 * Text Domain: spectre-elementor-icons
 *
 * @package SpectreElementorIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

define('SPECTRE_ELEMENTOR_ICONS_FILE', __FILE__);
define('SPECTRE_ELEMENTOR_ICONS_PATH', __DIR__);
define('SPECTRE_ELEMENTOR_ICONS_URL', plugin_dir_url(__FILE__));
define('SPECTRE_ELEMENTOR_ICONS_MANIFEST_PATH', SPECTRE_ELEMENTOR_ICONS_PATH . '/assets/manifests');
define('SPECTRE_ELEMENTOR_ICONS_MANIFEST_URL', SPECTRE_ELEMENTOR_ICONS_URL . 'assets/manifests/');

require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-settings.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-manifest-renderer.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-library-manager.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-lucide.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/spectre-elementor-icon-libraries.php';

add_action(
	'plugins_loaded',
	static function () {
		$settings = Spectre_Elementor_Icons_Settings::instance();
		Spectre_Elementor_Icons_Library_Manager::instance($settings);
	}
);

/**
 * Enqueue icon styles for frontend and editor.
 */
function spectre_elementor_icons_enqueue_styles()
{
	wp_enqueue_style(
		'spectre-elementor-icons',
		SPECTRE_ELEMENTOR_ICONS_URL . 'assets/css/spectre-elementor-icons-admin.css',
		[],
		'0.1.0'
	);
}
add_action('wp_enqueue_scripts', 'spectre_elementor_icons_enqueue_styles');
add_action('elementor/frontend/after_enqueue_styles', 'spectre_elementor_icons_enqueue_styles');
add_action('elementor/editor/after_enqueue_styles', 'spectre_elementor_icons_enqueue_styles');

/**
 * Enqueue JavaScript that injects inline SVGs wherever Elementor renders icons.
 */
function spectre_elementor_icons_enqueue_icon_scripts()
{
	static $script_enqueued = false;

	if ($script_enqueued) {
		return;
	}

	$libraries = spectre_elementor_get_icon_preview_config();

	if (empty($libraries)) {
		return;
	}

	$handle = 'spectre-elementor-icons-admin';

	if (! wp_script_is($handle, 'registered')) {
		wp_register_script(
			$handle,
			SPECTRE_ELEMENTOR_ICONS_URL . 'assets/js/spectre-elementor-icons-admin.js',
			[],
			'0.1.0',
			true
		);
	}

	wp_localize_script(
		$handle,
		'SpectreElementorIconsConfig',
		[
			'libraries' => $libraries,
		]
	);

	wp_enqueue_script($handle);
	$script_enqueued = true;
}
add_action('elementor/editor/after_enqueue_scripts', 'spectre_elementor_icons_enqueue_icon_scripts');
add_action('wp_enqueue_scripts', 'spectre_elementor_icons_enqueue_icon_scripts');
add_action('elementor/frontend/after_enqueue_scripts', 'spectre_elementor_icons_enqueue_icon_scripts');

/**
 * Display an admin warning when no manifests are available, as icons cannot render without them.
 */
function spectre_elementor_icons_missing_manifest_notice()
{
	if (! current_user_can('manage_options')) {
		return;
	}

	$is_settings_screen = isset($_GET['page']) && 'spectre-elementor-icons' === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$is_elementor_editor = isset($_GET['action']) && 'elementor' === $_GET['action']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if (! $is_settings_screen && ! $is_elementor_editor) {
		return;
	}

	if (! spectre_elementor_icons_manifests_available()) {
		echo '<div class="notice notice-error"><p>';
		esc_html_e('Spectre Elementor Icons needs generated manifest files. Run "php bin/generate-icon-manifests.php" and upload the JSON files under assets/manifests/.', 'spectre-elementor-icons');
		echo '</p></div>';
	}
}
add_action('admin_notices', 'spectre_elementor_icons_missing_manifest_notice');

/**
 * Utility: determine if at least one icon manifest exists.
 *
 * @return bool
 */
function spectre_elementor_icons_manifests_available()
{
	static $has_manifests = null;

	if (null !== $has_manifests) {
		return $has_manifests;
	}

	$config = spectre_elementor_get_icon_preview_config();
	$has_manifests = ! empty($config);

	return $has_manifests;
}
