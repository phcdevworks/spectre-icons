<?php

/**
 * Registers Spectre-provided icon libraries from generated manifests.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Return base definition for each icon library.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_library_definitions() {
	return array(
		'spectre-lucide' => array(
			'label'         => __('Lucide Icons', 'spectre-icons'),
			'label_icon'    => 'eicon-check',
			'manifest_file' => 'spectre-lucide.json',
			'class_prefix'  => 'spectre-lucide-',
		),
		'spectre-fontawesome' => array(
			'label'         => __('Font Awesome', 'spectre-icons'),
			'label_icon'    => 'eicon-star',
			'manifest_file' => 'spectre-fontawesome.json',
			'class_prefix'  => 'spectre-fa-',
		),
	);
}

/**
 * Build preview config for Elementor.
 *
 * Note: This returns config in the "tab" shape. Your Library Manager expects
 * libraries shaped as [ slug => [ 'label' => ..., 'config' => ... ] ].
 * Keep this function only if you use it elsewhere; the filter below is what Elementor uses.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_preview_config() {
	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$config      = array();

	$base_dir = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');

	foreach ($definitions as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		$manifest_file = isset($def['manifest_file']) ? sanitize_file_name($def['manifest_file']) : '';
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = $base_dir . $manifest_file;

		if (! file_exists($manifest_path)) {
			continue;
		}

		$config[$slug] = array(
			'name'            => $slug,
			'label'           => isset($def['label']) ? (string) $def['label'] : $slug,
			'labelIcon'       => isset($def['label_icon']) ? (string) $def['label_icon'] : '',
			'manifest'        => $manifest_path,
			'prefix'          => isset($def['class_prefix']) ? (string) $def['class_prefix'] : '',
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
 * This is the authoritative source used by the Library Manager.
 *
 * @param array $libraries Existing icon libraries.
 * @return array Modified libraries array.
 */
function spectre_icons_elementor_register_manifest_libraries($libraries) {
	if (! is_array($libraries)) {
		$libraries = array();
	}

	$defs     = spectre_icons_elementor_get_icon_library_definitions();
	$base_dir = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');

	foreach ($defs as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		$manifest_file = isset($def['manifest_file']) ? sanitize_file_name($def['manifest_file']) : '';
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = $base_dir . $manifest_file;

		if (! file_exists($manifest_path)) {
			continue;
		}

		$label        = isset($def['label']) ? (string) $def['label'] : $slug;
		$label_icon   = isset($def['label_icon']) ? (string) $def['label_icon'] : '';
		$class_prefix = isset($def['class_prefix']) ? (string) $def['class_prefix'] : '';

		// Register manifest with renderer.
		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			$slug,
			$manifest_path,
			array(
				'prefix'  => $class_prefix,
				'options' => array(),
			)
		);

		// Build config for the Library Manager / Elementor.
		$libraries[$slug] = array(
			'label'  => $label,
			'config' => array(
				'name'            => $slug,
				'label'           => $label,
				'labelIcon'       => $label_icon,
				'manifest'        => $manifest_path,
				'prefix'          => $class_prefix,
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
