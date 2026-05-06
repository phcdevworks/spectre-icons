<?php
/**
 * Registers Spectre-provided icon libraries from generated manifests.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return base definition for each icon library.
 *
 * Hardcoded entries are authoritative and take precedence over any manifest
 * file with the same slug found via auto-discovery. Additional manifests
 * dropped into assets/manifests/ are picked up automatically.
 *
 * @return array<string,array>
 */
function spectre_icons_elementor_get_icon_library_definitions() {
	static $definitions = null;

	if ( null !== $definitions ) {
		return $definitions;
	}

	$hardcoded = array(
		'spectre-lucide'      => array(
			'label'         => 'Lucide Icons',
			'label_icon'    => 'eicon-check',
			'manifest_file' => 'spectre-lucide.json',
			'class_prefix'  => 'spectre-lucide-',
			'style'         => 'outline',
		),
		'spectre-fontawesome' => array(
			'label'         => 'Font Awesome',
			'label_icon'    => 'eicon-star',
			'manifest_file' => 'spectre-fontawesome.json',
			'class_prefix'  => 'spectre-fa-',
			'style'         => 'filled',
		),
	);

	$discovered = spectre_icons_elementor_discover_manifest_files( array_keys( $hardcoded ) );

	// Hardcoded entries win on slug collision.
	$definitions = array_merge( $discovered, $hardcoded );

	return $definitions;
}

/**
 * Scan the manifests directory and build library definitions for any JSON
 * files not already covered by the hardcoded list.
 *
 * Manifest files may include optional top-level metadata fields to control
 * how the library is registered:
 *   - label       (string) Human-readable library name.
 *   - class_prefix (string) CSS class prefix, e.g. "my-icons-".
 *   - style       (string) "outline" or "filled".
 *   - label_icon  (string) Elementor eicon-* token for the picker tab.
 *
 * If these fields are absent the values are derived from the filename.
 *
 * @param string[] $exclude_slugs Slugs already handled by hardcoded definitions.
 * @return array<string,array>
 */
function spectre_icons_elementor_discover_manifest_files( array $exclude_slugs = array() ) {
	$manifest_dir = trailingslashit( SPECTRE_ICONS_PATH . 'assets/manifests/' );

	if ( ! is_dir( $manifest_dir ) ) {
		return array();
	}

	$files = glob( $manifest_dir . '*.json' );

	if ( ! is_array( $files ) || empty( $files ) ) {
		return array();
	}

	$discovered = array();

	foreach ( $files as $file ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			continue;
		}

		$filename = basename( $file );
		$slug     = sanitize_key( substr( $filename, 0, -5 ) ); // strip .json

		if ( '' === $slug || in_array( $slug, $exclude_slugs, true ) ) {
			continue;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $file );
		if ( false === $contents ) {
			continue;
		}

		$data = json_decode( $contents, true );

		if ( ! is_array( $data ) || empty( $data['icons'] ) || ! is_array( $data['icons'] ) ) {
			continue;
		}

		// Label: manifest field → name field → filename.
		if ( ! empty( $data['label'] ) && is_string( $data['label'] ) ) {
			$label = $data['label'];
		} elseif ( ! empty( $data['name'] ) && is_string( $data['name'] ) ) {
			$label = ucwords( str_replace( array( '-', '_' ), ' ', $data['name'] ) );
		} else {
			$label = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
		}

		// Class prefix: manifest field → slug with trailing hyphen.
		$class_prefix = ( ! empty( $data['class_prefix'] ) && is_string( $data['class_prefix'] ) )
			? $data['class_prefix']
			: $slug . '-';

		// Style: manifest field → slug heuristic.
		if ( ! empty( $data['style'] ) && in_array( $data['style'], array( 'outline', 'filled' ), true ) ) {
			$style = $data['style'];
		} elseif ( false !== strpos( $slug, 'lucide' ) ) {
			$style = 'outline';
		} elseif ( false !== strpos( $slug, 'fontawesome' ) || false !== strpos( $slug, '-fa' ) ) {
			$style = 'filled';
		} else {
			$style = '';
		}

		// Label icon: manifest field, validated to eicon-* format.
		$label_icon = '';
		if ( ! empty( $data['label_icon'] ) && is_string( $data['label_icon'] )
			&& preg_match( '/^eicon-[a-z0-9\-]+$/', $data['label_icon'] ) ) {
			$label_icon = $data['label_icon'];
		}

		$discovered[ $slug ] = array(
			'label'         => $label,
			'label_icon'    => $label_icon,
			'manifest_file' => $filename,
			'class_prefix'  => $class_prefix,
			'style'         => $style,
		);
	}

	return $discovered;
}

