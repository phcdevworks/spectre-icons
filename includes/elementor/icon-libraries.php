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
			'label'        => __('Lucide Icons', 'spectre-icons'),
			'label_icon'   => 'eicon-check',
			'manifest_file' => 'spectre-lucide.json',
			'class_prefix' => 'spectre-lucide-',
		),
		'spectre-fontawesome' => array(
			'label'        => __('Font Awesome', 'spectre-icons'),
			'label_icon'   => 'eicon-star',
			'manifest_file' => 'spectre-fontawesome.json',
			'class_prefix' => 'spectre-fa-',
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

		$base_dir = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');

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
			'label'           => $def['label'],
			'labelIcon'       => isset($def['label_icon']) ? $def['label_icon'] : '',
			'manifest' => $manifest_path,
			'prefix'          => isset($def['class_prefix']) ? $def['class_prefix'] : '',
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

		$base_dir = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');

		$manifest_file = isset($def['manifest_file']) ? sanitize_file_name($def['manifest_file']) : '';
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = $base_dir . $manifest_file;

		if (! file_exists($manifest_path)) {
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
