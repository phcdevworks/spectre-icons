<?php
/**
 * Registers Spectre-provided icon libraries.
 *
 * @package SpectreElementorIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'spectre_elementor_register_lucide_library' ) ) :
	/**
	 * Add the Lucide SVG collection to the available custom libraries.
	 *
	 * @param array $libraries Existing Spectre libraries.
	 *
	 * @return array
	 */
	function spectre_elementor_register_lucide_library( $libraries ) {
		$icon_slugs = Spectre_Elementor_Icons_Lucide::get_icon_slugs();

		if ( empty( $icon_slugs ) ) {
			return $libraries;
		}

		$libraries['spectre-lucide'] = [
			'label'       => __( 'Lucide Icons', 'spectre-elementor-icons' ),
			'description' => __( 'Open-source Lucide outline icons.', 'spectre-elementor-icons' ),
			'config'      => [
				'name'            => 'spectre-lucide',
				'label'           => __( 'Lucide Icons', 'spectre-elementor-icons' ),
				'labelIcon'       => 'eicon-star',
				'displayPrefix'   => 'lucide',
				'prefix'          => 'lucide-',
				'icons'           => $icon_slugs,
				'native'          => false,
				'render_callback' => [ 'Spectre_Elementor_Icons_Lucide', 'render_icon' ],
				'ver'             => '0.1.0',
			],
		];

		return $libraries;
	}
	add_filter( 'spectre_elementor_icon_libraries', 'spectre_elementor_register_lucide_library' );
endif;
