<?php

/**
 * Renders Spectre icon manifests as inline SVG for Elementor.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Spectre_Icons_Elementor_Manifest_Renderer')) :

	final class Spectre_Icons_Elementor_Manifest_Renderer {

		/**
		 * @var array<string,array>
		 */
		private static $libraries = array();

		/**
		 * @var array<string,array>
		 */
		private static $icons_cache = array();

		/**
		 * Register a manifest for a library.
		 *
		 * @param string $library_slug
		 * @param string $manifest_path
		 * @param array  $args
		 * @return void
		 */
		public static function register_manifest($library_slug, $manifest_path, array $args = array()) {
			$slug = sanitize_key($library_slug);

			if ('' === $slug || ! is_string($manifest_path) || '' === $manifest_path) {
				return;
			}

			$args = wp_parse_args(
				$args,
				array(
					'prefix'  => '',
					'options' => array(),
				)
			);

			$prefix = is_string($args['prefix']) ? (string) $args['prefix'] : '';
			// Preserve hyphens/trailing hyphen for prefixes like "spectre-lucide-".
			$prefix = preg_replace('/[^a-z0-9\-_]/i', '', $prefix);

			self::$libraries[$slug] = array(
				'manifest' => $manifest_path,
				'prefix'   => $prefix,
				'options'  => is_array($args['options']) ? $args['options'] : array(),
			);

			unset(self::$icons_cache[$slug]);
		}

		/**
		 * Get all icon slugs for a given library.
		 *
		 * @param string $library_slug
		 * @return string[]
		 */
		public static function get_icon_slugs($library_slug) {
			$slug = sanitize_key($library_slug);

			if ('' === $slug || ! isset(self::$libraries[$slug])) {
				return array();
			}

			$icons = self::get_icons($slug);

			return is_array($icons) ? array_keys($icons) : array();
		}

		/**
		 * Render a single icon.
		 *
		 * @param array|string $icon
		 * @param array        $attributes
		 * @param string       $tag
		 * @return string
		 */
		public static function render_icon($icon, $attributes = array(), $tag = 'span') {
			list($library_slug, $icon_slug) = self::extract_slug($icon);

			if ('' === $icon_slug || '' === $library_slug || ! isset(self::$libraries[$library_slug])) {
				return '';
			}

			$icons = self::get_icons($library_slug);

			if (empty($icons) || empty($icons[$icon_slug]) || ! is_array($icons[$icon_slug])) {
				return '';
			}

			$attributes = self::prepare_attributes(
				self::maybe_add_style_class((array) $attributes, $library_slug),
				$icon_slug,
				self::$libraries[$library_slug]
			);

			$svg = self::build_svg_from_manifest_icon($icons[$icon_slug]);

			if ('' === $svg) {
				return '';
			}

			$tag = self::sanitize_tag_name($tag);

			return sprintf(
				'<%1$s%2$s>%3$s</%1$s>',
				$tag,
				self::attributes_to_string($attributes),
				$svg
			);
		}

		/**
		 * Load and cache icon data for a library.
		 *
		 * IMPORTANT: Do NOT use WP_Filesystem here (breaks on frontend on some hosts).
		 *
		 * @param string $library_slug
		 * @return array<string,array>
		 */
		private static function get_icons($library_slug) {
			if (isset(self::$icons_cache[$library_slug])) {
				return self::$icons_cache[$library_slug];
			}

			if (! isset(self::$libraries[$library_slug])) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			$library = self::$libraries[$library_slug];
			$path    = isset($library['manifest']) ? (string) $library['manifest'] : '';

			if ('' === $path) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			// Restrict manifest loading to plugin manifests directory.
			$base_dir = realpath(SPECTRE_ICONS_PATH . 'assets/manifests');
			$real     = realpath($path);

			if (! $base_dir || ! $real) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			$base_dir = trailingslashit($base_dir);

			// Must be inside base_dir (with trailing slash to avoid prefix tricks).
			if (0 !== strpos($real, $base_dir)) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			// Must be a readable file.
			if (! is_file($real) || ! is_readable($real)) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			$contents = file_get_contents($real); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if (! is_string($contents) || '' === $contents) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			$data = json_decode($contents, true);
			if (! is_array($data)) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			if (isset($data['icons']) && is_array($data['icons'])) {
				$data = $data['icons'];
			}

			$icons = array();

			foreach ($data as $slug => $entry) {
				$key = '';

				if (is_string($slug) && '' !== $slug) {
					$key = sanitize_key($slug);
				} elseif (is_array($entry) && ! empty($entry['slug'])) {
					$key = sanitize_key((string) $entry['slug']);
				}

				if ('' === $key) {
					continue;
				}

				if (is_string($entry)) {
					$icons[$key] = array('svg' => $entry);
				} elseif (is_array($entry)) {
					$icons[$key] = $entry;
				}
			}

			self::$icons_cache[$library_slug] = $icons;

			return $icons;
		}

		/**
		 * @param array|string $icon
		 * @return array{string,string}
		 */
		private static function extract_slug($icon) {
			$library = '';
			$slug    = '';

			if (is_array($icon)) {
				$library = sanitize_key($icon['library'] ?? '');
				$value   = (string) ($icon['value'] ?? $icon['icon'] ?? '');

				if ('' !== $value) {
					$parts = preg_split('/\s+/', trim($value));
					$slug  = sanitize_key((string) end($parts));
				}
			} elseif (is_string($icon)) {
				$slug = sanitize_key($icon);
			}

			if ($library && isset(self::$libraries[$library]['prefix'])) {
				$prefix = (string) self::$libraries[$library]['prefix'];
				if ('' !== $prefix && 0 === strpos($slug, $prefix)) {
					$slug = sanitize_key(substr($slug, strlen($prefix)));
				}
			}

			return array($library, $slug);
		}

		/**
		 * @param string $tag
		 * @return string
		 */
		private static function sanitize_tag_name($tag) {
			$tag = strtolower((string) $tag);
			return in_array($tag, array('span', 'i', 'div'), true) ? $tag : 'span';
		}

		/**
		 * @param array  $attributes
		 * @param string $library_slug
		 * @return array
		 */
		private static function maybe_add_style_class(array $attributes, $library_slug) {
			$class = (false !== strpos($library_slug, 'lucide'))
				? 'spectre-icon--style-outline'
				: ((false !== strpos($library_slug, 'fontawesome')) ? 'spectre-icon--style-filled' : '');

			if ('' !== $class) {
				$attributes['class'] = trim((string) ($attributes['class'] ?? '') . ' ' . $class);
			}

			return $attributes;
		}

		/**
		 * @param array  $attributes
		 * @param string $icon_slug
		 * @param array  $library
		 * @return array
		 */
		private static function prepare_attributes(array $attributes, $icon_slug, array $library) {
			$prefix = isset($library['prefix']) ? (string) $library['prefix'] : '';
			$class  = $prefix . $icon_slug;

			$attributes['class'] = trim($class . ' ' . (string) ($attributes['class'] ?? ''));

			$clean = array();

			foreach ($attributes as $k => $v) {
				$k = (string) $k;
				if ('' === $k) {
					continue;
				}

				// Disallow event handlers like onclick, onload, etc.
				if (0 === stripos($k, 'on')) {
					continue;
				}

				// Allow common safe attribute names, including aria-* and data-*.
				if (preg_match('/^(?:data|aria)-[a-z0-9_\-]+$/i', $k) || preg_match('/^[a-z][a-z0-9_\-:.]*$/i', $k)) {
					$clean[$k] = $v;
				}
			}

			return $clean;
		}

		/**
		 * @param array $attributes
		 * @return string
		 */
		private static function attributes_to_string(array $attributes) {
			$out = '';
			foreach ($attributes as $k => $v) {
				$out .= sprintf(' %s="%s"', esc_attr($k), esc_attr((string) $v));
			}
			return $out;
		}

		/**
		 * @param array $icon_data
		 * @return string
		 */
		private static function build_svg_from_manifest_icon(array $icon_data) {
			if (! empty($icon_data['svg']) && is_string($icon_data['svg'])) {
				return Spectre_Icons_SVG_Sanitizer::sanitize($icon_data['svg']);
			}
			return '';
		}
	}

endif;
