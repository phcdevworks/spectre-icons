<?php
/**
 * Settings controller for Spectre Icons Elementor.
 *
 * @package SpectreIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Spectre_Icons_Elementor_Settings' ) ) :
	/**
	 * Handles the WordPress settings page and stored preferences.
	 */
	final class Spectre_Icons_Elementor_Settings {

		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Icons_Elementor_Settings|null
		 */
		private static $instance = null;

		/**
		 * Stored option name.
		 *
		 * @var string
		 */
		private $option_name = 'spectre_icons_elementor_icon_tabs';

		/**
		 * Legacy option names to migrate from silently.
		 *
		 * @var array
		 */
		private $legacy_option_names = array(
			'spectre_elementor_icon_tabs',
		);

		/**
		 * Settings page slug.
		 *
		 * @var string
		 */
		private $settings_slug = 'spectre-icons';

		/**
		 * List of available tabs.
		 *
		 * @var array
		 */
		private $tabs = array();

		/**
		 * Retrieve the singleton.
		 *
		 * @return Spectre_Icons_Elementor_Settings
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Setup hooks.
		 */
		private function __construct() {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Register the plugin options page.
		 */
		public function register_menu() {
			add_options_page(
				__( 'Spectre Icons', 'spectre-icons' ),
				__( 'Spectre Icons', 'spectre-icons' ),
				'manage_options',
				$this->settings_slug,
				array( $this, 'render_settings_page' )
			);
		}

		/**
		 * Register WordPress settings API bits.
		 */
		public function register_settings() {
			register_setting(
				$this->settings_slug,
				$this->option_name,
				array(
					'type'              => 'array',
					'sanitize_callback' => array( $this, 'sanitize_tabs' ),
					'default'           => $this->get_default_option_values(),
				)
			);

			add_settings_section(
				'spectre_icon_tabs_section',
				__( 'Icon Picker Tabs', 'spectre-icons' ),
				function () {
					echo '<p>' . esc_html__( 'Toggle which tabs stay visible within Elementor’s icon picker.', 'spectre-icons' ) . '</p>';
				},
				$this->settings_slug
			);

			add_settings_field(
				'spectre_icon_tabs_field',
				__( 'Available Tabs', 'spectre-icons' ),
				array( $this, 'render_tabs_field' ),
				$this->settings_slug,
				'spectre_icon_tabs_section'
			);
		}

		/**
		 * Inject the dynamic list of Spectre libraries.
		 *
		 * @param array $tabs Spectre library metadata.
		 */
		public function set_tabs( array $tabs ) {
			$this->tabs = $tabs;
		}

		/**
		 * Default stored option values (all tabs enabled).
		 *
		 * @return array
		 */
		private function get_default_option_values() {
			$defaults = array();

			foreach ( $this->tabs as $slug => $tab ) {
				$defaults[ $slug ] = true;
			}

			return $defaults;
		}

		/**
		 * Get the saved preferences merged with defaults.
		 *
		 * @return array
		 */
		public function get_tab_preferences() {
			$stored = get_option( $this->option_name, null );

			if ( null === $stored ) {
				foreach ( $this->legacy_option_names as $legacy_option ) {
					$legacy_value = get_option( $legacy_option, null );

					if ( null !== $legacy_value ) {
						$stored = $legacy_value;
						break;
					}
				}
			}

			if ( ! is_array( $stored ) ) {
				$stored = array();
			}

			return wp_parse_args(
				$stored,
				$this->get_default_option_values()
			);
		}

		/**
		 * Sanitize checkbox submission.
		 *
		 * @param array $value Raw submitted values.
		 *
		 * @return array
		 */
		public function sanitize_tabs( $value ) {
			$value     = (array) $value;
			$sanitized = array();

			foreach ( $this->tabs as $slug => $tab ) {
				$sanitized[ $slug ] = ! empty( $value[ $slug ] );
			}

			return $sanitized;
		}

		/**
		 * Render the checkbox UI for the tabs.
		 */
		public function render_tabs_field() {
			if ( empty( $this->tabs ) ) {
				echo '<p>' . esc_html__( 'No Spectre icon libraries are available yet.', 'spectre-icons' ) . '</p>';
				return;
			}

			$options = $this->get_tab_preferences();

			echo '<div class="spectre-icons-tabs-grid">';

			foreach ( $this->tabs as $slug => $tab ) {
				$field_id = 'spectre-tab-' . esc_attr( $slug );
				$checked  = ! empty( $options[ $slug ] );
				?>
				<label for="<?php echo esc_attr( $field_id ); ?>" class="spectre-icons-tabs-grid__item">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $slug ); ?>]"
						id="<?php echo esc_attr( $field_id ); ?>"
						value="1"
						<?php checked( $checked ); ?>
					/>
					<span class="spectre-icons-tabs-grid__label"><?php echo esc_html( $tab['label'] ); ?></span>
					<?php if ( ! empty( $tab['description'] ) ) : ?>
						<span class="spectre-icons-tabs-grid__description">
							<?php echo esc_html( $tab['description'] ); ?>
						</span>
					<?php endif; ?>
				</label>
				<?php
			}

			echo '</div>';
			echo '<style>
				.spectre-icons-tabs-grid {
					display: grid;
					gap: 12px;
					grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
					margin-top: 1rem;
				}

				.spectre-icons-tabs-grid__item {
					background: #fff;
					border: 1px solid #ccd0d4;
					border-radius: 6px;
					padding: 12px;
					display: flex;
					flex-direction: column;
					gap: 6px;
				}

				.spectre-icons-tabs-grid__label {
					font-weight: 600;
				}

				.spectre-icons-tabs-grid__description {
					color: #50575e;
					font-size: 13px;
				}
			</style>';
		}

		/**
		 * Display the actual settings page.
		 */
		public function render_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Spectre Icons — Elementor', 'spectre-icons' ); ?></h1>
				<?php settings_errors( $this->settings_slug ); ?>
				<form method="post" action="options.php">
					<?php
					settings_fields( $this->settings_slug );
					do_settings_sections( $this->settings_slug );
					submit_button( __( 'Save Tab Visibility', 'spectre-icons' ) );
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Expose the raw tab definitions.
		 *
		 * @return array
		 */
		public function get_tabs() {
			return $this->tabs;
		}
	}
endif;
