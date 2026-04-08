<?php

declare(strict_types=1);

final class IntegrationHooksTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_enqueue_styles_adds_hide_css_for_disabled_libraries(): void {
		update_option(
			'spectre_icons_elementor_tabs',
			array(
				'spectre-lucide'      => false,
				'spectre-fontawesome' => true,
			)
		);

		spectre_icons_elementor_enqueue_styles();

		$this->assertArrayHasKey( 'spectre-icons-elementor', $GLOBALS['spectre_wp_styles'] );
		$this->assertStringContainsString( '[data-library="spectre-lucide"]', $GLOBALS['spectre_wp_inline_styles']['spectre-icons-elementor'] );
		$this->assertStringNotContainsString( 'spectre-fontawesome', $GLOBALS['spectre_wp_inline_styles']['spectre-icons-elementor'] );
	}

	public function test_enqueue_icon_scripts_localizes_preview_library_config(): void {
		update_option(
			'spectre_icons_elementor_tabs',
			array(
				'spectre-lucide'      => true,
				'spectre-fontawesome' => false,
			)
		);

		$GLOBALS['spectre_wp_scripts']['wp-auth-check'] = array( 'handle' => 'wp-auth-check' );

		spectre_icons_elementor_enqueue_icon_scripts();

		$this->assertArrayHasKey( 'spectre-icons-elementor-js', $GLOBALS['spectre_wp_scripts'] );
		$this->assertArrayNotHasKey( 'wp-auth-check', $GLOBALS['spectre_wp_scripts'] );
		$this->assertArrayHasKey( 'spectre-icons-elementor-js', $GLOBALS['spectre_wp_localized_scripts'] );

		$config = $GLOBALS['spectre_wp_localized_scripts']['spectre-icons-elementor-js']['data'];

		$this->assertSame( 'SpectreIconsElementorConfig', $GLOBALS['spectre_wp_localized_scripts']['spectre-icons-elementor-js']['object_name'] );
		$this->assertArrayHasKey( 'spectre-lucide', $config['libraries'] );
		$this->assertArrayHasKey( 'spectre-fontawesome', $config['libraries'] );
		$this->assertTrue( $config['libraries']['spectre-lucide']['enabled'] );
		$this->assertFalse( $config['libraries']['spectre-fontawesome']['enabled'] );
		$this->assertSame( 'outline', $config['libraries']['spectre-lucide']['style'] );
		$this->assertSame( 'filled', $config['libraries']['spectre-fontawesome']['style'] );
	}
}
