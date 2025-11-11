<?php
/**
 * Settings controller for Spectre Elementor Icons.
 *
 * @package SpectreElementorIcons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Spectre_Elementor_Icons_Settings' ) ) :
	/**
	 * Handles the WordPress settings page and stored preferences.
	 */
	final class Spectre_Elementor_Icons_Settings {

		/**
		 * Singleton instance.
		 *
		 * @var Spectre_Elementor_Icons_Settings|null
		 */
		private static $instance = null;

		/**
		 * Stored option name.
		 *
		 * @var string
		 */
		private $option_name = 'spectre_elementor_icon_tabs';

		/**
		 * Settings page slug.
		 *
		 * @var string
		 */
		private $settings_slug = 'spectre-elementor-icons';

		/**
		 * List of available tabs.
		 *
		 * @var array
		 */
		private $tabs = [];

		/**
		 * Retrieve the singleton.
		 *
		 * @return Spectre_Elementor_Icons_Settings
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
			$this->tabs = apply_filters( 'spectre_elementor_icon_tabs', $this->get_default_tabs() );

			add_action( 'admin_menu', [ $this, 'register_menu' ] );
			add_action( 'admin_init', [ $this, 'register_settings' ] );
		}

		/**
		 * Register the plugin options page.
		 */
		public function register_menu() {
			add_options_page(
				__( 'Spectre Icons', 'spectre-elementor-icons' ),
				__( 'Spectre Icons', 'spectre-elementor-icons' ),
				'manage_options',
				$this->settings_slug,
				[ $this, 'render_settings_page' ]
			);
		}

		/**
		 * Register WordPress settings API bits.
		 */
		public function register_settings() {
			register_setting(
				$this->settings_slug,
				$this->option_name,
				[
					'type'              => 'array',
					'sanitize_callback' => [ $this, 'sanitize_tabs' ],
					'default'           => $this->get_default_option_values(),
				]
			);

			add_settings_section(
				'spectre_icon_tabs_section',
				__( 'Icon Picker Tabs', 'spectre-elementor-icons' ),
				function () {
					echo '<p>' . esc_html__( 'Toggle which tabs stay visible within Elementor’s icon picker.', 'spectre-elementor-icons' ) . '</p>';
				},
				$this->settings_slug
			);

			add_settings_field(
				'spectre_icon_tabs_field',
				__( 'Available Tabs', 'spectre-elementor-icons' ),
				[ $this, 'render_tabs_field' ],
				$this->settings_slug,
				'spectre_icon_tabs_section'
			);
		}

		/**
		 * Provide default Elementor tabs.
		 *
		 * @return array
		 */
		private function get_default_tabs() {
			return [
				'fa-solid'   => [
					'label'       => __( 'Font Awesome Solid', 'spectre-elementor-icons' ),
					'description' => __( 'Solid weight Font Awesome icons.', 'spectre-elementor-icons' ),
				],
				'fa-regular' => [
					'label'       => __( 'Font Awesome Regular', 'spectre-elementor-icons' ),
					'description' => __( 'Regular weight Font Awesome icons.', 'spectre-elementor-icons' ),
				],
				'fa-brands'  => [
					'label'       => __( 'Font Awesome Brands', 'spectre-elementor-icons' ),
					'description' => __( 'Brand icons provided by Font Awesome.', 'spectre-elementor-icons' ),
				],
			];
		}

		/**
		 * Default stored option values (all tabs enabled).
		 *
		 * @return array
		 */
		private function get_default_option_values() {
			$defaults = [];

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
			return wp_parse_args(
				get_option( $this->option_name, $this->get_default_option_values() ),
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
			$sanitized = [];

			foreach ( $this->tabs as $slug => $tab ) {
				$sanitized[ $slug ] = ! empty( $value[ $slug ] );
			}

			return $sanitized;
		}

		/**
		 * Render the checkbox UI for the tabs.
		 */
		public function render_tabs_field() {
			$options = $this->get_tab_preferences();

			echo '<div class="spectre-elementor-tabs-grid">';

			foreach ( $this->tabs as $slug => $tab ) {
				$field_id = 'spectre-tab-' . esc_attr( $slug );
				$checked  = ! empty( $options[ $slug ] );
				?>
				<label for="<?php echo esc_attr( $field_id ); ?>" class="spectre-elementor-tabs-grid__item">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $slug ); ?>]"
						id="<?php echo esc_attr( $field_id ); ?>"
						value="1"
						<?php checked( $checked ); ?>
					/>
					<span class="spectre-elementor-tabs-grid__label"><?php echo esc_html( $tab['label'] ); ?></span>
					<?php if ( ! empty( $tab['description'] ) ) : ?>
						<span class="spectre-elementor-tabs-grid__description">
							<?php echo esc_html( $tab['description'] ); ?>
						</span>
					<?php endif; ?>
				</label>
				<?php
			}

			echo '</div>';
			echo '<style>
				.spectre-elementor-tabs-grid {
					display: grid;
					gap: 12px;
					grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
					margin-top: 1rem;
				}

				.spectre-elementor-tabs-grid__item {
					background: #fff;
					border: 1px solid #ccd0d4;
					border-radius: 6px;
					padding: 12px;
					display: flex;
					flex-direction: column;
					gap: 6px;
				}

				.spectre-elementor-tabs-grid__label {
					font-weight: 600;
				}

				.spectre-elementor-tabs-grid__description {
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
				<h1><?php esc_html_e( 'Spectre Elementor Icons', 'spectre-elementor-icons' ); ?></h1>
				<?php settings_errors( $this->settings_slug ); ?>
				<form method="post" action="options.php">
					<?php
					settings_fields( $this->settings_slug );
					do_settings_sections( $this->settings_slug );
					submit_button( __( 'Save Tab Visibility', 'spectre-elementor-icons' ) );
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
