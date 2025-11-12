<?php

/**
 * Registers Spectre-provided icon libraries from generated manifests.
 *
 * @package SpectreElementorIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('spectre_elementor_get_icon_library_definitions')) :
	/**
	 * Describe the manifest-backed icon packs bundled with the plugin.
	 *
	 * @return array
	 */
	function spectre_elementor_get_icon_library_definitions()
	{
		$definitions = [
			'spectre-lucide'      => [
				'label'          => __('Lucide Icons', 'spectre-elementor-icons'),
				'description'    => __('Open-source Lucide outline icons.', 'spectre-elementor-icons'),
				'manifest'       => 'spectre-lucide.json',
				'display_prefix' => 'lucide',
				'class_prefix'   => 'lucide-',
				'label_icon'     => 'eicon-star',
				'style'          => 'outline',
				'preview_selector' => 'i[class*="lucide-"]',
			],
			'spectre-fontawesome' => [
				'label'          => __('Font Awesome', 'spectre-elementor-icons'),
				'description'    => __('Font Awesome Free (solid, regular, brands).', 'spectre-elementor-icons'),
				'manifest'       => 'spectre-fontawesome.json',
				'display_prefix' => 'sfa',
				'class_prefix'   => 'sfa-',
				'label_icon'     => 'eicon-font',
				'style'          => 'filled',
				'preview_selector' => 'i[class*="sfa-"]',
			],
		];

		return apply_filters('spectre_elementor_icon_library_definitions', $definitions);
	}
endif;

if (! function_exists('spectre_elementor_get_icon_preview_config')) :
	/**
	 * Build the preview configuration consumed by the editor JS.
	 *
	 * @return array
	 */
	function spectre_elementor_get_icon_preview_config()
	{
		$config      = [];
		$definitions = spectre_elementor_get_icon_library_definitions();

		foreach ($definitions as $slug => $definition) {
			$manifest_file = trailingslashit(SPECTRE_ELEMENTOR_ICONS_MANIFEST_PATH) . ltrim($definition['manifest'], '/\\');

			if (! file_exists($manifest_file)) {
				continue;
			}

			$config[$slug] = [
				'prefix'   => $definition['class_prefix'],
				'selector' => ! empty($definition['preview_selector'])
					? $definition['preview_selector']
					: 'i.' . sanitize_html_class($definition['display_prefix']),
				'json'     => trailingslashit(SPECTRE_ELEMENTOR_ICONS_MANIFEST_URL) . ltrim($definition['manifest'], '/\\'),
				'style'    => $definition['style'],
			];
		}

		return apply_filters('spectre_elementor_icon_preview_libraries', $config);
	}
endif;

if (! function_exists('spectre_elementor_register_manifest_libraries')) :
	/**
	 * Register each manifest-backed library with the plugin settings and Elementor.
	 *
	 * @param array $libraries Existing libraries.
	 *
	 * @return array
	 */
	function spectre_elementor_register_manifest_libraries($libraries)
	{
		$definitions = spectre_elementor_get_icon_library_definitions();

		foreach ($definitions as $slug => $definition) {
			$manifest_file = trailingslashit(SPECTRE_ELEMENTOR_ICONS_MANIFEST_PATH) . ltrim($definition['manifest'], '/\\');

			$registered = Spectre_Elementor_Icons_Manifest_Renderer::register_manifest(
				$slug,
				$manifest_file,
				[
					'class_prefix' => $definition['class_prefix'],
					'style'        => $definition['style'],
				]
			);

			if (! $registered) {
				continue;
			}

			$icon_slugs = Spectre_Elementor_Icons_Manifest_Renderer::get_icon_slugs($slug);

			if (empty($icon_slugs)) {
				continue;
			}

			// Get the manifest URL for fetchJson
			$manifest_url = plugin_dir_url(SPECTRE_ELEMENTOR_ICONS_FILE) . 'assets/manifests/' . $definition['manifest'];

			$libraries[$slug] = [
				'label'       => $definition['label'],
				'description' => $definition['description'],
				'config'      => [
					'name'            => $slug,
					'label'           => $definition['label'],
					'labelIcon'       => $definition['label_icon'],
					'displayPrefix'   => $definition['display_prefix'],
					'prefix'          => $definition['class_prefix'],
					'icons'           => $icon_slugs,
					'fetchJson'       => $manifest_url,
					'native'          => false,
					'render_callback' => ['Spectre_Elementor_Icons_Manifest_Renderer', 'render_icon'],
					'ver'             => '0.1.0',
				],
			];
		}

		return $libraries;
	}
	add_filter('spectre_elementor_icon_libraries', 'spectre_elementor_register_manifest_libraries');
endif;
