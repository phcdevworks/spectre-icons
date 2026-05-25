<?php
/**
 * Elementor-specific icon library functions for Spectre Icons.
 *
 * Handles Elementor preferences, enabled/disabled state, Elementor-format
 * preview config, and wiring manifests into the Elementor filter pipeline.
 *
 * Builder-agnostic discovery and path resolution live in:
 * includes/core/manifest-helpers.php
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return stored enabled/disabled states for known icon libraries.
 *
 * @return array<string,bool>
 */
function spectre_icons_elementor_get_icon_library_preferences() {
	$definitions = spectre_icons_get_library_definitions();
	$stored      = get_option( 'spectre_icons_elementor_tabs', array() );
	$stored      = is_array( $stored ) ? $stored : array();
	$prefs       = array();

	foreach ( array_keys( $definitions ) as $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			continue;
		}

		$prefs[ $slug ] = isset( $stored[ $slug ] ) ? (bool) $stored[ $slug ] : true;
	}

	return $prefs;
}

/**
 * Whether a given icon library is enabled in settings.
 *
 * @param string                  $slug  Library slug.
 * @param array<string,bool>|null $prefs Optional preloaded preferences.
 * @return bool
 */
function spectre_icons_elementor_is_library_enabled( $slug, $prefs = null ) {
	$slug = sanitize_key( $slug );
	if ( '' === $slug ) {
		return false;
	}

	if ( ! is_array( $prefs ) ) {
		$prefs = spectre_icons_elementor_get_icon_library_preferences();
	}

	return isset( $prefs[ $slug ] ) ? (bool) $prefs[ $slug ] : true;
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
	$definitions = spectre_icons_get_library_definitions();
	$config      = array();

	foreach ( $definitions as $slug => $def ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}

		$manifest_file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
		$real          = spectre_icons_resolve_manifest_path( $manifest_file );

		if ( ! $real ) {
			continue;
		}

		$label_icon = ( isset( $def['label_icon'] ) && is_string( $def['label_icon'] ) && preg_match( '/^eicon-[a-z0-9\-]+$/', $def['label_icon'] ) )
			? $def['label_icon']
			: '';

		$class_prefix_raw = isset( $def['class_prefix'] ) ? (string) $def['class_prefix'] : '';
		$class_prefix     = preg_replace( '/[^a-z0-9\-_]/i', '', $class_prefix_raw );

		$config[ $slug ] = array(
			'name'            => $slug,
			'label'           => isset( $def['label'] ) ? (string) $def['label'] : $slug,
			'labelIcon'       => $label_icon,
			'manifest'        => $real,
			'prefix'          => $class_prefix,
			'render_callback' => array( 'Spectre_Icons_Icon_Renderer', 'render_icon' ),
			'native'          => false,
			'ver'             => (string) filemtime( $real ),
		);
	}

	return $config;
}

/**
 * Register manifest libraries with the core renderer + return Elementor-ready config.
 *
 * @param array $libraries Existing icon libraries.
 * @return array Modified libraries array.
 */
function spectre_icons_elementor_register_manifest_libraries( $libraries ) {
	if ( ! is_array( $libraries ) ) {
		$libraries = array();
	}

	$defs = spectre_icons_get_library_definitions();

	foreach ( $defs as $slug => $def ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}
		// Support manifest_path (absolute path) for external manifests, e.g. user-uploaded icons.
		if ( ! empty( $def['manifest_path'] ) ) {
			$real = wp_normalize_path( (string) $def['manifest_path'] );
			if ( ! is_file( $real ) ) {
				continue;
			}
		} else {
			$manifest_file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
			$real          = spectre_icons_resolve_manifest_path( $manifest_file );
			if ( ! $real ) {
				continue;
			}
		}

		$label = isset( $def['label'] ) ? (string) $def['label'] : $slug;

		$class_prefix_raw = isset( $def['class_prefix'] ) ? (string) $def['class_prefix'] : '';
		$class_prefix     = preg_replace( '/[^a-z0-9\-_]/i', '', $class_prefix_raw );

		$label_icon = ( isset( $def['label_icon'] ) && is_string( $def['label_icon'] ) && preg_match( '/^eicon-[a-z0-9\-]+$/', $def['label_icon'] ) )
			? $def['label_icon']
			: '';

		$style = isset( $def['style'] ) ? (string) $def['style'] : '';

		// Register manifest with the core registry.
		Spectre_Icons_Manifest_Registry::register_manifest(
			$slug,
			$real,
			array(
				'prefix'  => $class_prefix,
				'options' => array(
					'style' => $style,
				),
			)
		);

		$libraries[ $slug ] = array(
			'label'  => $label,
			'config' => array(
				'name'            => $slug,
				'label'           => $label,
				'labelIcon'       => $label_icon,
				'manifest'        => $real,
				'prefix'          => $class_prefix,
				'icons'           => Spectre_Icons_Manifest_Registry::get_icon_slugs( $slug ),
				'render_callback' => array( 'Spectre_Icons_Icon_Renderer', 'render_icon' ),
				'native'          => false,
				'ver'             => (string) filemtime( $real ),
			),
		);
	}

	return $libraries;
}

add_filter( 'spectre_icons_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries' );
