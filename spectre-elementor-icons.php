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
