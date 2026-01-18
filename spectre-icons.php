<?php

/**
 * Plugin Name: Spectre Icons
 * Plugin URI: https://github.com/phcdevworks/spectre-icons
 * Description: Spectre Icons brings modern SVG icon libraries like Lucide and Font Awesome directly into WordPress builders, delivering a unified, performance-focused icon system.
 * Version: 1.1.0
 * Author: PHCDevworks
 * Author URI: https://phcdevworks.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * SPDX-License-Identifier: GPL-2.0-or-later
 * Text Domain: spectre-icons
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.6
 */

if (! defined('ABSPATH')) {
	exit;
}

define('SPECTRE_ICONS_VERSION', '1.1.0');
define('SPECTRE_ICONS_PATH', plugin_dir_path(__FILE__));
define('SPECTRE_ICONS_URL', plugin_dir_url(__FILE__));

add_action('init', function () {
	load_plugin_textdomain('spectre-icons', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

$includes = array(
	'includes/elementor/class-spectre-icons-elementor-library-manager.php',
	'includes/elementor/class-spectre-icons-elementor-manifest-renderer.php',
	'includes/elementor/class-spectre-icons-elementor-settings.php',
	'includes/class-spectre-icons-svg-sanitizer.php',
	'includes/elementor/icon-libraries.php',
	'includes/elementor/integration-hooks.php',
);

foreach ($includes as $file) {
	$path = SPECTRE_ICONS_PATH . $file;
	if (file_exists($path)) {
		require_once $path;
	}
}
