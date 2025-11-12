<?php

/**
 * Coordinates custom Spectre icon libraries with Elementor.
 *
 * @package SpectreElementorIcons
 */

if (! defined('ABSPATH')) {
	exit;
}


if (! class_exists('Spectre_Elementor_Icons_Library_Manager')) :
	/**
	 * Loads Spectre libraries and registers them with Elementor.
	 */
	final class Spectre_Elementor_Icons_Library_Manager
	{

		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Elementor_Icons_Library_Manager|null
		 */
		private static $instance = null;

		/**
		 * Settings handler.
		 *
		 * @var Spectre_Elementor_Icons_Settings
		 */
		private $settings;

		/**
		 * Known Spectre libraries.
		 *
		 * @var array
		 */
		private $libraries = [];

		/**
		 * Retrieve the singleton.
		 *
		 * @param Spectre_Elementor_Icons_Settings $settings Settings dependency.
		 *
		 * @return Spectre_Elementor_Icons_Library_Manager
		 */
		public static function instance(Spectre_Elementor_Icons_Settings $settings)
		{
			if (null === self::$instance) {
				self::$instance = new self($settings);
			}

			return self::$instance;
		}

		/**
		 * Wire up filters.
		 *
		 * @param Spectre_Elementor_Icons_Settings $settings Settings dependency.
		 */
		private function __construct(Spectre_Elementor_Icons_Settings $settings)
		{
			$this->settings  = $settings;
			$this->libraries = $this->load_libraries();

			$this->settings->set_tabs($this->get_settings_metadata());

			add_filter('elementor/icons_manager/additional_tabs', [$this, 'register_additional_tabs']);
		}

		/**
		 * Pull the Spectre libraries from filter consumers.
		 *
		 * @return array
		 */
		private function load_libraries()
		{
			$libraries = apply_filters('spectre_elementor_icon_libraries', []);

			if (empty($libraries) || ! is_array($libraries)) {
				return [];
			}

			$normalized = [];

			foreach ($libraries as $slug => $library) {
				$slug = sanitize_key($slug);

				if (empty($slug) || empty($library['label']) || empty($library['config'])) {
					continue;
				}

				$normalized[$slug] = [
					'label'       => sanitize_text_field($library['label']),
					'description' => isset($library['description']) ? wp_kses_post($library['description']) : '',
					'config'      => (array) $library['config'],
				];
			}

			return $normalized;
		}

		/**
		 * Return the subset of data the settings page needs.
		 *
		 * @return array
		 */
		private function get_settings_metadata()
		{
			$settings_tabs = [];

			foreach ($this->libraries as $slug => $library) {
				$settings_tabs[$slug] = [
					'label'       => $library['label'],
					'description' => $library['description'],
				];
			}

			return $settings_tabs;
		}

		/**
		 * Register enabled libraries with Elementor.
		 *
		 * @param array $tabs Existing Elementor tabs.
		 *
		 * @return array
		 */
		public function register_additional_tabs($tabs)
		{
			$preferences = $this->settings->get_tab_preferences();

			foreach ($this->libraries as $slug => $library) {
				// Default to enabled if no preference set yet
				$is_enabled = isset($preferences[$slug]) ? $preferences[$slug] : true;

				if (! $is_enabled) {
					continue;
				}

				$tabs[$slug] = $library['config'];
			}

			return $tabs;
		}
	}
endif;
