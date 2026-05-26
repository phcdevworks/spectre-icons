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
 * Stores icons in wp-content/uploads/spectre-icons/manifest.json and injects
 * the library into the global definitions via the spectre_icons_library_definitions
 * filter, making it available to all builder adapters automatically.
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
	 * Absolute path to the user icon manifest file.
	 *
	 * @return string
	 */
	public static function get_manifest_path() {
		return trailingslashit( self::get_upload_dir() ) . 'manifest.json';
	}

	/**
	 * Web URL to the user icon manifest file (consumed by editor JS).
	 *
	 * @return string
	 */
	public static function get_manifest_url() {
		return trailingslashit( self::get_upload_url() ) . 'manifest.json';
	}

	/**
	 * Create the upload directory and write a directory-silence index if needed.
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
	 * @return array<string,string> Slug => sanitized SVG string.
	 */
	public static function get_icons() {
		$path = self::get_manifest_path();

		if ( ! is_file( $path ) ) {
			return array();
		}

		$fs = self::filesystem();
		if ( ! $fs ) {
			return array();
		}

		$raw = $fs->get_contents( $path );

		if ( false === $raw || '' === trim( $raw ) ) {
			return array();
		}

		$data = json_decode( $raw, true );

		if ( ! is_array( $data ) || empty( $data['icons'] ) || ! is_array( $data['icons'] ) ) {
			return array();
		}

		return $data['icons'];
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
	 * Returns PHP_INT_MAX (unlimited) by default. A pro extension can hook
	 * spectre_icons_user_library_limit and return a lower value to enforce a cap.
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
	 * @param string               $original_filename Uploaded filename (e.g. 'Arrow Right.svg').
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
	 * @param string $sanitized_svg    Sanitized SVG string (already passed through the sanitizer).
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

		return self::write_manifest( $icons );
	}

	/**
	 * Write the icon manifest to disk.
	 *
	 * @param array<string,string> $icons Slug => SVG map.
	 * @return true|\WP_Error
	 */
	public static function write_manifest( array $icons ) {
		if ( ! self::ensure_dirs() ) {
			return new WP_Error(
				'spectre_icons_dir_error',
				__( 'Could not create upload directory.', 'spectre-icons' )
			);
		}

		$data = array( 'icons' => $icons );
		$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

		if ( false === $json ) {
			return new WP_Error(
				'spectre_icons_encode_error',
				__( 'Failed to encode icon manifest.', 'spectre-icons' )
			);
		}

		$fs = self::filesystem();
		if ( ! $fs ) {
			return new WP_Error(
				'spectre_icons_fs_error',
				__( 'WordPress filesystem is unavailable.', 'spectre-icons' )
			);
		}

		$written = $fs->put_contents( self::get_manifest_path(), $json, FS_CHMOD_FILE );

		if ( ! $written ) {
			return new WP_Error(
				'spectre_icons_write_error',
				__( 'Failed to write icon manifest.', 'spectre-icons' )
			);
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
			'manifest_url'  => self::get_manifest_url(),
			'class_prefix'  => 'spectre-user-',
			'style'         => '',
		);
	}

	/**
	 * Inject the user library into the global library definitions.
	 *
	 * Hooked to spectre_icons_library_definitions. Skipped when no icons exist
	 * so the library does not appear in builder pickers before first upload.
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
}
