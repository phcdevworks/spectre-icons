<?php

/**
 * Renders icons based on generated JSON manifests.
 *
 * @package SpectreElementorIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Spectre_Elementor_Icons_Manifest_Renderer')) :
	/**
	 * Handles manifest loading and inline SVG rendering.
	 */
	final class Spectre_Elementor_Icons_Manifest_Renderer
	{

		/**
		 * Registered libraries meta.
		 *
		 * @var array
		 */
		private static $libraries = [];

		/**
		 * Register a manifest-backed library.
		 *
		 * @param string $library_slug  Internal library slug.
		 * @param string $manifest_path Absolute path to the manifest file.
		 * @param array  $args          Extra configuration.
		 *
		 * @return bool
		 */
		public static function register_manifest($library_slug, $manifest_path, array $args = [])
		{
			if (empty($library_slug) || empty($manifest_path) || ! file_exists($manifest_path)) {
				return false;
			}

			self::$libraries[$library_slug] = [
				'path'         => $manifest_path,
				'icons'        => null,
				'class_prefix' => isset($args['class_prefix']) ? $args['class_prefix'] : '',
				'style'        => isset($args['style']) ? $args['style'] : 'filled',
			];

			return true;
		}

		/**
		 * Retrieve all icon slugs for the provided library.
		 *
		 * @param string $library_slug Library slug.
		 *
		 * @return array
		 */
		public static function get_icon_slugs($library_slug)
		{
			$icons = self::get_icons($library_slug);

			return array_keys($icons);
		}

		/**
		 * Render callback used by Elementor.
		 *
		 * @param array  $icon       Icon payload from Elementor.
		 * @param array  $attributes HTML attributes.
		 * @param string $tag        Requested HTML tag (unused; span returned).
		 *
		 * @return string
		 */
		public static function render_icon($icon, $attributes = [], $tag = 'span')
		{
			$library = isset($icon['library']) ? $icon['library'] : '';

			if (empty($library) || empty(self::$libraries[$library])) {
				return '';
			}

			$slug = self::extract_slug($icon, self::$libraries[$library]);

			if (empty($slug)) {
				return '';
			}

			$icons = self::get_icons($library);

			if (empty($icons[$slug])) {
				return '';
			}

			$attributes = self::prepare_attributes($attributes, $slug, $library);

			// Add the icon class that Elementor expects
			$prefix = self::$libraries[$library]['class_prefix'];
			if (! isset($attributes['class'])) {
				$attributes['class'] = [];
			}
			if (is_string($attributes['class'])) {
				$attributes['class'] = explode(' ', $attributes['class']);
			}
			$attributes['class'][] = $prefix . $slug;

			$attr_pairs = [];

			foreach ($attributes as $key => $value) {
				if (is_array($value)) {
					$value = implode(' ', array_unique(array_filter($value)));
				}

				if ('' === $value) {
					continue;
				}

				$attr_pairs[] = sprintf('%s="%s"', esc_attr($key), esc_attr($value));
			}

			$attr_string = $attr_pairs ? ' ' . implode(' ', $attr_pairs) : '';

			return sprintf('<span%s>%s</span>', $attr_string, $icons[$slug]);
		}

		/**
		 * Load manifest icons for a library (cached in memory).
		 *
		 * @param string $library Library slug.
		 *
		 * @return array
		 */
		private static function get_icons($library)
		{
			if (empty(self::$libraries[$library])) {
				return [];
			}

			if (null !== self::$libraries[$library]['icons']) {
				return self::$libraries[$library]['icons'];
			}

			$path = self::$libraries[$library]['path'];
			$json = file_exists($path) ? file_get_contents($path) : '';
			$data = json_decode($json, true);

			if (empty($data['icons']) || ! is_array($data['icons'])) {
				self::$libraries[$library]['icons'] = [];
				return [];
			}

			self::$libraries[$library]['icons'] = $data['icons'];

			return self::$libraries[$library]['icons'];
		}

		/**
		 * Extract the slug from Elementor's icon payload.
		 *
		 * @param array $icon    Icon payload.
		 * @param array $library Library configuration.
		 *
		 * @return string
		 */
		private static function extract_slug($icon, array $library)
		{
			if (empty($icon['value'])) {
				return '';
			}

			$value = is_array($icon['value']) ? implode(' ', $icon['value']) : (string) $icon['value'];
			$value = strtolower(trim($value));

			// Elementor sends: "displayPrefix selector" (e.g., "lucide lucide-home")
			// We need to extract just the icon slug (e.g., "home")
			$parts = preg_split('/\s+/', $value);

			// Last part is usually the full selector (e.g., "lucide-home")
			$maybe = array_pop($parts);

			$prefix = isset($library['class_prefix']) ? $library['class_prefix'] : '';

			// Remove the prefix to get the bare slug
			if ($prefix && 0 === strpos($maybe, $prefix)) {
				$maybe = substr($maybe, strlen($prefix));
			}

			return sanitize_key($maybe);
		}
		/**
		 * Normalize HTML attributes for the wrapper span.
		 *
		 * @param array  $attributes Incoming attributes.
		 * @param string $slug       Icon slug.
		 * @param string $library    Library slug.
		 *
		 * @return array
		 */
		private static function prepare_attributes($attributes, $slug, $library)
		{
			if (empty($attributes['class'])) {
				$attributes['class'] = [];
			}

			if (is_string($attributes['class'])) {
				$attributes['class'] = array_filter(explode(' ', $attributes['class']));
			}

			$attributes['class'][] = 'spectre-icon--rendered';
			$attributes['class'][] = 'spectre-icon--' . sanitize_html_class($library);

			$style = isset(self::$libraries[$library]['style']) ? self::$libraries[$library]['style'] : '';
			if ($style) {
				$attributes['class'][] = 'spectre-icon--style-' . sanitize_html_class($style);
			}

			$attributes['class'][] = 'spectre-icon--' . sanitize_html_class($library . '-' . $slug);
			$attributes['data-spectre-library'] = $library;

			return $attributes;
		}
	}
endif;
