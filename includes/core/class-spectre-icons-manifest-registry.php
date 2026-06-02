<?php
/**
 * Core manifest registry for Spectre Icons.
 *
 * Stores registered icon libraries and loads/caches their JSON manifests.
 * Builder-agnostic — no Elementor or page-builder dependencies.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Spectre_Icons_Manifest_Registry' ) ) :

	/**
	 * Static registry for icon library manifests.
	 *
	 * Responsibilities:
	 * - Register manifests per library (path + options).
	 * - Load and cache manifest contents.
	 * - Return icon slugs and icon data for other services.
	 */
	final class Spectre_Icons_Manifest_Registry {

		/**
		 * Registered libraries.
		 *
		 * Shape:
		 * [
		 *   'library-slug' => [
		 *     'manifest' => '/path/to/manifest.json',
		 *     'prefix'   => 'spectre-lucide-',
		 *     'options'  => [ ... ],
		 *   ],
		 * ]
		 *
		 * @var array<string, array>
		 */
		private static $libraries = array();

		/**
		 * Cached decoded icon manifests.
		 *
		 * Shape:
		 * [
		 *   'library-slug' => [
		 *     'icon-slug' => [ manifest-entry-array ],
		 *   ],
		 * ]
		 *
		 * @var array<string, array>
		 */
		private static $icons_cache = array();

		/**
		 * Register a manifest for a given library.
		 *
		 * @param string $library_slug  Library slug (e.g. 'spectre-lucide').
		 * @param string $manifest_path Absolute path to the JSON manifest file.
		 * @param array  $args          Optional extra args. Supported keys:
		 *                              - prefix  (string) CSS class prefix.
		 *                              - options (array)  any additional data.
		 *
		 * @return void
		 */
		public static function register_manifest( $library_slug, $manifest_path, array $args = array() ) {
			if ( ! is_scalar( $library_slug ) ) {
				spectre_icons_log_debug( sprintf( 'register_manifest called with non-scalar library slug: %s.', gettype( $library_slug ) ), 'Manifest Registry' );
				return;
			}

			$slug = sanitize_key( (string) $library_slug );

			if ( '' === $slug ) {
				$msg_slug = is_scalar( $library_slug ) ? (string) $library_slug : gettype( $library_slug );
				spectre_icons_log_debug( sprintf( 'register_manifest called with invalid library slug "%s".', $msg_slug ), 'Manifest Registry' );
				return;
			}

			if ( ! is_string( $manifest_path ) || '' === $manifest_path ) {
				spectre_icons_log_debug( sprintf( 'Library "%s" missing manifest path.', $slug ), 'Manifest Registry' );
				return;
			}

			// We intentionally do NOT require file_exists() here, because the path
			// may point into a packaged asset that is not yet available in some
			// contexts (e.g. during build-time tools). We will check on load.
			$defaults = array(
				'prefix'  => '',
				'options' => array(),
			);

			$args = wp_parse_args( $args, $defaults );

			self::$libraries[ $slug ] = array(
				'manifest' => $manifest_path,
				'prefix'   => is_string( $args['prefix'] ) ? $args['prefix'] : '',
				'options'  => is_array( $args['options'] ) ? $args['options'] : array(),
			);

			// Clear cache for this library in case we re-register.
			unset( self::$icons_cache[ $slug ] );
		}

		/**
		 * Get all icon slugs for a given library.
		 *
		 * @param string $library_slug Library slug.
		 * @return array<string> Icon slugs (may be empty).
		 */
		public static function get_icon_slugs( $library_slug ) {
			if ( ! is_scalar( $library_slug ) ) {
				return array();
			}

			$slug = sanitize_key( (string) $library_slug );

			if ( '' === $slug || ! isset( self::$libraries[ $slug ] ) ) {
				$msg_slug = is_scalar( $library_slug ) ? (string) $library_slug : gettype( $library_slug );
				spectre_icons_log_debug( sprintf( 'get_icon_slugs called for unknown library "%s".', $msg_slug ), 'Manifest Registry' );
				return array();
			}

			$icons = self::get_icons( $slug );

			if ( empty( $icons ) || ! is_array( $icons ) ) {
				return array();
			}

			return array_keys( $icons );
		}

		/**
		 * Whether a library slug has been registered.
		 *
		 * @param string $slug Sanitized library slug.
		 * @return bool
		 */
		public static function has_library( $slug ) {
			return isset( self::$libraries[ $slug ] );
		}

		/**
		 * Return the raw library config array for a registered slug, or null.
		 *
		 * @param string $slug Sanitized library slug.
		 * @return array|null
		 */
		public static function get_library_config( $slug ) {
			return isset( self::$libraries[ $slug ] ) ? self::$libraries[ $slug ] : null;
		}

		/**
		 * Return the full icon map for a library (public wrapper around the cache).
		 *
		 * @param string $slug Sanitized library slug.
		 * @return array<string, array> Map of icon slug => manifest entry.
		 */
		public static function get_icons_for( $slug ) {
			return self::get_icons( $slug );
		}

		/**
		 * Load and cache the icon manifest for the given library.
		 *
		 * @param string $library_slug Sanitized library slug.
		 * @return array<string, array> Map of icon slug => manifest entry.
		 */
		private static function get_icons( $library_slug ) {
			if ( isset( self::$icons_cache[ $library_slug ] ) ) {
				return self::$icons_cache[ $library_slug ];
			}

			if ( ! isset( self::$libraries[ $library_slug ] ) ) {
				return array();
			}

			$library       = self::$libraries[ $library_slug ];
			$manifest_path = isset( $library['manifest'] ) ? (string) $library['manifest'] : '';

			if ( '' === $manifest_path || ! file_exists( $manifest_path ) ) {
				spectre_icons_log_debug( sprintf( 'Manifest file missing for library "%s": %s', $library_slug, $manifest_path ), 'Manifest Registry' );
				self::$icons_cache[ $library_slug ] = array();
				return array();
			}

			$contents = file_get_contents( $manifest_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( false === $contents ) {
				spectre_icons_log_debug( sprintf( 'Could not read manifest file for library "%s".', $library_slug ), 'Manifest Registry' );
				self::$icons_cache[ $library_slug ] = array();
				return array();
			}

			try {
				$data = json_decode( $contents, true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $e ) {
				spectre_icons_log_debug(
					sprintf(
						'JSON decode error in manifest for library "%1$s": %2$s',
						$library_slug,
						$e->getMessage()
					),
					'Manifest Registry'
				);
				self::$icons_cache[ $library_slug ] = array();
				return array();
			}

			if ( ! is_array( $data ) || empty( $data ) ) {
				spectre_icons_log_debug( sprintf( 'Manifest for library "%s" did not decode to an array.', $library_slug ), 'Manifest Registry' );
				self::$icons_cache[ $library_slug ] = array();
				return array();
			}

			/**
			 * Supported manifest structures:
			 * - Top-level map:    [ 'arrow-right' => [ ... ], ... ]
			 * - Top-level wrapper: [ 'icons' => [ 'arrow-right' => '<svg...>', ... ] ]
			 * - Indexed list:     [ [ 'slug' => 'arrow-right', ... ], ... ]
			 */
			if ( isset( $data['icons'] ) ) {
				if ( is_array( $data['icons'] ) ) {
					$data = $data['icons'];
				} else {
					spectre_icons_log_debug( sprintf( 'Manifest for library "%s" has non-array "icons" key.', $library_slug ), 'Manifest Registry' );
					self::$icons_cache[ $library_slug ] = array();
					return array();
				}
			}

			if ( empty( $data ) ) {
				self::$icons_cache[ $library_slug ] = array();
				return array();
			}

			$icons = array();

			// Associative array keyed by slug.
			$is_assoc = array_keys( $data ) !== range( 0, count( $data ) - 1 );

			if ( $is_assoc ) {
				foreach ( $data as $slug => $icon_entry ) {
					$slug = sanitize_key( $slug );
					if ( '' === $slug ) {
						continue;
					}
					if ( is_string( $icon_entry ) && '' !== trim( $icon_entry ) ) {
						$trimmed = trim( $icon_entry );
						// Format 4: filename reference — .svg extension with no SVG markup.
						if ( '.svg' === substr( strtolower( $trimmed ), -4 ) && false === strpos( $trimmed, '<' ) ) {
							$svg_file = trailingslashit( dirname( $manifest_path ) ) . basename( $trimmed );
							if ( '' !== $manifest_path && file_exists( $svg_file ) ) {
								// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
								$svg_content = file_get_contents( $svg_file );
								if ( false !== $svg_content && '' !== trim( $svg_content ) ) {
									$icons[ $slug ] = array( 'svg' => $svg_content );
									continue;
								}
							}
							continue; // File missing or unreadable — skip icon.
						}
						$icons[ $slug ] = array( 'svg' => $trimmed );
						continue;
					}
					if ( is_array( $icon_entry ) ) {
						$has_svg  = isset( $icon_entry['svg'] ) && is_string( $icon_entry['svg'] ) && '' !== trim( $icon_entry['svg'] );
						$has_body = isset( $icon_entry['body'] ) && is_string( $icon_entry['body'] ) && '' !== trim( $icon_entry['body'] );
						if ( $has_svg || $has_body ) {
							$icons[ $slug ] = $icon_entry;
						}
					}
				}
			} else {
				foreach ( $data as $icon_entry ) {
					if ( ! is_array( $icon_entry ) || empty( $icon_entry['slug'] ) ) {
						continue;
					}
					$slug = sanitize_key( $icon_entry['slug'] );
					if ( '' === $slug ) {
						continue;
					}

					$has_svg  = isset( $icon_entry['svg'] ) && is_string( $icon_entry['svg'] ) && '' !== trim( $icon_entry['svg'] );
					$has_body = isset( $icon_entry['body'] ) && is_string( $icon_entry['body'] ) && '' !== trim( $icon_entry['body'] );

					if ( $has_svg || $has_body ) {
						$icons[ $slug ] = $icon_entry;
					}
				}
			}

			if ( empty( $icons ) ) {
				spectre_icons_log_debug( sprintf( 'Manifest for library "%s" contained no valid icons.', $library_slug ), 'Manifest Registry' );
			}

			self::$icons_cache[ $library_slug ] = $icons;

			return $icons;
		}
	}

endif;
