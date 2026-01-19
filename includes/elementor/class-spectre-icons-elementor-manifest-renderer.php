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

		private static $libraries   = array();
		private static $icons_cache = array();

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

			self::$libraries[$slug] = array(
				'manifest' => $manifest_path,
				'prefix'   => is_string($args['prefix']) ? $args['prefix'] : '',
				'options'  => is_array($args['options']) ? $args['options'] : array(),
			);

			unset(self::$icons_cache[$slug]);
		}

		public static function get_icon_slugs($library_slug) {
			$slug = sanitize_key($library_slug);
			if ('' === $slug || ! isset(self::$libraries[$slug])) {
				return array();
			}

			$icons = self::get_icons($slug);
			return is_array($icons) ? array_keys($icons) : array();
		}

		public static function render_icon($icon, $attributes = array(), $tag = 'span') {
			list($library_slug, $icon_slug) = self::extract_slug($icon);

			if ('' === $icon_slug || ! isset(self::$libraries[$library_slug])) {
				return '';
			}

			$icons = self::get_icons($library_slug);
			if (empty($icons[$icon_slug])) {
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

		private static function get_icons($library_slug) {
			if (isset(self::$icons_cache[$library_slug])) {
				return self::$icons_cache[$library_slug];
			}

			$library = self::$libraries[$library_slug];
			$path    = (string) $library['manifest'];

			$base_dir = realpath(trailingslashit(SPECTRE_ICONS_PATH . 'assets/manifests'));
			$real     = realpath($path);

			if (! $base_dir || ! $real || 0 !== strpos($real, $base_dir)) {
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			global $wp_filesystem;
			if (! $wp_filesystem) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				if (! WP_Filesystem()) {
					return array();
				}
			}

			$contents = $wp_filesystem->get_contents($real);
			if (! is_string($contents) || '' === $contents) {
				return array();
			}

			$data = json_decode($contents, true);
			if (! is_array($data)) {
				return array();
			}

			if (isset($data['icons']) && is_array($data['icons'])) {
				$data = $data['icons'];
			}

			$icons = array();
			foreach ($data as $slug => $entry) {
				$key = sanitize_key(is_string($slug) ? $slug : ($entry['slug'] ?? ''));
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

		private static function extract_slug($icon) {
			$library = '';
			$slug    = '';

			if (is_array($icon)) {
				$library = sanitize_key($icon['library'] ?? '');
				$value   = (string) ($icon['value'] ?? $icon['icon'] ?? '');
				if ($value) {
					$parts = preg_split('/\s+/', trim($value));
					$slug  = sanitize_key(end($parts));
				}
			} elseif (is_string($icon)) {
				$slug = sanitize_key($icon);
			}

			if ($library && isset(self::$libraries[$library]['prefix'])) {
				$prefix = self::$libraries[$library]['prefix'];
				if ($prefix && 0 === strpos($slug, $prefix)) {
					$slug = sanitize_key(substr($slug, strlen($prefix)));
				}
			}

			return array($library, $slug);
		}

		private static function sanitize_tag_name($tag) {
			return in_array(strtolower($tag), array('span', 'i', 'div'), true) ? strtolower($tag) : 'span';
		}

		private static function maybe_add_style_class(array $attributes, $library_slug) {
			$class = strpos($library_slug, 'lucide') !== false
				? 'spectre-icon--style-outline'
				: (strpos($library_slug, 'fontawesome') !== false ? 'spectre-icon--style-filled' : '');

			if ($class) {
				$attributes['class'] = trim(($attributes['class'] ?? '') . ' ' . $class);
			}

			return $attributes;
		}

		private static function prepare_attributes(array $attributes, $icon_slug, array $library) {
			$class = ($library['prefix'] ?? '') . $icon_slug;
			$attributes['class'] = trim($class . ' ' . ($attributes['class'] ?? ''));

			$clean = array();
			foreach ($attributes as $k => $v) {
				if (0 === stripos($k, 'on')) {
					continue;
				}
				if (preg_match('/^(data|aria)-|^[a-z][a-z0-9_\-:.]*$/i', $k)) {
					$clean[$k] = $v;
				}
			}

			return $clean;
		}

		private static function attributes_to_string(array $attributes) {
			$out = '';
			foreach ($attributes as $k => $v) {
				$out .= sprintf(' %s="%s"', esc_attr($k), esc_attr((string) $v));
			}
			return $out;
		}

		private static function build_svg_from_manifest_icon(array $icon_data) {
			if (! empty($icon_data['svg'])) {
				return Spectre_Icons_SVG_Sanitizer::sanitize($icon_data['svg']);
			}
			return '';
		}
	}

endif;
