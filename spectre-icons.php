<?php

/**

 * Plugin Name: Spectre Icons
 * Plugin URI: https://github.com/phcdevworks/spectre-icons
 * Description: Modular Spectre icon libraries for WordPress builders. Includes Elementor integration.
 * Version: 0.0.1
 * Author: Spectre Plugins by PHCDevworks
 * Author URI: https://phcdevworks.com/
 * Requires at least: 6.0
 * Tested up to: 6.8.3
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spectre-icons
 * Domain Path: /languages
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

define('SPECTRE_ICONS_FILE', __FILE__);
define('SPECTRE_ICONS_PATH', __DIR__);
define('SPECTRE_ICONS_URL', plugin_dir_url(__FILE__));
define('SPECTRE_ICONS_VERSION', '1.0.0');
define('SPECTRE_ICONS_MANIFEST_PATH', SPECTRE_ICONS_PATH . '/assets/manifests');
define('SPECTRE_ICONS_MANIFEST_URL', SPECTRE_ICONS_URL . 'assets/manifests/');

if (! function_exists('spectre_icons_load_textdomain')) :
	/**
	 * Load translations for WordPress.org compatibility.
	 */
	function spectre_icons_load_textdomain() {
		load_plugin_textdomain(
			'spectre-icons',
			false,
			dirname(plugin_basename(SPECTRE_ICONS_FILE)) . '/languages/'
		);
	}
	add_action('init', 'spectre_icons_load_textdomain');
endif;

require_once SPECTRE_ICONS_PATH . '/includes/class-spectre-icons-svg-sanitizer.php';
require_once SPECTRE_ICONS_PATH . '/includes/elementor/class-spectre-icons-elementor-settings.php';
require_once SPECTRE_ICONS_PATH . '/includes/elementor/class-spectre-icons-elementor-manifest-renderer.php';
require_once SPECTRE_ICONS_PATH . '/includes/elementor/class-spectre-icons-elementor-library-manager.php';
require_once SPECTRE_ICONS_PATH . '/includes/elementor/icon-libraries.php';

if (! function_exists('spectre_icons_get_asset_version')) :
	/**
	 * Generate a cache-busting version string for plugin assets.
	 *
	 * @param string $relative_path Relative path from plugin root.
	 *
	 * @return string
	 */
	function spectre_icons_get_asset_version($relative_path) {
		$default_version = SPECTRE_ICONS_VERSION;
		$path            = trailingslashit(SPECTRE_ICONS_PATH) . ltrim($relative_path, '/\\');

		if (! file_exists($path)) {
			return $default_version;
		}

		return (string) filemtime($path);
	}
endif;

require_once SPECTRE_ICONS_PATH . '/includes/elementor/integration-hooks.php';
