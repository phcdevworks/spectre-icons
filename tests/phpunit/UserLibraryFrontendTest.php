<?php

declare(strict_types=1);

/**
 * Regression tests for the spectre-user (My Icons) frontend render path.
 *
 * Verifies that:
 * - spectre-user library definitions always carry manifest_path + manifest_url.
 * - spectre_icons_ensure_manifests_registered() populates the registry for
 *   a manifest_path-based library so render_icon works on the frontend.
 * - Spectre_Icons_Icon_Renderer::render_icon() returns inline SVG for a
 *   user-uploaded icon once the library is registered.
 */
final class UserLibraryFrontendTest extends Spectre_Icons_PHPUnit_Test_Case {

	/**
	 * Regression: library_definition() must always expose manifest_path and
	 * manifest_url so the frontend render path and JS preview can locate the
	 * manifest without falling back to spectre_icons_resolve_manifest_path().
	 */
	public function test_user_library_definition_has_manifest_path_and_manifest_url(): void {
		$def = Spectre_Icons_User_Library_Manager::library_definition();

		$this->assertArrayHasKey( 'manifest_path', $def, 'manifest_path key must exist in library definition' );
		$this->assertArrayHasKey( 'manifest_url', $def, 'manifest_url key must exist in library definition' );
		$this->assertNull( $def['manifest_file'], 'manifest_file must be null for spectre-user (not a plugin-bundled asset)' );
		$this->assertSame( 'spectre-user-', $def['class_prefix'], 'serialization-anchored prefix must not change' );
		$this->assertNotEmpty( $def['manifest_path'], 'manifest_path must not be empty' );
		$this->assertNotEmpty( $def['manifest_url'], 'manifest_url must not be empty' );
	}

	/**
	 * spectre_icons_ensure_manifests_registered() must register a spectre-user
	 * manifest (identified by manifest_path, not manifest_file) with the core
	 * registry, even without the Elementor icon tab filter having fired.
	 */
	public function test_ensure_manifests_registered_registers_user_library_via_manifest_path(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'star' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3 7h7l-5 4 2 7-7-4-7 4 2-7-5-4h7z"/></svg>',
				),
			)
		);

		add_filter(
			'spectre_icons_library_definitions',
			static function ( $defs ) use ( $manifest_path ) {
				$defs['spectre-user'] = array(
					'label'         => 'My Icons',
					'label_icon'    => 'eicon-upload',
					'manifest_file' => null,
					'manifest_path' => $manifest_path,
					'manifest_url'  => 'http://example.org/wp-content/uploads/spectre-icons/manifest.json',
					'class_prefix'  => 'spectre-user-',
					'style'         => '',
				);
				return $defs;
			}
		);

		$this->assertFalse(
			Spectre_Icons_Manifest_Registry::has_library( 'spectre-user' ),
			'Registry must be empty before ensure() is called'
		);

		spectre_icons_ensure_manifests_registered();

		$this->assertTrue(
			Spectre_Icons_Manifest_Registry::has_library( 'spectre-user' ),
			'Registry must contain spectre-user after ensure() is called'
		);
	}

	/**
	 * Uploaded icons must render as inline SVG via Spectre_Icons_Icon_Renderer
	 * once the library is registered — this is the PHP frontend render path.
	 */
	public function test_render_icon_returns_svg_for_registered_user_icon(): void {
		$svg_content   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>';
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'circle' => $svg_content,
				),
			)
		);

		Spectre_Icons_Manifest_Registry::register_manifest(
			'spectre-user',
			$manifest_path,
			array(
				'prefix'  => 'spectre-user-',
				'options' => array( 'style' => '' ),
			)
		);

		$html = Spectre_Icons_Icon_Renderer::render_icon(
			array(
				'library' => 'spectre-user',
				'value'   => 'spectre-user-circle',
			)
		);

		$this->assertNotEmpty( $html, 'render_icon must return non-empty HTML for a registered user icon' );
		$this->assertStringContainsString( '<svg', $html, 'render_icon output must contain an SVG element' );
		$this->assertStringContainsString( 'spectre-user-circle', $html, 'render_icon output must include the icon class' );
	}

	/**
	 * Full integration path: ensure() + render_icon() work end-to-end for a
	 * spectre-user manifest registered only via manifest_path.
	 */
	public function test_ensure_then_render_works_end_to_end_for_user_library(): void {
		$svg_content   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20"/></svg>';
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'box' => $svg_content,
				),
			)
		);

		add_filter(
			'spectre_icons_library_definitions',
			static function ( $defs ) use ( $manifest_path ) {
				$defs['spectre-user'] = array(
					'label'         => 'My Icons',
					'label_icon'    => 'eicon-upload',
					'manifest_file' => null,
					'manifest_path' => $manifest_path,
					'manifest_url'  => 'http://example.org/wp-content/uploads/spectre-icons/manifest.json',
					'class_prefix'  => 'spectre-user-',
					'style'         => '',
				);
				return $defs;
			}
		);

		spectre_icons_ensure_manifests_registered();

		$html = Spectre_Icons_Icon_Renderer::render_icon(
			array(
				'library' => 'spectre-user',
				'value'   => 'spectre-user-box',
			)
		);

		$this->assertNotEmpty( $html, 'render_icon must return HTML after ensure() populates the registry' );
		$this->assertStringContainsString( '<svg', $html );
		$this->assertStringContainsString( 'spectre-user-box', $html );
	}

	/**
	 * ensure() must be idempotent — calling it twice must not double-register
	 * or corrupt the registry.
	 */
	public function test_ensure_manifests_registered_is_idempotent(): void {
		$manifest_path = $this->create_temp_manifest(
			array(
				'icons' => array(
					'heart' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21C12 21 3 13 3 7a4 4 0 0 1 8-1 4 4 0 0 1 8 1c0 6-9 14-9 14z"/></svg>',
				),
			)
		);

		add_filter(
			'spectre_icons_library_definitions',
			static function ( $defs ) use ( $manifest_path ) {
				$defs['spectre-user'] = array(
					'label'         => 'My Icons',
					'label_icon'    => 'eicon-upload',
					'manifest_file' => null,
					'manifest_path' => $manifest_path,
					'manifest_url'  => 'http://example.org/wp-content/uploads/spectre-icons/manifest.json',
					'class_prefix'  => 'spectre-user-',
					'style'         => '',
				);
				return $defs;
			}
		);

		spectre_icons_ensure_manifests_registered();
		spectre_icons_ensure_manifests_registered(); // second call must be a no-op

		$this->assertTrue( Spectre_Icons_Manifest_Registry::has_library( 'spectre-user' ) );

		$html = Spectre_Icons_Icon_Renderer::render_icon(
			array(
				'library' => 'spectre-user',
				'value'   => 'spectre-user-heart',
			)
		);

		$this->assertStringContainsString( '<svg', $html );
	}

	/**
	 * Bundled libraries must still be registered by ensure() alongside
	 * spectre-user, so a single call handles all libraries.
	 */
	public function test_ensure_manifests_registered_also_covers_bundled_libraries(): void {
		spectre_icons_ensure_manifests_registered();

		$this->assertTrue(
			Spectre_Icons_Manifest_Registry::has_library( 'spectre-lucide' ),
			'ensure() must register spectre-lucide'
		);
		$this->assertTrue(
			Spectre_Icons_Manifest_Registry::has_library( 'spectre-fontawesome' ),
			'ensure() must register spectre-fontawesome'
		);
	}
}
