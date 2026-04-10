<?php

declare(strict_types=1);

final class ManifestRendererTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_render_icon_sanitizes_manifest_svg_and_preserves_wrapper_attributes(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'arrow-right' => array(
						'svg' => '<svg xmlns="http://www.w3.org/2000/svg" onclick="bad()"><script>alert(1)</script><path d="M0 0" /></svg>',
					),
				),
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			'test-fontawesome',
			$manifest_path,
			array( 'prefix' => 'spectre-test-' )
		);

		$html = Spectre_Icons_Elementor_Manifest_Renderer::render_icon(
			array(
				'library' => 'test-fontawesome',
				'value'   => 'test-fontawesome arrow-right',
			),
			array(
				'class'     => 'extra-class',
				'data-test' => 'preview',
			),
			'div'
		);

		$this->assertStringStartsWith( '<div ', $html );
		$this->assertStringContainsString( 'class="spectre-test-arrow-right extra-class spectre-icon--style-filled"', $html );
		$this->assertStringContainsString( 'data-test="preview"', $html );
		$this->assertStringContainsString( '<svg', $html );
		$this->assertStringNotContainsString( '<script', $html );
		$this->assertStringNotContainsString( 'onclick=', $html );
	}

	public function test_renderer_supports_indexed_body_manifests_and_outline_style_class(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				array(
					'slug' => 'camera',
					'body' => '<path d="M1 1" />',
				),
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			'custom-lucide',
			$manifest_path,
			array( 'prefix' => 'spectre-lucide-' )
		);

		$this->assertSame(
			array( 'camera' ),
			Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'custom-lucide' )
		);

		$html = Spectre_Icons_Elementor_Manifest_Renderer::render_icon(
			array(
				'library' => 'custom-lucide',
				'value'   => 'spectre-lucide-camera',
			)
		);

		$this->assertStringContainsString( 'spectre-icon--style-outline', $html );
		$this->assertStringContainsString( 'class="spectre-lucide-camera spectre-icon--style-outline"', $html );
		$this->assertStringContainsString( 'stroke="currentColor"', $html );
	}

	public function test_renderer_prioritizes_style_option_over_slug_fallback(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'icon1' => array( 'svg' => '<svg><path d="M0 0" /></svg>' ),
				),
			)
		);

		// slug contains 'lucide' but we force 'filled' style.
		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest(
			'forced-filled-lucide',
			$manifest_path,
			array(
				'options' => array( 'style' => 'filled' ),
			)
		);

		$html = Spectre_Icons_Elementor_Manifest_Renderer::render_icon(
			array(
				'library' => 'forced-filled-lucide',
				'value'   => 'icon1',
			)
		);

		$this->assertStringContainsString( 'spectre-icon--style-filled', $html );
		$this->assertStringNotContainsString( 'spectre-icon--style-outline', $html );
	}

	public function test_unknown_library_and_unknown_icon_return_empty_output(): void {
		$this->assertSame(
			'',
			Spectre_Icons_Elementor_Manifest_Renderer::render_icon(
				array(
					'library' => 'missing',
					'value'   => 'missing icon',
				)
			)
		);

		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'known' => array(
						'svg' => '<svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0" /></svg>',
					),
				),
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'test-lib', $manifest_path );

		$this->assertSame(
			'',
			Spectre_Icons_Elementor_Manifest_Renderer::render_icon(
				array(
					'library' => 'test-lib',
					'value'   => 'test-lib unknown',
				)
			)
		);
	}
}
