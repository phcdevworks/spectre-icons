<?php
/**
 * Core manifest helper functions for Spectre Icons.
 *
 * Library discovery, definition registry, and path resolution.
 * Builder-agnostic — no Elementor or page-builder dependencies.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return all known icon library definitions (hardcoded + auto-discovered).
 *
 * Hardcoded entries are authoritative and win on slug collision.
 * Any *.json file dropped into assets/manifests/ that is not already
 * defined here will be picked up automatically.
 *
 * @return array<string,array>
 */
function spectre_icons_get_library_definitions() {
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

	$discovered = spectre_icons_discover_manifest_files( array_keys( $hardcoded ) );

	// Hardcoded entries win on slug collision.
	$definitions = array_merge( $discovered, $hardcoded );

	return $definitions;
}

/**
 * Scan the manifests directory and build library definitions for any JSON
 * files not already covered by the hardcoded list.
 *
 * Manifest files may include optional top-level metadata fields:
 *   - label        (string) Human-readable library name.
 *   - class_prefix (string) CSS class prefix, e.g. "my-icons-".
 *   - style        (string) "outline" or "filled".
 *   - label_icon   (string) eicon-* token for the picker tab (Elementor).
 *
 * If these fields are absent the values are derived from the filename.
 *
 * @param string[] $exclude_slugs Slugs already covered by hardcoded definitions.
 * @return array<string,array>
 */
function spectre_icons_discover_manifest_files( array $exclude_slugs = array() ) {
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
 * Resolve and harden a manifest filename to an absolute filesystem path.
 *
 * Ensures the resolved path stays within the plugin's manifests directory.
 *
 * @param string $manifest_file Filename (e.g. 'spectre-lucide.json').
 * @return string|null Absolute path or null on failure.
 */
function spectre_icons_resolve_manifest_path( $manifest_file ) {
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
