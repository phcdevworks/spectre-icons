<?php
/**

 * Coordinates Spectre icon libraries with Elementor.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Spectre_Icons_Elementor_Library_Manager' ) ) :
	/**
	 * Loads Spectre libraries and registers them with Elementor.
	 */
	final class Spectre_Icons_Elementor_Library_Manager {


		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Icons_Elementor_Library_Manager|null
		 */
		private static $instance = null;

		/**
		 * Settings handler.
		 *
		 * @var Spectre_Icons_Elementor_Settings
		 */
		private $settings;

		/**
		 * Known Spectre libraries.
		 *
		 * @var array
		 */
		private $libraries = array();

		/**
		 * Retrieve the singleton.
		 *
		 * @param Spectre_Icons_Elementor_Settings $settings Settings dependency.
		 *
		 * @return Spectre_Icons_Elementor_Library_Manager
		 */
		public static function instance( Spectre_Icons_Elementor_Settings $settings ) {
			if ( null === self::$instance ) {
				self::$instance = new self( $settings );
			}

			return self::$instance;
		}

		/**
		 * Wire up filters.
		 *
		 * @param Spectre_Icons_Elementor_Settings $settings Settings dependency.
		 */
		private function __construct( Spectre_Icons_Elementor_Settings $settings ) {
			$this->settings  = $settings;
			$this->libraries = $this->load_libraries();

			$this->settings->set_tabs( $this->get_settings_metadata() );

			add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'register_additional_tabs' ) );
		}

		/**
		 * Pull the Spectre libraries from filter consumers.
		 *
		 * @return array
		 */
		private function load_libraries() {
			$libraries = apply_filters( 'spectre_icons_elementor_icon_libraries', array() );
			$libraries = apply_filters( 'spectre_elementor_icon_libraries', $libraries );

			if ( empty( $libraries ) || ! is_array( $libraries ) ) {
				return array();
			}

			$normalized = array();

			foreach ( $libraries as $slug => $library ) {
				$slug = sanitize_key( $slug );

				if ( empty( $slug ) || empty( $library['label'] ) || empty( $library['config'] ) ) {
					continue;
				}

				$normalized[ $slug ] = array(
					'label'       => sanitize_text_field( $library['label'] ),
					'description' => isset( $library['description'] ) ? wp_kses_post( $library['description'] ) : '',
					'config'      => (array) $library['config'],
				);
			}

			return $normalized;
		}

		/**
		 * Return the subset of data the settings page needs.
		 *
		 * @return array
		 */
		private function get_settings_metadata() {
			$settings_tabs = array();

			foreach ( $this->libraries as $slug => $library ) {
				$settings_tabs[ $slug ] = array(
					'label'       => $library['label'],
					'description' => $library['description'],
				);
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
		public function register_additional_tabs( $tabs ) {
			$preferences = $this->settings->get_tab_preferences();

			foreach ( $this->libraries as $slug => $library ) {
				// Default to enabled if no preference set yet.
				$is_enabled = isset( $preferences[ $slug ] ) ? $preferences[ $slug ] : true;
				if ( ! $is_enabled ) {
					continue;
				}

				$tabs[ $slug ] = $library['config'];
			}

			return $tabs;
		}
	}
endif;
