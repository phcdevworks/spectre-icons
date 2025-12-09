<?php
/**
 * Generates JSON manifests for Spectre icon packs.
 *
 * Usage: php bin/generate-icon-manifests.php
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/includes/class-spectre-icons-svg-sanitizer.php';

$root_path       = dirname( __DIR__ );
$iconpacks_path  = $root_path . '/assets/iconpacks';
$manifests_path  = $root_path . '/assets/manifests';
$manifest_prefix = 'spectre-';

if ( ! is_dir( $iconpacks_path ) ) {
	fwrite( STDERR, "Iconpacks directory not found: {$iconpacks_path}\n" );
	exit( 1 );
}

if ( ! is_dir( $manifests_path ) && ! mkdir( $manifests_path, 0755, true ) && ! is_dir( $manifests_path ) ) {
	fwrite( STDERR, "Unable to create manifests directory: {$manifests_path}\n" );
	exit( 1 );
}

$iconpack_dirs = glob( $iconpacks_path . '/*', GLOB_ONLYDIR );

if ( empty( $iconpack_dirs ) ) {
	fwrite( STDERR, "No icon packs detected in {$iconpacks_path}\n" );
	exit( 0 );
}

$total_icons = 0;

foreach ( $iconpack_dirs as $pack_dir ) {
	$pack_slug = basename( $pack_dir );
	$icons = generate_icons_from_directory( $pack_dir );

	if ( empty( $icons ) ) {
		printf( "Skipping %s (no SVG files found)\n", $pack_slug );
		continue;
	}

	$manifest = [
		'name'         => $pack_slug,
		'generated_at' => gmdate( 'c' ),
		'icon_count'   => count( $icons ),
		'icons'        => $icons,
	];

	$manifest_name = $manifest_prefix . $pack_slug . '.json';
	$manifest_file = $manifests_path . '/' . $manifest_name;

	file_put_contents(
		$manifest_file,
		json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);

	printf(
		"Wrote %d icons to %s\n",
		count( $icons ),
		str_replace( $root_path . '/', '', $manifest_file )
	);

	$total_icons += count( $icons );
}

printf( "Completed. Total icons processed: %d\n", $total_icons );

/**
 * Minimal replacement for sanitize_title to avoid loading WordPress in CLI context.
 *
 * @param string $title Raw title.
 *
 * @return string
 */
function sanitize_title( string $title ): string {
	$title = strtolower( $title );
	$title = preg_replace( '/[^a-z0-9\-]+/', '-', $title );

	return trim( $title, '-' );
}

/**
 * Recursively parse SVG files within a directory.
 *
 * @param string $directory Path to the icon pack directory.
 *
 * @return array
 */
function generate_icons_from_directory( string $directory ): array {
	$icons = [];
	$files = collect_svg_files( $directory );

	foreach ( $files as $file_path ) {
		$relative_path = trim(
			str_replace( $directory, '', $file_path ),
			DIRECTORY_SEPARATOR
		);

		$relative_path = str_replace( DIRECTORY_SEPARATOR, '-', $relative_path );
		$icon_slug     = sanitize_title( preg_replace( '/\.svg$/i', '', $relative_path ) );
		$svg           = file_get_contents( $file_path );

		if ( false === $svg ) {
			printf( "Failed to read %s\n", $file_path );
			continue;
		}

		$svg = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		if ( '' === $svg ) {
			printf( "Skipping %s (sanitization removed markup)\n", $file_path );
			continue;
		}

		$icons[ $icon_slug ] = $svg;
	}

	return $icons;
}

/**
 * Recursively collect SVG file paths in a directory.
 *
 * @param string $directory Directory to scan.
 *
 * @return array
 */
function collect_svg_files( string $directory ): array {
	$files    = [];
	$entries  = scandir( $directory );

	if ( false === $entries ) {
		return $files;
	}

	foreach ( $entries as $entry ) {
		if ( '.' === $entry || '..' === $entry ) {
			continue;
		}

		$path = $directory . DIRECTORY_SEPARATOR . $entry;

		if ( is_dir( $path ) ) {
			$files = array_merge( $files, collect_svg_files( $path ) );
			continue;
		}

		if ( 'svg' !== strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
			continue;
		}

		$files[] = $path;
	}

	return $files;
}
