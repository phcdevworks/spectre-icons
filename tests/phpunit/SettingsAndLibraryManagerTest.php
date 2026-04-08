<?php

declare(strict_types=1);

final class SettingsAndLibraryManagerTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_settings_sanitize_tabs_whitelists_known_libraries_and_persists_false_values(): void {
		$settings = new Spectre_Icons_Elementor_Settings();

		$sanitized = $settings->sanitize_tabs(
			array(
				'spectre-lucide'      => '1',
				'spectre-fontawesome' => '',
				'unknown-library'     => '1',
			)
		);

		$this->assertSame(
			array(
				'spectre-lucide'      => true,
				'spectre-fontawesome' => false,
			),
			$sanitized
		);
	}

	public function test_library_manager_drops_invalid_libraries_and_hides_disabled_picker_icons(): void {
		update_option(
			'spectre_icons_elementor_tabs',
			array(
				'custom-library' => false,
			)
		);

		add_filter(
			'spectre_icons_elementor_icon_libraries',
			static function ( $libraries ) {
				$libraries['custom-library'] = array(
					'label'  => 'Custom Library',
					'config' => array(
						'name'            => 'custom-library',
						'label'           => 'Custom Library',
						'labelIcon'       => 'eicon-star',
						'prefix'          => 'spectre-custom-',
						'icons'           => array( 'one', 'two' ),
						'render_callback' => 'strval',
					),
				);

				$libraries['broken-library'] = array(
					'label'  => 'Broken Library',
					'config' => array(
						'name'  => 'broken-library',
						'icons' => array( 'x' ),
					),
				);

				return $libraries;
			}
		);

		$settings = new Spectre_Icons_Elementor_Settings();
		$manager  = Spectre_Icons_Elementor_Library_Manager::instance( $settings );
		$tabs     = $manager->register_additional_tabs( array() );

		$this->assertArrayHasKey( 'custom-library', $tabs );
		$this->assertArrayNotHasKey( 'broken-library', $tabs );
		$this->assertSame( array(), $tabs['custom-library']['icons'] );
		$this->assertSame( 'spectre-custom-', $tabs['custom-library']['prefix'] );
		$this->assertSame( 'eicon-star', $tabs['custom-library']['labelIcon'] );
		$this->assertSame( 'strval', $tabs['custom-library']['render_callback'] );
	}
}
