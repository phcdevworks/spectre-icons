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

	/**
	 * Static service for working with JSON icon manifests.
	 *
	 * Responsibilities:
	 * - Register manifests per library (path + options).
	 * - Load & cache manifest contents.
	 * - Return icon slugs for previews.
	 * - Render individual icons as inline SVG elements.
	 */
	final class Spectre_Icons_Elementor_Manifest_Renderer {

		/**
		 * Registered libraries.
		 *
		 * Shape:
		 * [
		 *   'library-slug' => [
		 *     'manifest' => '/path/to/manifest.json',
		 *     'prefix'   => 'spectre-lucide-', // optional
		 *     'options'  => [ ... ],           // optional
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
		 * Usually called from icon-libraries.php when building library config.
		 *
		 * @param string $library_slug  Library slug (e.g. 'spectre-lucide').
		 * @param string $manifest_path Absolute path to the JSON manifest file.
		 * @param array  $args          Optional extra args. Supported keys:
		 *                              - prefix (string) CSS class prefix
		 *                              - options (array) any additional data
		 *
		 * @return void
		 */
		public static function register_manifest($library_slug, $manifest_path, array $args = array()) {
			$slug = sanitize_key($library_slug);

			if ('' === $slug) {
				self::log_debug(sprintf('register_manifest called with invalid library slug "%s".', (string) $library_slug));
				return;
			}

			if (! is_string($manifest_path) || '' === $manifest_path) {
				self::log_debug(sprintf('Library "%s" missing manifest path.', $slug));
				return;
			}

			$defaults = array(
				'prefix'  => '',
				'options' => array(),
			);

			$args = wp_parse_args($args, $defaults);

			self::$libraries[$slug] = array(
				'manifest' => $manifest_path,
				'prefix'   => is_string($args['prefix']) ? $args['prefix'] : '',
				'options'  => is_array($args['options']) ? $args['options'] : array(),
			);

			// Clear cache for this library in case we re-register.
			unset(self::$icons_cache[$slug]);
		}

		/**
		 * Get all icon slugs for a given library.
		 *
		 * Used by Elementor for preview lists.
		 *
		 * @param string $library_slug Library slug.
		 * @return array<string>       Icon slugs (may be empty).
		 */
		public static function get_icon_slugs($library_slug) {
			$slug = sanitize_key($library_slug);

			if ('' === $slug || ! isset(self::$libraries[$slug])) {
				self::log_debug(sprintf('get_icon_slugs called for unknown library "%s".', (string) $library_slug));
				return array();
			}

			$icons = self::get_icons($slug);

			if (empty($icons) || ! is_array($icons)) {
				return array();
			}

			return array_keys($icons);
		}

		/**
		 * Render a single icon as inline SVG wrapped in an HTML tag.
		 *
		 * This is wired as the Elementor `render_callback`.
		 *
		 * @param array|string $icon       Icon descriptor from Elementor, or raw slug.
		 * @param array        $attributes Optional HTML attributes for the wrapper tag.
		 * @param string       $tag        HTML tag name to wrap the SVG in (default: span).
		 *
		 * @return string Rendered HTML or empty string on failure.
		 */
		public static function render_icon($icon, $attributes = array(), $tag = 'span') {
			// Determine library + icon slug from Elementor's payload.
			list($library_slug, $icon_slug) = self::extract_slug($icon);

			if ('' === $icon_slug) {
				return '';
			}

			if ('' === $library_slug || ! isset(self::$libraries[$library_slug])) {
				self::log_debug(sprintf('render_icon: unknown library "%s" for icon "%s".', $library_slug, $icon_slug));
				return '';
			}

			$library = self::$libraries[$library_slug];
			$icons   = self::get_icons($library_slug);

			if (empty($icons) || ! is_array($icons)) {
				self::log_debug(sprintf('render_icon: no icons loaded for library "%s".', $library_slug));
				return '';
			}

			if (! isset($icons[$icon_slug])) {
				self::log_debug(sprintf('render_icon: icon "%s" not found in library "%s".', $icon_slug, $library_slug));
				return '';
			}

			$icon_data   = $icons[$icon_slug];
			$attributes  = is_array($attributes) ? $attributes : array();
			$tag         = self::sanitize_tag_name($tag);
			$attributes  = self::maybe_add_style_class($attributes, $library_slug);
			$attributes  = self::prepare_attributes($attributes, $icon_slug, $library);
			$attr_string = self::attributes_to_string($attributes);

			$svg = self::build_svg_from_manifest_icon($icon_data);

			if ('' === $svg) {
				self::log_debug(sprintf('render_icon: icon "%s" in library "%s" has empty SVG.', $icon_slug, $library_slug));
				return '';
			}

			// Wrapper tag is restricted (sanitize_tag_name), wrapper attributes are escaped,
			// and SVG markup is sanitized via Spectre_Icons_SVG_Sanitizer from local plugin manifests.
			return sprintf(
				'<%1$s%2$s>%3$s</%1$s>',
				$tag,
				$attr_string,
				$svg
			);
		}

		/**
		 * Add a style class based on library slug so CSS can target outline vs filled icons.
		 *
		 * @param array  $attributes Wrapper attributes.
		 * @param string $library_slug Library slug.
		 * @return array
		 */
		private static function maybe_add_style_class(array $attributes, $library_slug) {
			$style_class = '';
			if (false !== strpos($library_slug, 'lucide')) {
				$style_class = 'spectre-icon--style-outline';
			} elseif (false !== strpos($library_slug, 'fontawesome')) {
				$style_class = 'spectre-icon--style-filled';
			}

			if ('' === $style_class) {
				return $attributes;
			}

			if (isset($attributes['class'])) {
				if (is_array($attributes['class'])) {
					$attributes['class'][] = $style_class;
					return $attributes;
				}
				$attributes['class'] = trim((string) $attributes['class'] . ' ' . $style_class);
				return $attributes;
			}

			$attributes['class'] = $style_class;
			return $attributes;
		}

		/**
		 * Load and cache the icon manifest for the given library.
		 *
		 * @param string $library_slug Sanitized library slug.
		 * @return array<string, array> Map of icon slug => manifest entry.
		 */
		private static function get_icons($library_slug) {
			if (isset(self::$icons_cache[$library_slug])) {
				return self::$icons_cache[$library_slug];
			}

			if (! isset(self::$libraries[$library_slug])) {
				return array();
			}

			$library       = self::$libraries[$library_slug];
			$manifest_path = isset($library['manifest']) ? (string) $library['manifest'] : '';

			if ('' === $manifest_path || ! file_exists($manifest_path)) {
				self::log_debug(sprintf('Manifest file missing for library "%s": %s', $library_slug, $manifest_path));
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			// Read manifest using WP_Filesystem (reviewer-friendly alternative to file_get_contents()).
			$contents = '';

			global $wp_filesystem;
			if (! $wp_filesystem) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}

			if ($wp_filesystem && method_exists($wp_filesystem, 'get_contents')) {
				$contents = $wp_filesystem->get_contents($manifest_path);
			}

			if (false === $contents || '' === $contents) {
				self::log_debug(sprintf('Could not read manifest file for library "%s".', $library_slug));
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			$data = json_decode($contents, true);

			if (null === $data && JSON_ERROR_NONE !== json_last_error()) {
				self::log_debug(
					sprintf(
						'JSON decode error in manifest for library "%1$s": %2$s',
						$library_slug,
						json_last_error_msg()
					)
				);
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			if (! is_array($data)) {
				self::log_debug(sprintf('Manifest for library "%s" did not decode to an array.', $library_slug));
				self::$icons_cache[$library_slug] = array();
				return array();
			}

			/**
			 * Supported manifest structures:
			 * - Top-level map: [ 'arrow-right' => [ ... ], ... ]
			 * - Top-level wrapper: [ 'icons' => [ 'arrow-right' => '<svg...>', ... ] ]
			 * - Indexed list: [ [ 'slug' => 'arrow-right', ... ], ... ]
			 */
			if (isset($data['icons']) && is_array($data['icons'])) {
				$data = $data['icons'];
			}

			$icons = array();

			// Associative array keyed by slug.
			$is_assoc = array_keys($data) !== range(0, count($data) - 1);

			if ($is_assoc) {
				foreach ($data as $slug => $icon_entry) {
					$slug = sanitize_key($slug);
					if ('' === $slug) {
						continue;
					}
					if (is_string($icon_entry)) {
						$icons[$slug] = array('svg' => $icon_entry);
						continue;
					}
					if (is_array($icon_entry)) {
						$icons[$slug] = $icon_entry;
					}
				}
			} else {
				foreach ($data as $icon_entry) {
					if (! is_array($icon_entry) || empty($icon_entry['slug'])) {
						continue;
					}
					$slug = sanitize_key($icon_entry['slug']);
					if ('' === $slug) {
						continue;
					}
					if (isset($icon_entry['svg']) && is_string($icon_entry['svg'])) {
						$icons[$slug] = $icon_entry;
						continue;
					}
					$icons[$slug] = $icon_entry;
				}
			}

			self::$icons_cache[$library_slug] = $icons;

			return $icons;
		}

		/**
		 * Extract library slug and icon slug from Elementor's icon payload.
		 *
		 * Supported shapes:
		 * - [ 'library' => 'spectre-lucide', 'value' => 'spectre-lucide arrow-right' ]
		 * - [ 'library' => 'spectre-lucide', 'value' => 'arrow-right' ]
		 * - 'arrow-right' (string) – library slug will be empty.
		 *
		 * @param array|string $icon Icon descriptor or slug.
		 * @return array{string,string} [ library_slug, icon_slug ]
		 */
		private static function extract_slug($icon) {
			$library_slug = '';
			$icon_slug    = '';

			if (is_array($icon)) {
				if (! empty($icon['library'])) {
					$library_slug = sanitize_key((string) $icon['library']);
				}

				$value = '';
				if (! empty($icon['value'])) {
					$value = (string) $icon['value'];
				} elseif (! empty($icon['icon'])) {
					$value = (string) $icon['icon'];
				}

				if ('' !== $value) {
					$parts = preg_split('/\s+/', trim($value));
					$last  = end($parts);

					$icon_slug = sanitize_key((string) $last);
				}
			} elseif (is_string($icon)) {
				$icon_slug = sanitize_key($icon);
			}

			// Strip library prefix if Elementor stored a prefixed slug.
			if (
				'' !== $library_slug &&
				'' !== $icon_slug &&
				isset(self::$libraries[$library_slug]['prefix'])
			) {
				$prefix = (string) self::$libraries[$library_slug]['prefix'];
				if ('' !== $prefix && 0 === strpos($icon_slug, $prefix)) {
					$icon_slug = sanitize_key(substr($icon_slug, strlen($prefix)));
				}
			}

			return array($library_slug, $icon_slug);
		}

		/**
		 * Prepare HTML attributes for the wrapper, ensuring class names are set.
		 *
		 * @param array  $attributes Input attributes (possibly including 'class').
		 * @param string $icon_slug  Icon slug.
		 * @param array  $library    Library config (from self::$libraries).
		 * @return array Sanitized attributes.
		 */
		private static function prepare_attributes(array $attributes, $icon_slug, array $library) {
			$prepared = array();

			$base_class = $icon_slug;
			if (! empty($library['prefix']) && is_string($library['prefix'])) {
				$base_class = $library['prefix'] . $icon_slug;
			}

			$current_class = '';
			if (isset($attributes['class'])) {
				$current_class = is_array($attributes['class'])
					? implode(' ', $attributes['class'])
					: (string) $attributes['class'];
			}

			$class_attr = trim($base_class . ' ' . $current_class);
			if ('' !== $class_attr) {
				$prepared['class'] = $class_attr;
			}

			// Copy through remaining attributes with validated names (allow aria-* and data-*).
			foreach ($attributes as $name => $value) {
				if ('class' === $name) {
					continue;
				}

				$name = (string) $name;
				if ('' === $name) {
					continue;
				}

				// Disallow event handlers (onclick, onload, etc).
				if (0 === stripos($name, 'on')) {
					continue;
				}

				// Allow common safe attribute names, including aria-* and data-*.
				if (
					! preg_match('/^(?:data|aria)-[a-z0-9_\-]+$/i', $name) &&
					! preg_match('/^[a-z][a-z0-9_\-:.]*$/i', $name)
				) {
					continue;
				}

				$prepared[$name] = $value;
			}

			return $prepared;
		}

		/**
		 * Convert an attribute array into a string for HTML output.
		 *
		 * @param array $attributes Attribute name => value.
		 * @return string Leading space + attributes, or empty string.
		 */
		private static function attributes_to_string(array $attributes) {
			if (empty($attributes)) {
				return '';
			}

			$parts = array();

			foreach ($attributes as $name => $value) {
				$name    = esc_attr($name);
				$value   = esc_attr((string) $value);
				$parts[] = sprintf('%s="%s"', $name, $value);
			}

			return ' ' . implode(' ', $parts);
		}

		/**
		 * Sanitize a tag name for the wrapper element.
		 *
		 * Restricts to reasonable tags (span, i, div).
		 *
		 * @param string $tag Requested tag.
		 * @return string Safe tag.
		 */
		private static function sanitize_tag_name($tag) {
			$tag     = strtolower((string) $tag);
			$allowed = array('span', 'i', 'div');

			if (in_array($tag, $allowed, true)) {
				return $tag;
			}

			return 'span';
		}

		/**
		 * Build an SVG string from a manifest icon entry.
		 *
		 * @param array $icon_data Manifest entry for a single icon.
		 * @return string SVG markup.
		 */
		private static function build_svg_from_manifest_icon(array $icon_data) {
			if (! empty($icon_data['svg']) && is_string($icon_data['svg'])) {
				return Spectre_Icons_SVG_Sanitizer::sanitize($icon_data['svg']);
			}

			if (! empty($icon_data['body']) && is_string($icon_data['body'])) {
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

				$attr_string = self::attributes_to_string($attrs);

				$svg = sprintf(
					'<svg%1$s>%2$s</svg>',
					$attr_string,
					$body
				);

				return Spectre_Icons_SVG_Sanitizer::sanitize($svg);
			}

			return '';
		}

		/**
		 * Internal debug logger.
		 *
		 * @param string $message Message to log.
		 * @return void
		 */
		private static function log_debug($message) {
			// Intentionally no-op to avoid error_log in production.
			unset($message);
		}
	}

endif;
