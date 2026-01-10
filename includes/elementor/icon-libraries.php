<?php

/**
 * Registers Spectre-provided icon libraries from generated manifests.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

// Allow local dev stubs.
$stub_path = dirname(__DIR__, 2) . '/stubs/wordpress-stubs.php';
if (file_exists($stub_path)) {
	require_once $stub_path;
}

/**
 * Return base definition for each icon library.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_library_definitions() {

	$base_path = SPECTRE_ICONS_PATH . 'assets/manifests/';
	return array(
		'spectre-lucide' => array(
			'label'         => 'Lucide Icons',
			'label_icon'    => 'eicon-check',
			'manifest_path' => $base_path . 'spectre-lucide.json',
			'class_prefix'  => 'spectre-lucide-',
		),

		'spectre-fontawesome' => array(
			'label'         => 'Font Awesome',
			'label_icon'    => 'eicon-star',
			'manifest_path' => $base_path . 'spectre-fontawesome.json',
			'class_prefix'  => 'spectre-fa-',
		),

	);
}

/**
 * Build preview config for Elementor.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_preview_config() {
	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$config      = array();

	foreach ($definitions as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		if (
			empty($def['manifest_path']) ||
			! file_exists($def['manifest_path'])
		) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('[Spectre Icons] Missing manifest: ' . $def['manifest_path']);
			}
			continue;
		}

		$config[$slug] = array(
			'name'            => $slug,
			'label'           => $def['label'],
			'labelIcon'       => isset($def['label_icon']) ? $def['label_icon'] : '',
			'manifest'        => $def['manifest_path'],
			'prefix'          => $def['class_prefix'],
			'render_callback' => array('Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon'),
			'native'          => false,
			'icons'           => Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs($slug),
			'ver'             => '0.1.0',
		);
	}

	return $config;
}

/**
 * Register manifest libraries with the renderer + return Elementor-ready config.
 *
 * @param array $libraries Existing icon libraries.
 * @return array Modified libraries array.
 */
function spectre_icons_elementor_register_manifest_libraries($libraries) {

	$defs = spectre_icons_elementor_get_icon_library_definitions();

	foreach ($defs as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		$manifest_path = isset($def['manifest_path']) ? $def['manifest_path'] : '';

		if ('' === $manifest_path || ! file_exists($manifest_path)) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('[Spectre Icons] Skipping library "' . $slug . '" — manifest missing.');
			}
			continue;
		}

		// Register manifest with renderer.
		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			$slug,
			$manifest_path,
			array(
				'prefix'  => isset($def['class_prefix']) ? $def['class_prefix'] : '',
				'options' => array(),
			)
		);

		// Build config for Elementor.
		$libraries[$slug] = array(
			'label'  => $def['label'],
			'config' => array(
				'name'            => $slug,
				'label'           => $def['label'],
				'labelIcon'       => isset($def['label_icon']) ? $def['label_icon'] : '',
				'manifest'        => $manifest_path,
				'prefix'          => isset($def['class_prefix']) ? $def['class_prefix'] : '',
				'icons'           => Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs($slug),
				'render_callback' => array('Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon'),
				'native'          => false,
				'ver'             => '0.1.0',
			),
		);
	}

	return $libraries;
}

add_filter('spectre_icons_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries');
add_filter('spectre_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries');
