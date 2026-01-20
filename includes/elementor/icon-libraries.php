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
			'label'         => 'Lucide Icons',
			'label_icon'    => 'eicon-check',
			'manifest_file' => 'spectre-lucide.json',
			'class_prefix'  => 'spectre-lucide-',
		),
		'spectre-fontawesome' => array(
			'label'         => 'Font Awesome',
			'label_icon'    => 'eicon-star',
			'manifest_file' => 'spectre-fontawesome.json',
			'class_prefix'  => 'spectre-fa-',
		),
	);
}

/**
 * Build preview config for Elementor.
 *
 * NOTE: Informational only. Does NOT register manifests or query icon slugs.
 * The filter below is authoritative and is what Elementor actually uses.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_preview_config() {
	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$config      = array();

	$base_dir  = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');
	$base_real = realpath($base_dir);

	// Normalize to a directory prefix to make strpos checks safer.
	$base_real = $base_real ? trailingslashit(wp_normalize_path($base_real)) : '';

	foreach ($definitions as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		$manifest_file = isset($def['manifest_file']) ? sanitize_file_name((string) $def['manifest_file']) : '';
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = $base_dir . $manifest_file;

		// Path hardening: ensure resolved path stays within manifests directory.
		$real = realpath($manifest_path);
		$real = $real ? wp_normalize_path($real) : '';

		if ('' === $base_real || '' === $real || 0 !== strpos($real, $base_real)) {
			continue;
		}

		if (! file_exists($real)) {
			continue;
		}

		$label_icon = (isset($def['label_icon']) && is_string($def['label_icon']) && preg_match('/^eicon-[a-z0-9\-]+$/', $def['label_icon']))
			? $def['label_icon']
			: '';

		$class_prefix_raw = isset($def['class_prefix']) ? (string) $def['class_prefix'] : '';
		$class_prefix     = preg_replace('/[^a-z0-9\-_]/i', '', $class_prefix_raw);

		$config[$slug] = array(
			'name'            => $slug,
			'label'           => isset($def['label']) ? (string) $def['label'] : $slug,
			'labelIcon'       => $label_icon,
			'manifest'        => $real,
			'prefix'          => $class_prefix,
			'render_callback' => array('Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon'),
			'native'          => false,
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
	if (! is_array($libraries)) {
		$libraries = array();
	}

	$defs      = spectre_icons_elementor_get_icon_library_definitions();
	$base_dir  = trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests/');
	$base_real = realpath($base_dir);

	// Normalize to a directory prefix to make strpos checks safer.
	$base_real = $base_real ? trailingslashit(wp_normalize_path($base_real)) : '';

	foreach ($defs as $slug => $def) {
		$slug = sanitize_key($slug);
		if ('' === $slug) {
			continue;
		}

		$manifest_file = isset($def['manifest_file']) ? sanitize_file_name((string) $def['manifest_file']) : '';
		if ('' === $manifest_file) {
			continue;
		}

		$manifest_path = $base_dir . $manifest_file;

		// Path hardening: ensure resolved path stays within manifests directory.
		$real = realpath($manifest_path);
		$real = $real ? wp_normalize_path($real) : '';

		if ('' === $base_real || '' === $real || 0 !== strpos($real, $base_real)) {
			continue;
		}

		if (! file_exists($real)) {
			continue;
		}

		$label = isset($def['label']) ? (string) $def['label'] : $slug;

		$class_prefix_raw = isset($def['class_prefix']) ? (string) $def['class_prefix'] : '';
		$class_prefix     = preg_replace('/[^a-z0-9\-_]/i', '', $class_prefix_raw);

		$label_icon = (isset($def['label_icon']) && is_string($def['label_icon']) && preg_match('/^eicon-[a-z0-9\-]+$/', $def['label_icon']))
			? $def['label_icon']
			: '';

		// Register manifest once (authoritative path).
		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			$slug,
			$real,
			array(
				'prefix' => $class_prefix,
			)
		);

		$libraries[$slug] = array(
			'label'  => $label,
			'config' => array(
				'name'            => $slug,
				'label'           => $label,
				'labelIcon'       => $label_icon,
				'manifest'        => $real,
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
