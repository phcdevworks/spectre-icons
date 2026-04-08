<?php

declare(strict_types=1);

final class IconLibrariesTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_library_preferences_default_to_enabled_for_known_libraries(): void {
		update_option(
			'spectre_icons_elementor_tabs',
			array(
				'spectre-lucide'      => false,
				'spectre-fontawesome' => true,
				'unknown-library'     => false,
			)
		);

		$preferences = spectre_icons_elementor_get_icon_library_preferences();

		$this->assertSame(
			array(
				'spectre-lucide'      => false,
				'spectre-fontawesome' => true,
			),
			$preferences
		);
	}

	public function test_preview_config_uses_real_manifest_files_and_renderer_callback(): void {
		$config = spectre_icons_elementor_get_icon_preview_config();

		$this->assertArrayHasKey( 'spectre-lucide', $config );
		$this->assertArrayHasKey( 'spectre-fontawesome', $config );
		$this->assertSame(
			array( 'Spectre_Icons_Elementor_Manifest_Renderer', 'render_icon' ),
			$config['spectre-lucide']['render_callback']
		);
		$this->assertStringEndsWith(
			'assets/manifests/spectre-lucide.json',
			wp_normalize_path( $config['spectre-lucide']['manifest'] )
		);
		$this->assertSame( 'spectre-lucide-', $config['spectre-lucide']['prefix'] );
	}

	public function test_register_manifest_libraries_loads_icon_lists_from_bundled_manifests(): void {
		$libraries = spectre_icons_elementor_register_manifest_libraries( array() );

		$this->assertArrayHasKey( 'spectre-lucide', $libraries );
		$this->assertArrayHasKey( 'spectre-fontawesome', $libraries );
		$this->assertNotEmpty( $libraries['spectre-lucide']['config']['icons'] );
		$this->assertNotEmpty( $libraries['spectre-fontawesome']['config']['icons'] );
		$this->assertContains( 'alarm-clock', $libraries['spectre-lucide']['config']['icons'] );
	}
}
