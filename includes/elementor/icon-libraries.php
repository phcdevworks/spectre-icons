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
 * Resolve a library definition to its real manifest path.
 *
 * Supports both external manifest_path entries (absolute path supplied
 * directly) and bundled manifest_file entries (filename resolved under the
 * plugin's manifests directory).
 *
 * @param array $def Library definition from spectre_icons_get_library_definitions().
 * @return string Absolute manifest path, or empty string when unresolvable.
 */
function spectre_icons_get_def_real_path( array $def ) {
	if ( ! empty( $def['manifest_path'] ) ) {
		$real = wp_normalize_path( (string) $def['manifest_path'] );
		return is_file( $real ) ? $real : '';
	}

	$manifest_file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
	$real          = spectre_icons_resolve_manifest_path( $manifest_file );
	return $real ? $real : '';
}

/**
 * Extract and sanitize the CSS class prefix from a library definition.
 *
 * @param array $def Library definition.
 * @return string Sanitized class prefix.
 */
function spectre_icons_get_def_class_prefix( array $def ) {
	$raw = isset( $def['class_prefix'] ) ? (string) $def['class_prefix'] : '';
	return preg_replace( '/[^a-z0-9\-_]/i', '', $raw );
}

/**
 * Extract and validate the Elementor label icon token from a library definition.
 *
 * Only eicon-* tokens are allowed; anything else returns an empty string.
 *
 * @param array $def Library definition.
 * @return string Validated label icon or empty string.
 */
function spectre_icons_get_def_label_icon( array $def ) {
	if ( isset( $def['label_icon'] ) && is_string( $def['label_icon'] )
		&& preg_match( '/^eicon-[a-z0-9\-]+$/', $def['label_icon'] ) ) {
		return $def['label_icon'];
	}
	return '';
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

		$real = spectre_icons_get_def_real_path( $def );
		if ( '' === $real ) {
			continue;
		}

		$config[ $slug ] = array(
			'name'            => $slug,
			'label'           => isset( $def['label'] ) ? (string) $def['label'] : $slug,
			'labelIcon'       => spectre_icons_get_def_label_icon( $def ),
			'manifest'        => $real,
			'prefix'          => spectre_icons_get_def_class_prefix( $def ),
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
		$real = spectre_icons_get_def_real_path( $def );
		if ( '' === $real ) {
			continue;
		}

		$label        = isset( $def['label'] ) ? (string) $def['label'] : $slug;
		$class_prefix = spectre_icons_get_def_class_prefix( $def );
		$style        = isset( $def['style'] ) ? (string) $def['style'] : '';

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
				'labelIcon'       => spectre_icons_get_def_label_icon( $def ),
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

/**
 * Register all known manifests with the core registry (idempotent).
 *
 * Bypasses the Elementor tab filter chain so the registry is populated
 * before any render_icon call, regardless of whether the
 * elementor/icons_manager/additional_tabs filter has fired.
 *
 * Safe to call multiple times — already-registered libraries are skipped.
 *
 * @return void
 */
function spectre_icons_ensure_manifests_registered() {
	$defs = spectre_icons_get_library_definitions();

	foreach ( $defs as $slug => $def ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}

		if ( Spectre_Icons_Manifest_Registry::has_library( $slug ) ) {
			continue;
		}

		$real = spectre_icons_get_def_real_path( $def );
		if ( '' === $real ) {
			continue;
		}

		$class_prefix = spectre_icons_get_def_class_prefix( $def );
		$style        = isset( $def['style'] ) ? (string) $def['style'] : '';

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
	}
}
