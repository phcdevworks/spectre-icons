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
function spectre_elementor_icons_enqueue_editor_assets() {
	$script_handle = 'spectre-elementor-icons-admin';
	$style_handle  = 'spectre-elementor-icons-admin-style';

	wp_enqueue_style(
		$style_handle,
		SPECTRE_ELEMENTOR_ICONS_URL . 'assets/css/spectre-elementor-icons-admin.css',
		[],
		'0.1.0'
	);

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
				'json'     => SPECTRE_ELEMENTOR_ICONS_URL . 'assets/iconpacks/lucide/icons.json',
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
