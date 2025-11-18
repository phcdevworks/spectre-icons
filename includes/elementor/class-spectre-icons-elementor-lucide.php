<?php

/**
 * Lucide icon helper utilities.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Spectre_Icons_Elementor_Lucide' ) ) :
	/**
	 * Provides icon data and rendering helpers for the Lucide pack.
	 */
	final class Spectre_Icons_Elementor_Lucide {


		/**
		 * Cached icon list.
		 *
		 * @var array|null
		 */
		private static $icon_cache = null;

		/**
		 * Cached SVG markup keyed by slug.
		 *
		 * @var array
		 */
		private static $svg_cache = array();

		/**
		 * Directory containing the Lucide SVG files.
		 *
		 * @return string
		 */
		private static function get_icons_dir() {
			return SPECTRE_ICONS_PATH . '/assets/iconpacks/lucide';
		}

		/**
		 * Collect every icon slug available in the Lucide pack.
		 *
		 * @return array
		 */
		public static function get_icon_slugs() {
			if ( null !== self::$icon_cache ) {
				return self::$icon_cache;
			}

			$directory = self::get_icons_dir();

			if ( ! is_dir( $directory ) ) {
				self::$icon_cache = array();
				return self::$icon_cache;
			}

			$files = glob( trailingslashit( $directory ) . '*.svg' );

			if ( empty( $files ) ) {
				self::$icon_cache = array();
				return self::$icon_cache;
			}

			$slugs = array_map(
				static function ( $file ) {
					return sanitize_key( basename( $file, '.svg' ) );
				},
				$files
			);

			sort( $slugs );

			self::$icon_cache = $slugs;

			return self::$icon_cache;
		}

		/**
		 * Render callback used by Elementor to output the actual SVG markup.
		 *
		 * @param array  $icon       Icon structure from Elementor.
		 * @param array  $attributes HTML attributes for the wrapper element.
		 * @param string $tag        HTML tag Elementor requested (unused, SVG is returned).
		 *
		 * @return string
		 */
		public static function render_icon( $icon, $attributes = array(), $tag = 'span' ) {
			unset( $tag );

			$slug = self::normalize_slug( $icon );

			if ( ! $slug ) {
				return '';
			}

			$svg = self::get_svg_markup( $slug );

			if ( empty( $svg ) ) {
				return '';
			}

			$attributes = self::prepare_attributes( $attributes, $slug );
			$attr_pairs = array();

			foreach ( $attributes as $key => $value ) {
				if ( is_array( $value ) ) {
					$value = implode( ' ', array_unique( array_filter( $value ) ) );
				}

				if ( '' === $value ) {
					continue;
				}

				$attr_pairs[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			$attr_string = $attr_pairs ? ' ' . implode( ' ', $attr_pairs ) : '';

			return sprintf( '<span%s>%s</span>', $attr_string, $svg );
		}

		/**
		 * Normalize Elementor's value into a usable slug.
		 *
		 * @param array $icon Icon payload from Elementor.
		 *
		 * @return string
		 */
		private static function normalize_slug( $icon ) {
			if ( empty( $icon['value'] ) ) {
				return '';
			}

			$value = strtolower( (string) $icon['value'] );
			$value = trim( $value );
			$value = str_replace( array( 'spectre-lucide-', 'lucide-', 'lucide ' ), '', $value );
			$value = preg_replace( '/[^a-z0-9\-]+/', '-', $value );

			return trim( $value, '-' );
		}

		/**
		 * Retrieve the SVG markup for the provided slug.
		 *
		 * @param string $slug Icon slug.
		 *
		 * @return string
		 */
		private static function get_svg_markup( $slug ) {
			if ( isset( self::$svg_cache[ $slug ] ) ) {
				return self::$svg_cache[ $slug ];
			}

			$path = trailingslashit( self::get_icons_dir() ) . $slug . '.svg';

			if ( ! file_exists( $path ) ) {
				self::$svg_cache[ $slug ] = '';
				return '';
			}

			$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			if ( false === $svg ) {
				self::$svg_cache[ $slug ] = '';
				return '';
			}

			// Strip XML declaration and make sure markup is minified for transport.
			$svg = preg_replace( '/<\?xml.*?\?>/i', '', $svg );
			$svg = preg_replace( '/\s+/', ' ', $svg );
			$svg = trim( $svg );

			self::$svg_cache[ $slug ] = $svg;

			return self::$svg_cache[ $slug ];
		}

		/**
		 * Ensure the wrapper element always carries a meaningful class list.
		 *
		 * @param array  $attributes Attributes coming from Elementor.
		 * @param string $slug       Icon slug.
		 *
		 * @return array
		 */
		private static function prepare_attributes( $attributes, $slug ) {
			$defaults = array(
				'class' => array(),
				'role'  => 'img',
			);

			if ( ! empty( $attributes['class'] ) ) {
				if ( is_string( $attributes['class'] ) ) {
					$defaults['class'] = array_filter( explode( ' ', $attributes['class'] ) );
				} elseif ( is_array( $attributes['class'] ) ) {
					$defaults['class'] = $attributes['class'];
				}
			}

			$defaults['class'][] = 'spectre-icon--rendered';
			$defaults['class'][] = 'spectre-lucide-icon';
			$defaults['class'][] = 'spectre-lucide-icon--' . sanitize_html_class( $slug );

			$attributes['class'] = $defaults['class'];
			$attributes['role']  = $defaults['role'];

			return $attributes;
		}
	}
endif;
