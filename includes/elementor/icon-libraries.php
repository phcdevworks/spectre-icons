<?php
/**

 * Registers Spectre-provided icon libraries from generated manifests.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WordPress function stubs for development environment.
$stub_path = dirname( __DIR__, 2 ) . '/stubs/wordpress-stubs.php';
if ( file_exists( $stub_path ) ) {
	require_once $stub_path;
}

if ( ! function_exists( 'spectre_icons_elementor_get_icon_library_definitions' ) ) :
	/**
	 * Describe the manifest-backed icon packs bundled with the plugin.
	 *
	 * @return array
	 */
	function spectre_icons_elementor_get_icon_library_definitions() {
		$definitions = array(
			'spectre-lucide'      => array(
				'label'            => __( 'Lucide Icons', 'spectre-icons' ),
				'description'      => __( 'Open-source Lucide outline icons.', 'spectre-icons' ),
				'manifest'         => 'spectre-lucide.json',
				'display_prefix'   => 'lucide',
				'class_prefix'     => 'lucide-',
				'label_icon'       => 'eicon-star',
				'style'            => 'outline',
				'preview_selector' => 'i[class*="lucide-"]',
			),
			'spectre-fontawesome' => array(
				'label'            => __( 'Font Awesome', 'spectre-icons' ),
				'description'      => __( 'Font Awesome Free (solid, regular, brands).', 'spectre-icons' ),
				'manifest'         => 'spectre-fontawesome.json',
				'display_prefix'   => 'sfa',
				'class_prefix'     => 'sfa-',
				'label_icon'       => 'eicon-font',
				'style'            => 'filled',
				'preview_selector' => 'i[class*="sfa-"]',
			),
		);

		$definitions = apply_filters( 'spectre_icons_elementor_icon_library_definitions', $definitions );

		return apply_filters( 'spectre_elementor_icon_library_definitions', $definitions );
	}
endif;

if ( ! function_exists( 'spectre_icons_elementor_get_icon_preview_config' ) ) :
	/**
	 * Build the preview configuration consumed by the editor JS.
	 *
	 * @return array
	 */
	function spectre_icons_elementor_get_icon_preview_config() {
		$config      = array();
		$definitions = spectre_icons_elementor_get_icon_library_definitions();

		foreach ( $definitions as $slug => $definition ) {
			$manifest_file = trailingslashit( SPECTRE_ICONS_MANIFEST_PATH ) . ltrim( $definition['manifest'], '/\\' );

			if ( ! file_exists( $manifest_file ) ) {
				continue;
			}

			$config[ $slug ] = array(
				'prefix'   => $definition['class_prefix'],
				'selector' => ! empty( $definition['preview_selector'] )
					? $definition['preview_selector']
					: 'i.' . sanitize_html_class( $definition['display_prefix'] ),
				'json'     => trailingslashit( SPECTRE_ICONS_MANIFEST_URL ) . ltrim( $definition['manifest'], '/\\' ),
				'style'    => $definition['style'],
			);
		}

		$config = apply_filters( 'spectre_icons_elementor_icon_preview_libraries', $config );

		return apply_filters( 'spectre_elementor_icon_preview_libraries', $config );
	}
endif;

if ( ! function_exists( 'spectre_icons_elementor_register_manifest_libraries' ) ) :
	/**
	 * Register each manifest-backed library with the plugin settings and Elementor.
	 *
	 * @param array $libraries Existing libraries.
	 *
	 * @return array
	 */
	function spectre_icons_elementor_register_manifest_libraries( $libraries ) {
		$definitions = spectre_icons_elementor_get_icon_library_definitions();

		foreach ( $definitions as $slug => $definition ) {
			$manifest_file = trailingslashit( SPECTRE_ICONS_MANIFEST_PATH ) . ltrim( $definition['manifest'], '/\\' );

			$registered = Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
				$slug,
				$manifest_file,
				array(
					'class_prefix' => $definition['class_prefix'],
					'style'        => $definition['style'],
				)
			);

			if ( ! $registered ) {
				continue;
			}

			$icon_slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( $slug );

			if ( empty( $icon_slugs ) ) {
				continue;
			}

			// Elementor expects a flat list of icon slugs (without prefixes).
			$formatted_icons = array_values( $icon_slugs );

			$libraries[ $slug ] = array(
				'label'       => $definition['label'],
				'description' => $definition['description'],
				'config'      => array(
					'name'            => $slug,
					'label'           => $definition['label'],
					'labelIcon'       => $definition['label_icon'],
					'displayPrefix'   => $definition['display_prefix'],
					'prefix'          => $definition['class_prefix'],
					'icons'           => $formatted_icons,
					'native'          => false,
					'render_callback' => array( 'Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon' ),
					'ver'             => '0.1.0',
				),
			);
		}

		return $libraries;
	}
	add_filter( 'spectre_icons_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries' );
	add_filter( 'spectre_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries' );
endif;
