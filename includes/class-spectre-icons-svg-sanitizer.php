<?php

/**
 * Sanitizes SVG markup safely for Spectre Icons.
 *
 * This ensures inline SVG from manifests cannot inject scripts,
 * events, external loads, or unsafe DOM constructs.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Spectre_Icons_SVG_Sanitizer')) :

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
			'x',
			'y',
			'width',
			'height',
			'viewBox',
			'points',
			'x1',
			'y1',
			'x2',
			'y2',
			'transform',
			'xmlns',
		);

		/**
		 * Sanitize an SVG string.
		 *
		 * @param string $svg Raw SVG markup.
		 * @return string Safe SVG markup (may be empty).
		 */
		public static function sanitize($svg) {
			if (! is_string($svg) || '' === trim($svg)) {
				return '';
			}

			if (! class_exists('DOMDocument')) {
				// Safest fallback when DOM extension is unavailable.
				return '';
			}

			// Strip anything outside the <svg>…</svg> block.
			if (preg_match('/<svg[\s\S]*?<\/svg>/i', $svg, $match)) {
				$svg = $match[0];
			} else {
				return '';
			}

			// Remove script tags, foreignObject, and other malicious containers (case-insensitive).
			$svg = preg_replace('/<\/?(script|foreignObject|iframe|object|embed)[^>]*>/i', '', $svg);

			// Remove inline event handlers (anything starting with "on…").
			$svg = preg_replace('/\son[a-z]+\s*=\s*"[^"]*"/i', '', $svg);
			$svg = preg_replace("/\son[a-z]+\s*=\s*'[^']*'/i", '', $svg);

			// Add XML header if missing (improves loadXML compatibility).
			if (false === stripos($svg, '<?xml')) {
				$svg = '<?xml version="1.0" encoding="UTF-8"?>' . $svg;
			}

			// Load via DOM to strip unwanted tags + attributes.
			$dom = new DOMDocument();

			// Prevent external entity expansion attacks.
			$dom->resolveExternals   = false;
			$dom->substituteEntities = false;

			// Suppress warnings for malformed SVG.
			libxml_use_internal_errors(true);
			$dom->loadXML($svg, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
			libxml_clear_errors();

			if (! $dom->documentElement) {
				return '';
			}

			self::sanitize_node_deep($dom->documentElement);

			// Output clean XML (just the <svg> element).
			$clean = $dom->saveXML($dom->documentElement);

			return is_string($clean) ? $clean : '';
		}

		/**
		 * Recursively sanitize a DOM node.
		 *
		 * @param DOMNode $node Node to sanitize.
		 * @return void
		 */
		private static function sanitize_node_deep(DOMNode $node) {

			if (XML_ELEMENT_NODE === $node->nodeType) {

				$tag = $node->nodeName;

				// Remove tag entirely if not permitted.
				if (! in_array($tag, self::$allowed_tags, true)) {
					if ($node->parentNode) {
						$node->parentNode->removeChild($node);
					}
					return;
				}

				// Remove disallowed attributes.
				if ($node->hasAttributes()) {
					$remove = array();

					foreach (iterator_to_array($node->attributes) as $attr) {
						$name  = (string) $attr->nodeName;
						$value = strtolower(preg_replace('/\s+/', '', (string) $attr->nodeValue));

						// Drop event handlers, href/xlink:href, javascript: URLs, and anything not allowlisted.
						if (
							0 === stripos($name, 'on') ||
							'href' === $name ||
							'xlink:href' === $name ||
							0 === strpos($value, 'javascript:') ||
							! in_array($name, self::$allowed_attributes, true)
						) {
							$remove[] = $name;
						}
					}

					foreach ($remove as $attr_name) {
						$node->removeAttribute($attr_name);
					}
				}
			}

			// Sanitize children recursively.
			if ($node->hasChildNodes()) {
				foreach (iterator_to_array($node->childNodes) as $child) {
					self::sanitize_node_deep($child);
				}
			}
		}
	}

endif;