/**
 * Return stored enabled/disabled states for known icon libraries.
 *
 * @return array<string,bool>
 */
function spectre_icons_elementor_get_icon_library_preferences() {
	$definitions = spectre_icons_elementor_get_icon_library_definitions();
	$stored      = get_option( 'spectre_icons_elementor_tabs', array() );
	$stored      = is_array( $stored ) ? $stored : array();
	$prefs       = array();

	foreach ( $definitions as $slug => $def ) {
		$slug = sanitize_key( $slug );
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
 * Resolve and harden a manifest file path.
 *
 * @param string $manifest_file Filename (e.g. 'spectre-lucide.json').
 * @return string|null Absolute path or null on failure.
 */
function spectre_icons_elementor_resolve_manifest_path( $manifest_file ) {
	$manifest_file = sanitize_file_name( (string) $manifest_file );
	if ( '' === $manifest_file ) {
		return null;
	}

	$base_dir  = trailingslashit( SPECTRE_ICONS_PATH . 'assets/manifests/' );
	$base_real = realpath( $base_dir );

	// Normalize to a directory prefix to make strpos checks safer.
	$base_real = $base_real ? trailingslashit( wp_normalize_path( $base_real ) ) : '';

	$manifest_path = $base_dir . $manifest_file;

	// Path hardening: ensure resolved path stays within manifests directory.
	$real = realpath( $manifest_path );
	$real = $real ? wp_normalize_path( $real ) : '';

	if ( '' === $base_real || '' === $real || 0 !== strpos( $real, $base_real ) ) {
		return null;
	}

	if ( ! file_exists( $real ) ) {
		return null;
	}

	return $real;
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

	foreach ( $definitions as $slug => $def ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}

		$manifest_file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
		$real          = spectre_icons_elementor_resolve_manifest_path( $manifest_file );

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
			'render_callback' => array( 'Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon' ),
			'native'          => false,
			'ver'             => defined( 'SPECTRE_ICONS_VERSION' ) ? SPECTRE_ICONS_VERSION : '1.1.0',
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
function spectre_icons_elementor_register_manifest_libraries( $libraries ) {
	if ( ! is_array( $libraries ) ) {
		$libraries = array();
	}

	$defs = spectre_icons_elementor_get_icon_library_definitions();

	foreach ( $defs as $slug => $def ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}
		$manifest_file = isset( $def['manifest_file'] ) ? (string) $def['manifest_file'] : '';
		$real          = spectre_icons_elementor_resolve_manifest_path( $manifest_file );

		if ( ! $real ) {
			continue;
		}

		$label = isset( $def['label'] ) ? (string) $def['label'] : $slug;

		$class_prefix_raw = isset( $def['class_prefix'] ) ? (string) $def['class_prefix'] : '';
		$class_prefix     = preg_replace( '/[^a-z0-9\-_]/i', '', $class_prefix_raw );

		$label_icon = ( isset( $def['label_icon'] ) && is_string( $def['label_icon'] ) && preg_match( '/^eicon-[a-z0-9\-]+$/', $def['label_icon'] ) )
			? $def['label_icon']
			: '';

		$style = isset( $def['style'] ) ? (string) $def['style'] : '';

		// Register manifest once (authoritative path).
		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
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
				'icons'           => Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( $slug ),
				'render_callback' => array( 'Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon' ),
				'native'          => false,
				'ver'             => defined( 'SPECTRE_ICONS_VERSION' ) ? SPECTRE_ICONS_VERSION : '1.1.0',
			),
		);
	}

	return $libraries;
}

add_filter( 'spectre_icons_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries' );
