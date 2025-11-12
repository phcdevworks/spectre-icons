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
