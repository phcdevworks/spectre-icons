<?php
/**
 * Sanitizes SVG markup safely for Spectre Icons.
 *
 * This ensures inline SVG from manifests cannot inject scripts,
 * events, external loads, or unsafe DOM constructs.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Spectre_Icons_SVG_Sanitizer' ) ) :

	/**
	 * Sanitizes raw SVG markup from bundled manifests for safe inline output.
	 */
	final class Spectre_Icons_SVG_Sanitizer {

		/**
		 * Allowed SVG tags.
		 *
		 * @var string[]
		 */
		private static $allowed_tags = array(
			'svg',
			'path',
			'g',
			'circle',
			'rect',
			'line',
			'polyline',
			'polygon',
			'ellipse',
			'defs',
			'use',
			'symbol',
			'title',
			'desc',
		);

		/**
		 * Allowed attributes for SVG elements.
		 *
		 * NOTE: We intentionally do NOT allow href/xlink:href, style, or any on* handlers.
		 *
		 * @var string[]
		 */
		private static $allowed_attributes = array(
			'class',
			'fill',
			'stroke',
			'stroke-width',
			'stroke-linecap',
			'stroke-linejoin',
			'd',
			'cx',
			'cy',
			'r',
			'rx',
			'ry',
			'x',
			'y',
			'width',
			'height',
			'viewbox', // Normalize to lowercase comparison.
			'points',
			'x1',
			'y1',
			'x2',
			'y2',
			'transform',
			'xmlns',
			'fill-rule',
			'clip-rule',
			'stroke-dasharray',
			'stroke-dashoffset',
			'vector-effect',
			'opacity',
			'fill-opacity',
			'stroke-opacity',
			'aria-hidden',
			'aria-label',
			'aria-labelledby',
			'aria-describedby',
			'role',
			'focusable',
			'preserveaspectratio',
			'overflow',
			'id',
		);

		/**
		 * Sanitize an SVG string.
		 *
		 * @param string $svg Raw SVG markup.
		 * @return string Safe SVG markup (may be empty).
		 */
		public static function sanitize( $svg ) {
			if ( ! is_string( $svg ) || '' === trim( $svg ) ) {
				return '';
			}

			if ( ! class_exists( 'DOMDocument' ) ) {
				// Safest fallback when DOM extension is unavailable.
				return '';
			}

			// Strip anything outside the <svg>…</svg> block or <svg /> self-closing tag.
			if ( preg_match( '/<svg(?:[\s\S]*?<\/svg>|[\s\S]*?\/>)/i', $svg, $match ) ) {
				$svg = $match[0];
			} else {
				return '';
			}

			// Remove dangerous containers (best-effort pre-strip).
			$svg = preg_replace( '/<\/?(script|foreignObject|iframe|object|embed)[^>]*>/i', '', $svg );

			// Remove inline event handlers (best-effort pre-strip).
			$svg = preg_replace( '/\son[a-z]+\s*=\s*"[^"]*"/i', '', $svg );
			$svg = preg_replace( "/\son[a-z]+\s*=\s*'[^']*'/i", '', $svg );

			// Prevent DOCTYPE/entity tricks (DOMDocument can parse DOCTYPE in XML mode).
			$svg = preg_replace( '/<!DOCTYPE[\s\S]*?>/i', '', $svg );
			$svg = preg_replace( '/<!ENTITY[\s\S]*?>/i', '', $svg );

			// Load via DOM to enforce allowlists.
			$dom = new DOMDocument();

			// Prevent external entity expansion attacks.
			$dom->resolveExternals   = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$dom->substituteEntities = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			libxml_use_internal_errors( true );
			$loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR );
			libxml_clear_errors();

			if ( ! $loaded || ! $dom->documentElement ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				return '';
			}

			self::sanitize_node_deep( $dom->documentElement ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			$clean = $dom->saveXML( $dom->documentElement ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			return is_string( $clean ) ? $clean : '';
		}

		/**
		 * Recursively sanitize a DOM node.
		 *
		 * @param DOMNode $node Node to sanitize.
		 * @return void
		 */
		private static function sanitize_node_deep( DOMNode $node ) {

			if ( XML_ELEMENT_NODE === $node->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$tag = strtolower( (string) $node->nodeName ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

				// Remove tag entirely if not permitted.
				if ( ! in_array( $tag, self::$allowed_tags, true ) ) {
					if ( $node->parentNode ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$node->parentNode->removeChild( $node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}
					return;
				}

				// Remove disallowed attributes.
				if ( $node->hasAttributes() ) {
					$remove = array();

					foreach ( iterator_to_array( $node->attributes ) as $attr ) {
						$name_raw = (string) $attr->nodeName; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$name     = strtolower( $name_raw );
						$value    = strtolower( preg_replace( '/\s+/', '', (string) $attr->nodeValue ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

						// Block any event handlers.
						if ( 0 === stripos( $name_raw, 'on' ) ) {
							$remove[] = $name_raw;
							continue;
						}

						// Block any href variants + xlink namespace.
						if ( 'href' === $name || 'xlink:href' === $name || 'xmlns:xlink' === $name ) {
							$remove[] = $name_raw;
							continue;
						}

						// Block javascript: and data: urls anywhere.
						if ( 0 === strpos( $value, 'javascript:' ) || 0 === strpos( $value, 'data:' ) ) {
							$remove[] = $name_raw;
							continue;
						}

						// Enforce attribute allowlist (case-insensitive).
						if ( ! in_array( $name, self::$allowed_attributes, true ) ) {
							$remove[] = $name_raw;
							continue;
						}
					}

					if ( $node instanceof DOMElement ) {
						foreach ( $remove as $attr_name ) {
							$node->removeAttribute( $attr_name );
						}
					}
				}
			}

			// Sanitize children recursively.
			if ( $node->hasChildNodes() ) {
				foreach ( iterator_to_array( $node->childNodes ) as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					self::sanitize_node_deep( $child );
				}
			}
		}
	}

endif;
