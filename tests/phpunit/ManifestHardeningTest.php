<?php

declare(strict_types=1);

final class ManifestHardeningTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_get_icons_filters_invalid_entries_in_associative_manifest(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'valid-svg'  => array( 'svg' => '<svg><path d="M0 0" /></svg>' ),
				'valid-body' => array( 'body' => '<path d="M1 1" />' ),
				'raw-string' => '<svg><circle cx="5" cy="5" r="5" /></svg>',
				'empty-svg'  => array( 'svg' => '' ),
				'empty-body' => array( 'body' => ' ' ),
				'no-content' => array( 'other' => 'data' ),
				'not-array'  => 123,
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'assoc-test', $manifest_path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'assoc-test' );

		$this->assertContains( 'valid-svg', $slugs );
		$this->assertContains( 'valid-body', $slugs );
		$this->assertContains( 'raw-string', $slugs );
		$this->assertNotContains( 'empty-svg', $slugs );
		$this->assertNotContains( 'empty-body', $slugs );
		$this->assertNotContains( 'no-content', $slugs );
		$this->assertNotContains( 'not-array', $slugs );
		$this->assertCount( 3, $slugs );
	}

	public function test_get_icons_filters_invalid_entries_in_indexed_manifest(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				array(
					'slug' => 'valid-svg',
					'svg'  => '<svg><path d="M0 0" /></svg>',
				),
				array(
					'slug' => 'valid-body',
					'body' => '<path d="M1 1" />',
				),
				array(
					'slug' => 'empty-svg',
					'svg'  => '',
				),
				array(
					'slug' => 'empty-body',
					'body' => ' ',
				),
				array(
					'slug'  => 'no-content',
					'other' => 'data',
				),
				array( 'no-slug' => 'data' ),
				'invalid-entry',
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'indexed-test', $manifest_path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'indexed-test' );

		$this->assertContains( 'valid-svg', $slugs );
		$this->assertContains( 'valid-body', $slugs );
		$this->assertNotContains( 'empty-svg', $slugs );
		$this->assertNotContains( 'empty-body', $slugs );
		$this->assertNotContains( 'no-content', $slugs );
		$this->assertCount( 2, $slugs );
	}

	public function test_get_icons_handles_wrapped_manifest_structure(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'wrapped-icon' => array( 'svg' => '<svg><path d="M0 0" /></svg>' ),
				),
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'wrapped-test', $manifest_path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'wrapped-test' );

		$this->assertSame( array( 'wrapped-icon' ), $slugs );
	}

	public function test_get_icons_returns_empty_for_malformed_json(): void {
		$path = tempnam( sys_get_temp_dir(), 'spectre-icons-bad-' );
		file_put_contents( $path, '{ "unclosed": "json" ' );

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'bad-json', $path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'bad-json' );

		$this->assertSame( array(), $slugs );
		unlink( $path );
	}

	public function test_get_icons_handles_invalid_icons_key_resiliently(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => 'not-an-array',
			)
		);

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'invalid-icons-test', $manifest_path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'invalid-icons-test' );

		$this->assertSame( array(), $slugs );
	}

	public function test_get_icons_handles_empty_manifest_resiliently(): void {
		$manifest_path = $this->create_temp_manifest( array() );

		Spectre_Icons_Elementor_Manifest_Renderer::register_manifest( 'empty-test', $manifest_path );
		$slugs = Spectre_Icons_Elementor_Manifest_Renderer::get_icon_slugs( 'empty-test' );

		$this->assertSame( array(), $slugs );
	}
}
