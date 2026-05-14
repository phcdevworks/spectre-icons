<?php
/**
 * Core icon renderer for Spectre Icons.
 *
 * Renders a registered icon slug as inline SVG wrapped in an HTML element.
 * Builder-agnostic — depends only on Spectre_Icons_Manifest_Registry and
 * Spectre_Icons_SVG_Sanitizer.
 *
 * Builder integrations wire this as their render callback, optionally wrapping
 * it to normalise the builder's own icon-descriptor format first.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Spectre_Icons_Icon_Renderer' ) ) :

	/**
	 * Static service for rendering individual icons as inline SVG.
	 *
	 * Responsibilities:
	 * - Parse icon descriptors (array or string) into library + icon slug.
	 * - Fetch icon data from Spectre_Icons_Manifest_Registry.
	 * - Build and sanitize the SVG output.
	 * - Wrap the SVG in a configurable HTML element.
	 */
	final class Spectre_Icons_Icon_Renderer {

		/**
		 * Render a single icon as inline SVG wrapped in an HTML tag.
		 *
		 * Accepts the standard icon descriptor shape used by Elementor and
		 * normalised by other builder adapters:
		 *   [ 'library' => 'spectre-lucide', 'value' => 'spectre-lucide-arrow-right' ]
		 * or a plain slug string.
		 *
		 * @param array|string $icon       Icon descriptor or raw slug.
		 * @param array        $attributes Optional HTML attributes for the wrapper tag.
		 * @param string       $tag        HTML wrapper tag (span, i, or div).
		 *
		 * @return string Rendered HTML or empty string on failure.
		 */
		public static function render_icon( $icon, $attributes = array(), $tag = 'span' ) {
			if ( ! is_array( $icon ) && ! is_string( $icon ) ) {
				return '';
			}

			list( $library_slug, $icon_slug ) = self::extract_slug( $icon );

			if ( '' === $icon_slug ) {
				return '';
			}

			if ( '' === $library_slug || ! Spectre_Icons_Manifest_Registry::has_library( $library_slug ) ) {
				$msg_lib  = is_scalar( $library_slug ) ? (string) $library_slug : gettype( $library_slug );
				$msg_icon = is_scalar( $icon_slug ) ? (string) $icon_slug : gettype( $icon_slug );
				self::log_debug( sprintf( 'render_icon: unknown library "%s" for icon "%s".', $msg_lib, $msg_icon ) );
				return '';
			}

			$library = Spectre_Icons_Manifest_Registry::get_library_config( $library_slug );
			$icons   = Spectre_Icons_Manifest_Registry::get_icons_for( $library_slug );

			if ( empty( $icons ) || ! is_array( $icons ) ) {
				self::log_debug( sprintf( 'render_icon: no icons loaded for library "%s".', $library_slug ) );
				return '';
			}

			if ( ! isset( $icons[ $icon_slug ] ) ) {
				self::log_debug( sprintf( 'render_icon: icon "%s" not found in library "%s".', $icon_slug, $library_slug ) );
				return '';
			}

			$icon_data = $icons[ $icon_slug ];

			if ( ! is_array( $icon_data ) ) {
				self::log_debug( sprintf( 'render_icon: icon "%s" in library "%s" has invalid data structure.', $icon_slug, $library_slug ) );
				return '';
			}

			$attributes  = is_array( $attributes ) ? $attributes : array();
			$tag         = self::sanitize_tag_name( $tag );
			$attributes  = self::maybe_add_style_class( $attributes, $library_slug );
			$attributes  = self::prepare_attributes( $attributes, $icon_slug, $library );
			$attr_string = self::attributes_to_string( $attributes );

			$svg = self::build_svg_from_manifest_icon( $icon_data );

			if ( '' === $svg ) {
				self::log_debug( sprintf( 'render_icon: icon "%s" in library "%s" has empty SVG.', $icon_slug, $library_slug ) );
				return '';
			}

			return sprintf(
				'<%1$s%2$s>%3$s</%1$s>',
				$tag,
				$attr_string,
				$svg
			);
		}

		/**
		 * Add a style class based on the library's style option so CSS can
		 * target outline vs filled icons.
		 *
		 * @param array  $attributes   Wrapper attributes.
		 * @param string $library_slug Library slug.
		 * @return array
		 */
		private static function maybe_add_style_class( array $attributes, $library_slug ) {
			$style_class = '';
			$library     = Spectre_Icons_Manifest_Registry::get_library_config( $library_slug );
			$style       = ( $library && isset( $library['options']['style'] ) ) ? (string) $library['options']['style'] : '';

			if ( 'outline' === $style ) {
				$style_class = 'spectre-icon--style-outline';
			} elseif ( 'filled' === $style ) {
				$style_class = 'spectre-icon--style-filled';
			}

			// Fallback slug-based detection for backward compatibility.
			if ( '' === $style_class ) {
				if ( false !== strpos( $library_slug, 'lucide' ) ) {
					$style_class = 'spectre-icon--style-outline';
				} elseif ( false !== strpos( $library_slug, 'fontawesome' ) ) {
					$style_class = 'spectre-icon--style-filled';
				}
			}

			if ( '' === $style_class ) {
				return $attributes;
			}

			if ( isset( $attributes['class'] ) ) {
				if ( is_array( $attributes['class'] ) ) {
					$attributes['class'][] = $style_class;
					return $attributes;
				}
				$attributes['class'] = trim( (string) $attributes['class'] . ' ' . $style_class );
				return $attributes;
			}

			$attributes['class'] = $style_class;
			return $attributes;
		}

		/**
		 * Extract library slug and icon slug from an icon descriptor.
		 *
		 * Supported shapes:
		 * - [ 'library' => 'spectre-lucide', 'value' => 'spectre-lucide-arrow-right' ]
		 * - [ 'library' => 'spectre-lucide', 'value' => 'arrow-right' ]
		 * - [ 'library' => 'spectre-lucide', 'value' => 'spectre-lucide arrow-right' ]
		 * - 'arrow-right' (plain string — library slug will be empty)
		 *
		 * @param array|string $icon Icon descriptor or slug.
		 * @return array{string,string} [ library_slug, icon_slug ]
		 */
		private static function extract_slug( $icon ) {
			if ( ! is_array( $icon ) && ! is_string( $icon ) ) {
				return array( '', '' );
			}

			$library_slug = '';
			$icon_slug    = '';

			if ( is_array( $icon ) ) {
				if ( ! empty( $icon['library'] ) && is_scalar( $icon['library'] ) ) {
					$library_slug = sanitize_key( (string) $icon['library'] );
				}

				$value = '';
				if ( ! empty( $icon['value'] ) && is_scalar( $icon['value'] ) ) {
					$value = (string) $icon['value'];
				} elseif ( ! empty( $icon['icon'] ) && is_scalar( $icon['icon'] ) ) {
					// Fallback key used in some Elementor versions.
					$value = (string) $icon['icon'];
				}

				if ( '' !== trim( $value ) ) {
					$parts     = preg_split( '/\s+/', trim( $value ) );
					$slug      = end( $parts ); // Last token is the icon identifier.
					$icon_slug = sanitize_key( $slug );
				}
			} elseif ( is_string( $icon ) && '' !== trim( $icon ) ) {
				$parts = preg_split( '/\s+/', trim( $icon ) );
				if ( count( $parts ) > 1 ) {
					$library_slug = sanitize_key( $parts[0] );
					$icon_slug    = sanitize_key( end( $parts ) );
				} else {
					$icon_slug = sanitize_key( $parts[0] );
				}
			}

			// Strip library prefix if the value already included it.
			if ( '' !== $library_slug && '' !== $icon_slug ) {
				$library = Spectre_Icons_Manifest_Registry::get_library_config( $library_slug );
				if ( $library && isset( $library['prefix'] ) ) {
					$prefix = (string) $library['prefix'];
					if ( '' !== $prefix && 0 === strpos( $icon_slug, $prefix ) ) {
						$icon_slug = sanitize_key( substr( $icon_slug, strlen( $prefix ) ) );
					}
				}
			}

			return array( $library_slug, $icon_slug );
		}

		/**
		 * Prepare HTML attributes for the wrapper element.
		 *
		 * @param array  $attributes Input attributes (possibly including 'class').
		 * @param string $icon_slug  Icon slug.
		 * @param array  $library    Library config from the registry.
		 * @return array Sanitized attributes.
		 */
		private static function prepare_attributes( array $attributes, $icon_slug, array $library ) {
			$prepared = array();

			$base_class = $icon_slug;
			if ( ! empty( $library['prefix'] ) && is_string( $library['prefix'] ) ) {
				$base_class = $library['prefix'] . $icon_slug;
			}

			$current_class = '';
			if ( isset( $attributes['class'] ) ) {
				if ( is_array( $attributes['class'] ) ) {
					$scalar_classes = array_filter( $attributes['class'], 'is_scalar' );
					$current_class  = implode( ' ', $scalar_classes );
				} else {
					$current_class = (string) $attributes['class'];
				}
			}

			$class_attr = trim( $base_class . ' ' . $current_class );

			if ( '' !== $class_attr ) {
				$prepared['class'] = $class_attr;
			}

			foreach ( $attributes as $name => $value ) {
				if ( 'class' === $name ) {
					continue;
				}

				$sanitized_name = sanitize_key( (string) $name );
				if ( '' === $sanitized_name ) {
					continue;
				}

				// Block event handlers (e.g. onclick, onmouseover).
				if ( 0 === strpos( $sanitized_name, 'on' ) ) {
					continue;
				}

				$prepared[ $sanitized_name ] = $value;
			}

			return $prepared;
		}

		/**
		 * Convert an attribute array into an HTML attribute string.
		 *
		 * @param array $attributes Attribute name => value.
		 * @return string Leading space + attributes, or empty string.
		 */
		private static function attributes_to_string( array $attributes ) {
			if ( empty( $attributes ) ) {
				return '';
			}

			$parts = array();

			foreach ( $attributes as $name => $value ) {
				if ( ! is_scalar( $value ) ) {
					continue;
				}

				$name = esc_attr( (string) $name );
				if ( '' === $name ) {
					continue;
				}

				$value   = esc_attr( (string) $value );
				$parts[] = sprintf( '%s="%s"', $name, $value );
			}

			if ( empty( $parts ) ) {
				return '';
			}

			return ' ' . implode( ' ', $parts );
		}

		/**
		 * Sanitize an HTML tag name to a known safe set.
		 *
		 * @param string $tag Requested tag.
		 * @return string
		 */
		private static function sanitize_tag_name( $tag ) {
			$tag     = strtolower( (string) $tag );
			$allowed = array( 'span', 'i', 'div' );

			if ( in_array( $tag, $allowed, true ) ) {
				return $tag;
			}

			return 'span';
		}

		/**
		 * Build a sanitized SVG string from a manifest icon entry.
		 *
		 * Supports two manifest shapes:
		 * - $icon_data['svg']  — full <svg>...</svg> markup.
		 * - $icon_data['body'] — inner SVG markup (paths, etc.); wrapped in an SVG shell.
		 *
		 * @param array $icon_data Manifest entry for a single icon.
		 * @return string Sanitized SVG markup, or empty string.
		 */
		private static function build_svg_from_manifest_icon( array $icon_data ) {
			if ( isset( $icon_data['svg'] ) && is_string( $icon_data['svg'] ) && '' !== $icon_data['svg'] ) {
				return Spectre_Icons_SVG_Sanitizer::sanitize( $icon_data['svg'] );
			}

			if ( isset( $icon_data['body'] ) && is_string( $icon_data['body'] ) && '' !== $icon_data['body'] ) {
				$body = $icon_data['body'];

				$attrs = array(
					'xmlns'           => 'http://www.w3.org/2000/svg',
					'width'           => '24',
					'height'          => '24',
					'viewBox'         => '0 0 24 24',
					'fill'            => 'none',
					'stroke'          => 'currentColor',
					'stroke-width'    => '2',
					'stroke-linecap'  => 'round',
					'stroke-linejoin' => 'round',
				);

				$attr_string = self::attributes_to_string( $attrs );

				$svg = sprintf( '<svg%1$s>%2$s</svg>', $attr_string, $body );

				return Spectre_Icons_SVG_Sanitizer::sanitize( $svg );
			}

			return '';
		}

		/**
		 * Internal debug logger.
		 *
		 * @param string $message Message to log.
		 * @return void
		 */
		private static function log_debug( $message ) {
			if ( ! is_scalar( $message ) ) {
				$message = sprintf( 'Non-scalar message type: %s', gettype( $message ) );
			}

			$message = (string) $message;

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( '[Spectre Icons][Icon Renderer] ' . $message );
			}
		}
	}

endif;
