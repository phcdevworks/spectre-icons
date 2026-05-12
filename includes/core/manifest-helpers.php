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
 * Return all known icon library definitions.
 *
 * Scans assets/manifests/ for *.json files and builds library definitions
 * from metadata embedded in each manifest header.  Only files that contain
 * an "icons" key are registered.
 *
 * Two slugs are SERIALIZATION-ANCHORED in PHP:
 *   spectre-lucide      → spectre-lucide.json   (prefix: spectre-lucide-)
 *   spectre-fontawesome → spectre-fontawesome.json (prefix: spectre-fa-)
 *
 * Their manifest_file and class_prefix are locked here because every Elementor
 * icon saved to the database encodes the prefix in its class value
 * (e.g. "spectre-lucide-arrow-right").  Changing either field would break
 * every icon already placed on the site.  All other display metadata
 * (label, style, label_icon) is read from the JSON header, so the manifests
 * are self-describing and new libraries require no PHP changes.
 *
 * @return array<string,array>
 */
function spectre_icons_get_library_definitions() {
	static $definitions = null;

	if ( null !== $definitions ) {
		return $definitions;
	}

	// Slug => [ manifest_filename, class_prefix ].
	// LOCKED — change only when migrating all serialized icon data in the database.
	$anchored = array(
		'spectre-lucide'      => array( 'spectre-lucide.json',      'spectre-lucide-' ),
		'spectre-fontawesome' => array( 'spectre-fontawesome.json', 'spectre-fa-' ),
	);

	$manifest_dir = trailingslashit( SPECTRE_ICONS_PATH . 'assets/manifests/' );

	if ( ! is_dir( $manifest_dir ) ) {
		$definitions = array();
		return $definitions;
	}

	$files = glob( $manifest_dir . '*.json' );

	if ( ! is_array( $files ) || empty( $files ) ) {
		$definitions = array();
		return $definitions;
	}

	$found = array();

	foreach ( $files as $file ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			continue;
		}

		$filename = basename( $file );
		$slug     = sanitize_key( substr( $filename, 0, -5 ) ); // strip .json

		if ( '' === $slug ) {
			continue;
		}

		// Read only the bytes before the "icons" key — fast for multi-MB manifests.
		$parsed = spectre_icons_read_manifest_header( $file );

		if ( empty( $parsed['has_icons'] ) ) {
			continue; // Not a valid icon manifest.
		}

		$meta = $parsed['meta'];

		// Anchored: use locked filename + prefix; display fields come from the JSON.
		if ( isset( $anchored[ $slug ] ) ) {
			list( $manifest_file, $class_prefix ) = $anchored[ $slug ];
		} else {
			$manifest_file    = $filename;
			$class_prefix_raw = ( ! empty( $meta['class_prefix'] ) && is_string( $meta['class_prefix'] ) )
				? $meta['class_prefix']
				: $slug . '-';
			$class_prefix     = preg_replace( '/[^a-z0-9\-_]/i', '', $class_prefix_raw );
		}

		// Label: manifest field → name field → slug.
		if ( ! empty( $meta['label'] ) && is_string( $meta['label'] ) ) {
			$label = $meta['label'];
		} elseif ( ! empty( $meta['name'] ) && is_string( $meta['name'] ) ) {
			$label = ucwords( str_replace( array( '-', '_' ), ' ', $meta['name'] ) );
		} else {
			$label = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
		}

		// Style: manifest field → slug heuristic fallback.
		if ( ! empty( $meta['style'] ) && in_array( $meta['style'], array( 'outline', 'filled' ), true ) ) {
			$style = $meta['style'];
		} elseif ( false !== strpos( $slug, 'lucide' ) ) {
			$style = 'outline';
		} elseif ( false !== strpos( $slug, 'fontawesome' ) || false !== strpos( $slug, '-fa' ) ) {
			$style = 'filled';
		} else {
			$style = '';
		}

		// Label icon: eicon-* tokens only.
		$label_icon = '';
		if ( ! empty( $meta['label_icon'] ) && is_string( $meta['label_icon'] )
			&& preg_match( '/^eicon-[a-z0-9\-]+$/', $meta['label_icon'] ) ) {
			$label_icon = $meta['label_icon'];
		}

		$found[ $slug ] = array(
			'label'         => $label,
			'label_icon'    => $label_icon,
			'manifest_file' => $manifest_file,
			'class_prefix'  => $class_prefix,
			'style'         => $style,
		);
	}

	$definitions = $found;
	return $definitions;
}

/**
 * Read the JSON header of a manifest file without decoding the full icon set.
 *
 * Reads the file in 512-byte chunks until the "icons" key is found (or 8 KB
 * is consumed), then parses just the header bytes as JSON.  This avoids
 * loading several MB of SVG data when only manifest metadata is needed.
 *
 * @param string $path Absolute path to a manifest JSON file.
 * @return array{has_icons: bool, meta: array}
 *   'has_icons' — true when the file contains an "icons" key.
 *   'meta'      — decoded top-level fields from the header (sans icons).
 */
function spectre_icons_read_manifest_header( $path ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	$fh = fopen( $path, 'rb' );

	if ( ! $fh ) {
		return array( 'has_icons' => false, 'meta' => array() );
	}

	$buffer    = '';
	$has_icons = false;
	$max_bytes = 8192;

	while ( ! feof( $fh ) && strlen( $buffer ) < $max_bytes ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		$chunk = fread( $fh, 512 );
		if ( false === $chunk ) {
			break;
		}
		$buffer .= $chunk;
		if ( false !== strpos( $buffer, '"icons"' ) ) {
			$has_icons = true;
			break;
		}
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	fclose( $fh );

	if ( ! $has_icons ) {
		return array( 'has_icons' => false, 'meta' => array() );
	}

	// Truncate at the "icons" key so the remainder forms valid JSON.
	$truncated = preg_replace( '/,?\s*"icons"\s*:[\s\S]*$/u', "\n}", $buffer );

	if ( null === $truncated || '' === trim( $truncated ) ) {
		return array( 'has_icons' => true, 'meta' => array() );
	}

	$meta = json_decode( $truncated, true );

	return array(
		'has_icons' => true,
		'meta'      => is_array( $meta ) ? $meta : array(),
	);
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
