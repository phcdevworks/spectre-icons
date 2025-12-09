<?php
/**
 * Shared SVG sanitization utilities.
 *
 * @package SpectreIcons
 */

if ( ! class_exists( 'Spectre_Icons_SVG_Sanitizer' ) ) :
	/**
	 * Removes disallowed SVG tags/attributes to prevent script injection.
	 */
	final class Spectre_Icons_SVG_Sanitizer {

		/**
		 * Allowed SVG elements mapped to their allowed attributes.
		 *
		 * @var array<string, array<string, bool>>
		 */
		private static $allowed_elements;

		/**
		 * Sanitize an arbitrary SVG snippet.
		 *
		 * @param string $svg SVG markup.
		 *
		 * @return string
		 */
		public static function sanitize( $svg ) {
			if ( empty( $svg ) || ! is_string( $svg ) ) {
				return '';
			}

			$svg = trim( $svg );

			if ( '' === $svg ) {
				return '';
			}

			if ( ! extension_loaded( 'dom' ) ) {
				return self::fallback_strip_disallowed( $svg );
			}

			$dom              = new DOMDocument();
			$internal_errors  = libxml_use_internal_errors( true );
			$previous_loader  = null;
			$loader_supported = function_exists( 'libxml_disable_entity_loader' );

			if ( $loader_supported ) {
				$previous_loader = libxml_disable_entity_loader( true ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctionParameters.libxml_disable_entity_loader_Deprecated
			}

			$loaded = $dom->loadXML(
				$svg,
				LIBXML_NONET | LIBXML_COMPACT | LIBXML_NOERROR | LIBXML_NOWARNING
			);

			libxml_clear_errors();
			libxml_use_internal_errors( $internal_errors );

			if ( $loader_supported ) {
				libxml_disable_entity_loader( $previous_loader ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctionParameters.libxml_disable_entity_loader_Deprecated
			}

			if ( ! $loaded || ! $dom->documentElement ) {
				return '';
			}

			self::sanitize_dom_node( $dom->documentElement );

			$output = $dom->saveXML( $dom->documentElement );

			if ( false === $output ) {
				return '';
			}

			// Minify whitespace similarly to the prior manifest generator.
			$output = preg_replace( '/\s+/', ' ', $output );

			return trim( (string) $output );
		}

		/**
		 * Sanitize an individual DOM node recursively.
		 *
		 * @param DOMNode $node Current DOM node.
		 */
		private static function sanitize_dom_node( DOMNode $node ) {
			if ( XML_ELEMENT_NODE === $node->nodeType ) {
				$tag = strtolower( $node->nodeName );

				if ( ! self::is_element_allowed( $tag ) ) {
					self::remove_node( $node );
					return;
				}

				if ( $node->hasAttributes() ) {
					for ( $i = $node->attributes->length - 1; $i >= 0; $i-- ) {
						$attribute = $node->attributes->item( $i );

						if ( ! $attribute ) {
							continue;
						}

						$name = strtolower( $attribute->nodeName );

						if ( ! self::is_attribute_allowed( $tag, $name ) || self::value_has_disallowed_protocol( $attribute->value ) ) {
							$node->removeAttributeNode( $attribute );
						}
					}
				}
			} elseif ( XML_COMMENT_NODE === $node->nodeType ) {
				self::remove_node( $node );
				return;
			}

			for ( $child = $node->firstChild; null !== $child; ) {
				$next = $child->nextSibling;
				self::sanitize_dom_node( $child );
				$child = $next;
			}
		}

		/**
		 * Determine if the tag is allowed.
		 *
		 * @param string $tag Tag name.
		 *
		 * @return bool
		 */
		private static function is_element_allowed( $tag ) {
			self::prime_allow_list();

			return isset( self::$allowed_elements[ $tag ] );
		}

		/**
		 * Determine if the attribute is allowed for a tag.
		 *
		 * @param string $tag       Tag name.
		 * @param string $attribute Attribute name.
		 *
		 * @return bool
		 */
		private static function is_attribute_allowed( $tag, $attribute ) {
			self::prime_allow_list();

			if ( isset( self::$allowed_elements[ $tag ][ $attribute ] ) ) {
				return true;
			}

			return isset( self::$allowed_elements['*'][ $attribute ] );
		}

		/**
		 * Remove a node from the DOM safely.
		 *
		 * @param DOMNode $node DOM node to remove.
		 */
		private static function remove_node( DOMNode $node ) {
			if ( null === $node->parentNode ) {
				return;
			}

			$node->parentNode->removeChild( $node );
		}

		/**
		 * Simple protocol guard for attribute values.
		 *
		 * @param string $value Attribute value.
		 *
		 * @return bool
		 */
		private static function value_has_disallowed_protocol( $value ) {
			if ( ! is_string( $value ) ) {
				return true;
			}

			$value = trim( $value );

			if ( '' === $value ) {
				return false;
			}

			$lower_value = strtolower( $value );

			foreach ( array( 'javascript:', 'data:', 'vbscript:' ) as $protocol ) {
				if ( 0 === strpos( $lower_value, $protocol ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Build the allow list for SVG markup.
		 */
		private static function prime_allow_list() {
			if ( null !== self::$allowed_elements ) {
				return;
			}

			$common_attributes = array(
				'class'             => true,
				'clip-path'         => true,
				'clip-rule'         => true,
				'color'             => true,
				'cx'                => true,
				'cy'                => true,
				'd'                 => true,
				'fill'              => true,
				'fill-opacity'      => true,
				'fill-rule'         => true,
				'filter'            => true,
				'focusable'         => true,
				'height'            => true,
				'id'                => true,
				'opacity'           => true,
				'r'                 => true,
				'radius'            => true,
				'role'              => true,
				'rx'                => true,
				'ry'                => true,
				'stroke'            => true,
				'stroke-dasharray'  => true,
				'stroke-dashoffset' => true,
				'stroke-linecap'    => true,
				'stroke-linejoin'   => true,
				'stroke-miterlimit' => true,
				'stroke-opacity'    => true,
				'stroke-width'      => true,
				'transform'         => true,
				'viewBox'           => true,
				'width'             => true,
				'x'                 => true,
				'x1'                => true,
				'x2'                => true,
				'y'                 => true,
				'y1'                => true,
				'y2'                => true,
			);

			$shape_attributes = array_merge(
				$common_attributes,
				array(
					'points' => true,
				)
			);

			self::$allowed_elements = array(
				'*'    => array(
					'aria-hidden' => true,
					'data-name'   => true,
					'focusable'   => true,
					'xml:space'   => true,
				),
				'svg'  => array_merge(
					$common_attributes,
					array(
						'xmlns'       => true,
						'xmlns:xlink' => true,
						'version'     => true,
						'preserveAspectRatio' => true,
					)
				),
				'g'    => $shape_attributes,
				'path' => array_merge(
					$common_attributes,
					array(
						'pathLength' => true,
					)
				),
				'polyline' => $shape_attributes,
				'polygon'  => $shape_attributes,
				'circle'   => $shape_attributes,
				'ellipse'  => $shape_attributes,
				'rect'     => $shape_attributes,
				'line'     => $shape_attributes,
				'title'    => array( 'id' => true ),
				'desc'     => array( 'id' => true ),
				'defs'     => array(),
				'linearGradient' => array(
					'id'                => true,
					'x1'                => true,
					'x2'                => true,
					'y1'                => true,
					'y2'                => true,
					'gradientUnits'     => true,
					'gradientTransform' => true,
				),
				'radialGradient' => array(
					'id'                => true,
					'cx'                => true,
					'cy'                => true,
					'r'                 => true,
					'fx'                => true,
					'fy'                => true,
					'gradientUnits'     => true,
					'gradientTransform' => true,
				),
				'stop' => array(
					'offset'    => true,
					'stop-color' => true,
					'stop-opacity' => true,
				),
				'use' => array(
					'xlink:href' => true,
					'href'       => true,
					'transform'  => true,
				),
			);
		}

		/**
		 * Fallback string sanitization if DOM isn't available.
		 *
		 * @param string $svg SVG markup.
		 *
		 * @return string
		 */
		private static function fallback_strip_disallowed( $svg ) {
			$svg = preg_replace( '#<(script|foreignObject|iframe|audio|video|canvas|embed|object).*?</\1>#is', '', $svg );
			$svg = preg_replace( '#on[a-z]+\s*=\s*([\'"]).*?\1#i', '', $svg );
			$svg = preg_replace( '/\s+/', ' ', $svg );

			return trim( (string) $svg );
		}
	}
endif;
