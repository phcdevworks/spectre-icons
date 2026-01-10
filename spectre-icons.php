<?php

/**
 * Plugin Name: Spectre Icons
 * Plugin URI: https://github.com/phcdevworks/spectre-icons
 * Description: Spectre Icons brings modern SVG icon libraries like Lucide and Font Awesome directly into WordPress builders—delivering a unified, performance-first icon system that replaces dozens of fragmented icon plugins.
 * Version: 1.0.0
 * Author: PHCDevworks
 * Author URI: https://phcdevworks.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: spectre-icons
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.8.3
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * ------------------------------------------------------------
 *  CONSTANTS
 * ------------------------------------------------------------
 */
define('SPECTRE_ICONS_VERSION', '1.0.0');
define('SPECTRE_ICONS_PATH', plugin_dir_path(__FILE__));
define('SPECTRE_ICONS_URL', plugin_dir_url(__FILE__));

/**
 * ------------------------------------------------------------
 *  AUTOLOAD / INCLUDE FILES
 * ------------------------------------------------------------
 */
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-library-manager.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-manifest-renderer.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-settings.php';

require_once SPECTRE_ICONS_PATH . 'includes/class-spectre-icons-svg-sanitizer.php';

require_once SPECTRE_ICONS_PATH . 'includes/elementor/icon-libraries.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/integration-hooks.php';

/**
 * ------------------------------------------------------------
 *  PLUGIN INIT
 * ------------------------------------------------------------
 *
 * Elementor bootstrapping lives in includes/elementor/integration-hooks.php.
 */
