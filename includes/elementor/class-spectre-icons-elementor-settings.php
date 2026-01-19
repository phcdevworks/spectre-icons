<?php

/**
 * Settings handler for Spectre Icons → Elementor integration.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Spectre_Icons_Elementor_Settings')) :

	/**
	 * Manages admin settings for which Spectre icon libraries are enabled.
	 */
	final class Spectre_Icons_Elementor_Settings {

		/**
		 * Option name stored in wp_options.
		 *
		 * @var string
		 */
		private $option_name = 'spectre_icons_elementor_tabs';

		/**
		 * Cached tabs.
		 *
		 * @var array|null
		 */
		private $tabs_cache = null;

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action('admin_init', array($this, 'register_settings'));
			add_action('admin_menu', array($this, 'register_menu_page'));
		}

		/**
		 * Register WP settings field + sanitization.
		 *
		 * @return void
		 */
		public function register_settings() {
			register_setting(
				'spectre_icons_elementor',
				$this->option_name,
				array($this, 'sanitize_tabs')
			);

			add_settings_section(
				'spectre_icons_elementor_section',
				__('Spectre Icons: Elementor Libraries', 'spectre-icons'),
				'__return_false',
				'spectre_icons_elementor'
			);

			add_settings_field(
				'spectre_icons_elementor_tabs_field',
				__('Enabled Icon Libraries', 'spectre-icons'),
				array($this, 'render_tabs_field'),
				'spectre_icons_elementor',
				'spectre_icons_elementor_section'
			);
		}

		/**
		 * Register admin settings page.
		 *
		 * @return void
		 */
		public function register_menu_page() {
			add_options_page(
				__('Spectre Icons - Elementor', 'spectre-icons'),
				__('Spectre Icons', 'spectre-icons'),
				'manage_options',
				'spectre-icons-elementor',
				array($this, 'render_settings_page')
			);
		}

		/**
		 * Sanitize library preference values.
		 *
		 * Whitelists keys to only known library slugs from the filter.
		 *
		 * @param mixed $value Raw input.
		 * @return array Sanitized prefs.
		 */
		public function sanitize_tabs($value) {
			if (! is_array($value)) {
				return array();
			}

			// Whitelist known library slugs.
			$libraries = apply_filters('spectre_icons_elementor_icon_libraries', array());
			$allowed   = array();

			if (is_array($libraries)) {
				foreach ($libraries as $slug => $lib) {
					$slug = sanitize_key($slug);
					if ('' !== $slug) {
						$allowed[$slug] = true;
					}
				}
			}

			$clean = array();

			foreach ($value as $slug => $enabled) {
				$slug = sanitize_key($slug);
				if ('' === $slug || ! isset($allowed[$slug])) {
					continue;
				}

				$clean[$slug] = (bool) $enabled;
			}

			// Reset cached prefs after save.
			$this->tabs_cache = null;

			return $clean;
		}

		/**
		 * Render the tab checkbox field.
		 *
		 * @return void
		 */
		public function render_tabs_field() {
			$libraries = apply_filters('spectre_icons_elementor_icon_libraries', array());
			$prefs     = $this->get_tabs();

			if (empty($libraries) || ! is_array($libraries)) {
				echo '<p>' . esc_html__('No icon libraries available.', 'spectre-icons') . '</p>';
				return;
			}

			echo '<div class="spectre-icons-settings-list">';

			foreach ($libraries as $slug => $lib) {
				$slug = sanitize_key($slug);
				if ('' === $slug || ! is_array($lib)) {
					continue;
				}

				$label_raw = isset($lib['label']) ? (string) $lib['label'] : ucfirst($slug);
				$label     = wp_strip_all_tags($label_raw);

				$enabled = isset($prefs[$slug]) ? (bool) $prefs[$slug] : true;

				printf(
					'<label class="spectre-icons-setting-item">
						<input type="checkbox" name="%1$s[%2$s]" value="1" %3$s />
						%4$s
					</label>',
					esc_attr($this->option_name),
					esc_attr($slug),
					checked($enabled, true, false),
					esc_html($label)
				);
			}

			echo '</div>';
		}

		/**
		 * Fetch enabled/disabled libraries.
		 *
		 * @return array<string,bool>
		 */
		public function get_tabs() {
			if (null !== $this->tabs_cache) {
				return $this->tabs_cache;
			}

			$stored = get_option($this->option_name, array());
			if (! is_array($stored)) {
				$stored = array();
			}

			$prefs = array();

			foreach ($stored as $slug => $enabled) {
				$slug = sanitize_key($slug);
				if ('' === $slug) {
					continue;
				}

				$prefs[$slug] = (bool) $enabled;
			}

			$this->tabs_cache = $prefs;
			return $prefs;
		}

		/**
		 * Render full settings admin page.
		 *
		 * @return void
		 */
		public function render_settings_page() {
			if (! current_user_can('manage_options')) {
				return;
			}

			echo '<div class="wrap">';
			echo '<h1>' . esc_html__('Spectre Icons – Elementor Integration', 'spectre-icons') . '</h1>';

			echo '<form method="post" action="options.php">';
			settings_fields('spectre_icons_elementor');
			do_settings_sections('spectre_icons_elementor');
			submit_button();
			echo '</form>';

			echo '</div>';
		}
	}

endif;
