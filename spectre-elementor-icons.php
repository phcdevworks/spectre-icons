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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPECTRE_ELEMENTOR_ICONS_FILE', __FILE__ );
define( 'SPECTRE_ELEMENTOR_ICONS_PATH', __DIR__ );
define( 'SPECTRE_ELEMENTOR_ICONS_URL', plugin_dir_url( __FILE__ ) );
define( 'SPECTRE_ELEMENTOR_ICONS_MANIFEST_PATH', SPECTRE_ELEMENTOR_ICONS_PATH . '/assets/manifests' );
define( 'SPECTRE_ELEMENTOR_ICONS_MANIFEST_URL', SPECTRE_ELEMENTOR_ICONS_URL . 'assets/manifests/' );

require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-settings.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-library-manager.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/class-spectre-elementor-icons-lucide.php';
require_once SPECTRE_ELEMENTOR_ICONS_PATH . '/includes/spectre-elementor-icon-libraries.php';

add_action(
	'plugins_loaded',
	static function () {
		$settings = Spectre_Elementor_Icons_Settings::instance();
		Spectre_Elementor_Icons_Library_Manager::instance( $settings );
	}
);

/**
 * Enqueue Elementor editor assets for Spectre icon previews.
 */
function spectre_elementor_icons_register_styles() {
	wp_register_style(
		'spectre-elementor-icons-shared',
		SPECTRE_ELEMENTOR_ICONS_URL . 'assets/css/spectre-elementor-icons-admin.css',
		[],
		'0.1.0'
	);
}
add_action( 'init', 'spectre_elementor_icons_register_styles' );

/**
 * Front-end styles for inline SVG icons.
 */
function spectre_elementor_icons_enqueue_frontend_styles() {
	wp_enqueue_style( 'spectre-elementor-icons-shared' );
}
add_action( 'wp_enqueue_scripts', 'spectre_elementor_icons_enqueue_frontend_styles' );
add_action( 'elementor/frontend/after_enqueue_styles', 'spectre_elementor_icons_enqueue_frontend_styles' );

function spectre_elementor_icons_enqueue_editor_assets() {
	$script_handle = 'spectre-elementor-icons-admin';

	wp_enqueue_style( 'spectre-elementor-icons-shared' );

	wp_register_script(
		$script_handle,
		SPECTRE_ELEMENTOR_ICONS_URL . 'assets/js/spectre-elementor-icons-admin.js',
		[],
		'0.1.0',
		true
	);

	$libraries = apply_filters(
		'spectre_elementor_icon_preview_libraries',
		[
			'spectre-lucide' => [
				'prefix'   => 'lucide-',
				'selector' => 'i.lucide',
				'json'     => SPECTRE_ELEMENTOR_ICONS_MANIFEST_URL . 'spectre-lucide.json',
			],
		]
	);

	wp_localize_script(
		$script_handle,
		'SpectreElementorIconsConfig',
		[
			'libraries' => $libraries,
		]
	);

	wp_enqueue_script( $script_handle );
}

add_action( 'elementor/editor/after_enqueue_scripts', 'spectre_elementor_icons_enqueue_editor_assets' );
