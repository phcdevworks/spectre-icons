<?php

/**
 * Coordinates Spectre icon libraries with Elementor.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Spectre_Icons_Elementor_Library_Manager')) :

	/**
	 * Manages Spectre icon libraries and exposes them to Elementor.
	 *
	 * Responsibilities:
	 * - Load icon library definitions via filters.
	 * - Validate library configuration to avoid fatal errors.
	 * - Apply user preferences from Settings to determine which libraries are active.
	 * - Register the active libraries as Elementor icon tabs.
	 */
	final class Spectre_Icons_Elementor_Library_Manager {

		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Icons_Elementor_Library_Manager|null
		 */
		private static $instance = null;

		/**
		 * Settings instance.
		 *
		 * @var Spectre_Icons_Elementor_Settings
		 */
		private $settings;

		/**
		 * Loaded icon libraries, keyed by slug.
		 *
		 * @var array<string, array>
		 */
		private $libraries = array();

		/**
		 * Private constructor - use instance().
		 *
		 * @param Spectre_Icons_Elementor_Settings $settings Settings controller.
		 */
		private function __construct(Spectre_Icons_Elementor_Settings $settings) {
			$this->settings = $settings;
			$this->load_libraries();
		}

		/**
		 * Get the singleton instance.
		 *
		 * @param Spectre_Icons_Elementor_Settings $settings Settings controller.
		 * @return Spectre_Icons_Elementor_Library_Manager
		 */
		public static function instance(Spectre_Icons_Elementor_Settings $settings) {
			// If we already have an instance, but a different settings object is passed, log it.
			if (null !== self::$instance && self::$instance->settings !== $settings) {
				self::log_debug('Library Manager instantiated more than once with different Settings instance.');
			}

			if (null === self::$instance) {
				self::$instance = new self($settings);
			}

			return self::$instance;
		}

		/**
		 * Optional helper: register hooks into Elementor.
		 *
		 * Call this once during plugin bootstrap after Elementor is loaded,
		 * OR hook up register_additional_tabs() manually from your bootstrap.
		 *
		 * @return void
		 */
		public function register_hooks() {
			add_filter(
				'elementor/icons_manager/additional_tabs',
				array($this, 'register_additional_tabs')
			);
		}

		/**
		 * Load and validate icon libraries from filters.
		 *
		 * @return void
		 */
		private function load_libraries() {
			// icon-libraries.php should hook this filter.
			$libraries = apply_filters('spectre_icons_elementor_icon_libraries', array());

			if (! is_array($libraries)) {
				self::log_debug('spectre_icons_elementor_icon_libraries filter did not return an array.');
				$this->libraries = array();
				return;
			}

			$validated = array();

			foreach ($libraries as $slug => $library) {
				$sanitized_slug = sanitize_key($slug);

				if ('' === $sanitized_slug) {
					self::log_debug('Skipping library with empty or invalid slug.');
					continue;
				}

				if (! is_array($library)) {
					self::log_debug(sprintf('Skipping library "%s": definition is not an array.', $sanitized_slug));
					continue;
				}

				$validated_library = $this->validate_library_definition($sanitized_slug, $library);

				if (null === $validated_library) {
					// validate_library_definition already logged why it failed.
					continue;
				}

				$validated[$sanitized_slug] = $validated_library;
			}

			$this->libraries = $validated;
		}

		/**
		 * Validate a single library definition.
		 *
		 * Expected minimal shape:
		 *
		 * - label (string)
		 * - config (array) with:
		 *   - label (string)
		 *   - labelIcon (string)      Icon for the tab header.
		 *   - render_callback (callable|array)
		 *   - icons (array)           Icon identifiers (for previews).
		 *
		 * @param string $slug    Library slug.
		 * @param array  $library Library definition.
		 * @return array|null     Sanitized library or null on failure.
		 */
		private function validate_library_definition($slug, array $library) {
			$defaults = array(
				'label'  => '',
				'config' => array(),
			);

			$library = wp_parse_args($library, $defaults);

			if ('' === $library['label']) {
				self::log_debug(sprintf('Library "%s" missing "label".', $slug));
				return null;
			}

			if (! is_array($library['config'])) {
				self::log_debug(sprintf('Library "%s" config must be an array.', $slug));
				return null;
			}

			$config = wp_parse_args(
				$library['config'],
				array(
					'name'            => $slug,
					'label'           => $library['label'],
					'labelIcon'       => '',
					'manifest'        => '',
					'prefix'          => '',
					'icons'           => array(),
					'render_callback' => null,
					'native'          => false,
					'ver'             => '0.1.0',
				)
			);

			// Basic checks to avoid Elementor-side errors.
			if ('' === $config['label']) {
				self::log_debug(sprintf('Library "%s" config missing \"label\".', $slug));
				return null;
			}

			if (empty($config['render_callback']) || ! is_callable($config['render_callback'])) {
				self::log_debug(sprintf('Library "%s" has invalid or missing render_callback.', $slug));
				return null;
			}

			if (! is_array($config['icons'])) {
				self::log_debug(sprintf('Library "%s" icons must be an array.', $slug));
				$config['icons'] = array();
			}

			// Sanitize some string fields.
			$config['name']  = (is_string($config['name']) && '' !== $config['name'])
				? sanitize_key($config['name'])
				: $slug;

			$config['label'] = wp_strip_all_tags((string) $config['label']);

			// Allow only Elementor's eicon-* tokens.
			if (is_string($config['labelIcon']) && preg_match('/^eicon-[a-z0-9\-]+$/', $config['labelIcon'])) {
				$config['labelIcon'] = $config['labelIcon'];
			} else {
				$config['labelIcon'] = '';
			}

			// Preserve hyphens/trailing hyphen for class prefixes like "spectre-lucide-".
			$config['prefix'] = is_string($config['prefix'])
				? preg_replace('/[^a-z0-9\-_]/i', '', (string) $config['prefix'])
				: '';

			$library['config'] = $config;
			return $library;
		}

		/**
		 * Filter callback: registers Spectre icon tabs with Elementor.
		 *
		 * @param array $tabs Existing tabs from Elementor and other plugins.
		 * @return array Modified tabs including enabled Spectre libraries.
		 */
		public function register_additional_tabs($tabs) {
			if (! is_array($tabs)) {
				$tabs = array();
			}

			// If we don't have any valid libraries, just return what we were given.
			if (empty($this->libraries)) {
				return $tabs;
			}

			$preferences = $this->settings->get_tabs();

			if (! is_array($preferences)) {
				$preferences = array();
			}

			foreach ($this->libraries as $slug => $library) {

				// Safely fetch library configuration.
				if (empty($library['config']) || ! is_array($library['config'])) {
					self::log_debug(sprintf('Library "%s" skipped: missing config.', $slug));
					continue;
				}

				// Default to enabled if no preference has been saved yet.
				$is_enabled = isset($preferences[$slug]) ? (bool) $preferences[$slug] : true;

				if (! $is_enabled) {
					continue;
				}

				// Do not clobber existing tabs with the same slug.
				if (isset($tabs[$slug])) {
					self::log_debug(sprintf('Tab slug "%s" already exists, skipping Spectre library.', $slug));
					continue;
				}

				$tabs[$slug] = $library['config'];
			}

			return $tabs;
		}

		/**
		 * Internal debug logging helper.
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
