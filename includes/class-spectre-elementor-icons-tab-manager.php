<?php
/**
 * Hooks Elementor icon picker tabs to saved settings.
 *
 * @package SpectreElementorIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Spectre_Elementor_Icons_Tab_Manager' ) ) :
	/**
	 * Applies stored preferences to Elementor's icon picker tabs.
	 */
	final class Spectre_Elementor_Icons_Tab_Manager {

		/**
		 * Settings class.
		 *
		 * @var Spectre_Elementor_Icons_Settings
		 */
		private $settings;

		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Elementor_Icons_Tab_Manager|null
		 */
		private static $instance = null;

		/**
		 * Retrieve instance.
		 *
		 * @param Spectre_Elementor_Icons_Settings $settings Settings dependency.
		 *
		 * @return Spectre_Elementor_Icons_Tab_Manager
		 */
		public static function instance( Spectre_Elementor_Icons_Settings $settings ) {
			if ( null === self::$instance ) {
				self::$instance = new self( $settings );
			}

			return self::$instance;
		}

		/**
		 * Hook filters.
		 *
		 * @param Spectre_Elementor_Icons_Settings $settings Settings dependency.
		 */
		private function __construct( Spectre_Elementor_Icons_Settings $settings ) {
			$this->settings = $settings;

			add_filter( 'elementor/icons_manager/native', [ $this, 'filter_native_tabs' ], 20 );
			add_filter( 'elementor/icons_manager/native_tabs', [ $this, 'filter_native_tabs' ], 20 );
		}

		/**
		 * Remove tabs that are disabled in the settings.
		 *
		 * @param array $tabs Elementor native tabs.
		 *
		 * @return array
		 */
		public function filter_native_tabs( $tabs ) {
			$preferences = $this->settings->get_tab_preferences();
			$known_tabs  = $this->settings->get_tabs();

			foreach ( $known_tabs as $slug => $tab ) {
				$is_enabled = ! empty( $preferences[ $slug ] );

				if ( ! $is_enabled && isset( $tabs[ $slug ] ) ) {
					unset( $tabs[ $slug ] );
				}
			}

			return $tabs;
		}
	}
endif;
