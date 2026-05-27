<?php
/**
 * User icon library manager for Spectre Icons.
 *
 * Handles upload, storage, and manifest management for user-supplied SVG icons.
 * Builder-agnostic — no page-builder dependencies.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the user-uploaded custom icon library.
 *
 * Storage layout (wp-content/uploads/spectre-icons/):
 *   manifest.json          — index manifest: slug → "slug.svg" filename references
 *   manifest-compiled.json — compiled manifest: slug → full SVG string (consumed by editor JS)
 *   <slug>.svg             — individual sanitized SVG files (one per icon)
 *
 * Serialization-anchored: slug = spectre-user, prefix = spectre-user-
 * These must never change — icon classes are saved in post meta.
 */
final class Spectre_Icons_User_Library_Manager {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'spectre_icons_library_definitions', array( __CLASS__, 'inject_definition' ) );
		add_action( 'admin_init', array( __CLASS__, 'migrate_if_needed' ) );
	}

	/**
	 * Return the initialized WP Filesystem instance.
	 *
	 * @return \WP_Filesystem_Base|null
	 */
	private static function filesystem() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			$fs_file = ABSPATH . 'wp-admin/includes/file.php';
			if ( file_exists( $fs_file ) ) {
				require_once $fs_file;
			}
			if ( function_exists( 'WP_Filesystem' ) ) {
				WP_Filesystem();
			}
		}
		return ( $wp_filesystem instanceof WP_Filesystem_Base ) ? $wp_filesystem : null;
	}

	/**
	 * Absolute filesystem path to the plugin's upload directory.
	 *
	 * @return string
	 */
	public static function get_upload_dir() {
		$upload = wp_upload_dir();
		return trailingslashit( $upload['basedir'] ) . 'spectre-icons';
	}

	/**
	 * Web-accessible URL for the plugin's upload directory.
	 *
	 * @return string
	 */
	public static function get_upload_url() {
		$upload = wp_upload_dir();
		return trailingslashit( $upload['baseurl'] ) . 'spectre-icons';
	}

	/**
	 * Absolute path to the user icon index manifest file (slug → filename map).
	 *
	 * @return string
	 */
	public static function get_manifest_path() {
		return trailingslashit( self::get_upload_dir() ) . 'manifest.json';
	}

	/**
	 * Web URL to the user icon index manifest file.
	 *
	 * @return string
	 */
	public static function get_manifest_url() {
		return trailingslashit( self::get_upload_url() ) . 'manifest.json';
	}

	/**
	 * Absolute path to the compiled manifest (slug → SVG string, consumed by editor JS).
	 *
	 * @return string
	 */
	public static function get_compiled_manifest_path() {
		return trailingslashit( self::get_upload_dir() ) . 'manifest-compiled.json';
	}

	/**
	 * Web URL to the compiled manifest (consumed by editor JS).
	 *
	 * @return string
	 */
	public static function get_compiled_manifest_url() {
		return trailingslashit( self::get_upload_url() ) . 'manifest-compiled.json';
	}

	/**
	 * Absolute path to the individual SVG file for a given icon slug.
	 *
	 * @param string $slug Icon slug.
	 * @return string
	 */
	public static function get_svg_file_path( $slug ) {
		return trailingslashit( self::get_upload_dir() ) . sanitize_key( $slug ) . '.svg';
	}

	/**
	 * Create the upload directory and write security files if needed.
	 *
	 * @return bool True on success, false if the directory could not be created.
	 */
	public static function ensure_dirs() {
		$dir = self::get_upload_dir();

		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}

		$fs = self::filesystem();

		$silence = trailingslashit( $dir ) . 'index.php';
		if ( $fs && ! $fs->exists( $silence ) ) {
			$fs->put_contents( $silence, "<?php\n// Silence is golden.\n", FS_CHMOD_FILE );
		}

		$htaccess = trailingslashit( $dir ) . '.htaccess';
		if ( $fs && ! $fs->exists( $htaccess ) ) {
			$fs->put_contents( $htaccess, "Options -Indexes\n", FS_CHMOD_FILE );
		}

		return true;
	}

	/**
	 * Read all user icons from the manifest.
	 *
	 * Returns slug => SVG string map for all callers (admin page, registry, etc.).
	 * When the manifest uses the new file-based format (slug → "slug.svg"), the
	 * individual SVG files are resolved automatically.
	 * When the manifest uses the legacy inline format (slug → SVG string), the
	 * inline SVG is returned directly (pre-migration fallback).
	 *
	 * Uses native PHP for reads so this works on the frontend before
	 * WP_Filesystem has been initialised.
	 *
	 * @return array<string,string> Slug => sanitized SVG string.
	 */
	public static function get_icons() {
		$path = self::get_manifest_path();

		if ( ! is_file( $path ) ) {
			return array();
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw = file_get_contents( $path );

		if ( false === $raw || '' === trim( $raw ) ) {
			return array();
		}

		$data = json_decode( $raw, true );

		if ( ! is_array( $data ) || empty( $data['icons'] ) || ! is_array( $data['icons'] ) ) {
			return array();
		}

		$icons      = array();
		$upload_dir = trailingslashit( self::get_upload_dir() );

		foreach ( $data['icons'] as $slug => $value ) {
			$slug = sanitize_key( $slug );
			if ( '' === $slug || ! is_string( $value ) ) {
				continue;
			}

			$trimmed = trim( $value );

			// File-based format: value is a filename like "slug.svg".
			if ( '.svg' === substr( strtolower( $trimmed ), -4 ) && false === strpos( $trimmed, '<' ) ) {
				$file_path = $upload_dir . basename( $trimmed );
				if ( file_exists( $file_path ) ) {
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$svg_content = file_get_contents( $file_path );
					if ( false !== $svg_content && '' !== trim( $svg_content ) ) {
						$icons[ $slug ] = $svg_content;
					}
				}
				continue;
			}

			// Legacy inline SVG format (pre-1.5.0 or migration fallback).
			if ( '' !== $trimmed ) {
				$icons[ $slug ] = $trimmed;
			}
		}

		return $icons;
	}

	/**
	 * Count of currently uploaded icons.
	 *
	 * @return int
	 */
	public static function get_icon_count() {
		return count( self::get_icons() );
	}

	/**
	 * Maximum number of icons allowed.
	 *
	 * @return int
	 */
	public static function get_limit() {
		return (int) apply_filters( 'spectre_icons_user_library_limit', PHP_INT_MAX );
	}

	/**
	 * Whether the icon count has reached the configured limit.
	 *
	 * @return bool
	 */
	public static function is_at_limit() {
		return self::get_icon_count() >= self::get_limit();
	}

	/**
	 * Derive a unique icon slug from an uploaded filename.
	 *
	 * @param string               $original_filename Uploaded filename.
	 * @param array<string,string> $existing_icons    Current icon map (slug => svg).
	 * @return string
	 */
	public static function derive_slug( $original_filename, array $existing_icons ) {
		$base = sanitize_key(
			preg_replace( '/\.svg$/i', '', sanitize_file_name( $original_filename ) )
		);

		if ( '' === $base ) {
			$base = 'icon';
		}

		$slug   = $base;
		$suffix = 2;

		while ( isset( $existing_icons[ $slug ] ) ) {
			$slug = $base . '-' . $suffix;
			++$suffix;
		}

		return $slug;
	}

	/**
	 * Add a sanitized SVG icon to the library.
	 *
	 * @param string $sanitized_svg    Sanitized SVG string.
	 * @param string $original_filename Original upload filename, used to derive the slug.
	 * @return string|\WP_Error Slug on success, WP_Error on failure.
	 */
	public static function add_icon( $sanitized_svg, $original_filename ) {
		$icons = self::get_icons();

		if ( self::is_at_limit() ) {
			return new WP_Error(
				'spectre_icons_limit_reached',
				sprintf(
					/* translators: %d: maximum number of icons allowed */
					__( 'You have reached the %d icon limit. Remove an existing icon to upload a new one.', 'spectre-icons' ),
					self::get_limit()
				)
			);
		}

		$slug           = self::derive_slug( $original_filename, $icons );
		$icons[ $slug ] = $sanitized_svg;

		$result = self::write_manifest( $icons );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $slug;
	}

	/**
	 * Delete an icon from the library by slug.
	 *
	 * @param string $slug Icon slug.
	 * @return true|\WP_Error
	 */
	public static function delete_icon( $slug ) {
		$slug  = sanitize_key( $slug );
		$icons = self::get_icons();

		if ( ! isset( $icons[ $slug ] ) ) {
			return new WP_Error(
				'spectre_icons_not_found',
				__( 'Icon not found.', 'spectre-icons' )
			);
		}

		unset( $icons[ $slug ] );

		$result = self::write_manifest( $icons );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Delete the individual SVG file (best-effort; does not fail the operation).
		$fs       = self::filesystem();
		$svg_file = self::get_svg_file_path( $slug );
		if ( $fs && $fs->exists( $svg_file ) ) {
			$fs->delete( $svg_file );
		}

		return true;
	}

	/**
	 * Write the icon manifest and individual SVG files to disk.
	 *
	 * Writes three artifacts:
	 *   1. Individual .svg files for each icon.
	 *   2. manifest.json — index manifest (slug → filename) for PHP registry.
	 *   3. manifest-compiled.json — compiled manifest (slug → SVG string) for editor JS.
	 *
	 * @param array<string,string> $icons Slug => SVG string map.
	 * @return true|\WP_Error
	 */
	public static function write_manifest( array $icons ) {
		if ( ! self::ensure_dirs() ) {
			return new WP_Error(
				'spectre_icons_dir_error',
				__( 'Could not create upload directory.', 'spectre-icons' )
			);
		}

		$fs = self::filesystem();
		if ( ! $fs ) {
			return new WP_Error(
				'spectre_icons_fs_error',
				__( 'WordPress filesystem is unavailable.', 'spectre-icons' )
			);
		}

		// Write individual SVG files and build the index map.
		$index_icons = array();
		foreach ( $icons as $slug => $svg ) {
			$slug = sanitize_key( $slug );
			if ( '' === $slug || '' === trim( $svg ) ) {
				continue;
			}

			$filename = $slug . '.svg';
			$svg_path = trailingslashit( self::get_upload_dir() ) . $filename;
			$written  = $fs->put_contents( $svg_path, $svg, FS_CHMOD_FILE );

			if ( ! $written ) {
				return new WP_Error(
					'spectre_icons_write_error',
					sprintf(
						/* translators: %s: icon slug */
						__( 'Failed to write SVG file for icon "%s".', 'spectre-icons' ),
						$slug
					)
				);
			}

			$index_icons[ $slug ] = $filename;
		}

		// Write index manifest (slug → filename).
		$index_json = wp_json_encode(
			array( 'icons' => $index_icons ),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);

		if ( false === $index_json ) {
			return new WP_Error(
				'spectre_icons_encode_error',
				__( 'Failed to encode icon manifest.', 'spectre-icons' )
			);
		}

		if ( ! $fs->put_contents( self::get_manifest_path(), $index_json, FS_CHMOD_FILE ) ) {
			return new WP_Error(
				'spectre_icons_write_error',
				__( 'Failed to write icon manifest.', 'spectre-icons' )
			);
		}

		// Write compiled manifest (slug → SVG string) for editor JS.
		$compiled_json = wp_json_encode(
			array( 'icons' => $icons ),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);

		if ( false !== $compiled_json ) {
			$fs->put_contents( self::get_compiled_manifest_path(), $compiled_json, FS_CHMOD_FILE );
		}

		return true;
	}

	/**
	 * Build the library definition array used by the filter and builder adapters.
	 *
	 * @return array
	 */
	public static function library_definition() {
		return array(
			'label'         => __( 'My Icons', 'spectre-icons' ),
			'label_icon'    => 'eicon-upload',
			'manifest_file' => null,
			'manifest_path' => self::get_manifest_path(),
			'manifest_url'  => self::get_compiled_manifest_url(),
			'class_prefix'  => 'spectre-user-',
			'style'         => '',
		);
	}

	/**
	 * Inject the user library into the global library definitions.
	 *
	 * @param array $definitions Existing library definitions.
	 * @return array
	 */
	public static function inject_definition( $definitions ) {
		if ( ! is_file( self::get_manifest_path() ) ) {
			return $definitions;
		}

		if ( empty( self::get_icons() ) ) {
			return $definitions;
		}

		$definitions['spectre-user'] = self::library_definition();

		return $definitions;
	}

	/**
	 * Migrate inline SVG manifest (1.4.x) to file-based storage (1.5.0).
	 *
	 * Runs once on admin_init. Reads the existing manifest.json; if any icon
	 * stores its SVG inline, writes individual .svg files, rewrites manifest.json
	 * as an index, and writes manifest-compiled.json for the editor JS.
	 *
	 * The migration is conservative: if a file write fails for a given icon,
	 * that icon's entry remains inline in the manifest (still valid via the
	 * registry's format-3 fallback). The compiled manifest is always written
	 * with whatever icons were successfully resolved.
	 *
	 * @return void
	 */
	public static function migrate_if_needed() {
		if ( get_option( 'spectre_icons_user_library_migrated_v150' ) ) {
			return;
		}

		$path = self::get_manifest_path();

		if ( ! is_file( $path ) ) {
			update_option( 'spectre_icons_user_library_migrated_v150', true, false );
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw = file_get_contents( $path );

		if ( false === $raw || '' === trim( $raw ) ) {
			update_option( 'spectre_icons_user_library_migrated_v150', true, false );
			return;
		}

		$data = json_decode( $raw, true );

		if ( ! is_array( $data ) || empty( $data['icons'] ) || ! is_array( $data['icons'] ) ) {
			update_option( 'spectre_icons_user_library_migrated_v150', true, false );
			return;
		}

		// Check if any icon is stored as inline SVG (contains '<').
		$needs_migration = false;
		foreach ( $data['icons'] as $value ) {
			if ( is_string( $value ) && false !== strpos( $value, '<' ) ) {
				$needs_migration = true;
				break;
			}
		}

		if ( ! $needs_migration ) {
			update_option( 'spectre_icons_user_library_migrated_v150', true, false );
			return;
		}

		if ( ! self::ensure_dirs() ) {
			return;
		}

		$fs = self::filesystem();
		if ( ! $fs ) {
			return;
		}

		$upload_dir  = trailingslashit( self::get_upload_dir() );
		$index_icons = array();
		$compiled    = array();

		foreach ( $data['icons'] as $slug => $value ) {
			$slug = sanitize_key( $slug );
			if ( '' === $slug || ! is_string( $value ) || '' === trim( $value ) ) {
				continue;
			}

			$trimmed = trim( $value );

			if ( false !== strpos( $trimmed, '<' ) ) {
				// Inline SVG — write to individual file.
				$filename = $slug . '.svg';
				$svg_path = $upload_dir . $filename;
				$written  = $fs->put_contents( $svg_path, $trimmed, FS_CHMOD_FILE );

				if ( $written ) {
					$index_icons[ $slug ] = $filename;
					$compiled[ $slug ]    = $trimmed;
				} else {
					// Write failed — keep inline in index as fallback (still readable).
					$index_icons[ $slug ] = $trimmed;
					$compiled[ $slug ]    = $trimmed;
				}
			} elseif ( '.svg' === substr( strtolower( $trimmed ), -4 ) ) {
				// Already a filename reference — carry through.
				$index_icons[ $slug ] = $trimmed;
				$svg_file             = $upload_dir . basename( $trimmed );
				if ( file_exists( $svg_file ) ) {
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$svg_content = file_get_contents( $svg_file );
					if ( false !== $svg_content ) {
						$compiled[ $slug ] = $svg_content;
					}
				}
			}
		}

		// Rewrite index manifest.
		$index_json = wp_json_encode(
			array( 'icons' => $index_icons ),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		if ( false !== $index_json ) {
			$fs->put_contents( self::get_manifest_path(), $index_json, FS_CHMOD_FILE );
		}

		// Write compiled manifest for editor JS.
		$compiled_json = wp_json_encode(
			array( 'icons' => $compiled ),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		if ( false !== $compiled_json ) {
			$fs->put_contents( self::get_compiled_manifest_path(), $compiled_json, FS_CHMOD_FILE );
		}

		update_option( 'spectre_icons_user_library_migrated_v150', true, false );
	}
}
