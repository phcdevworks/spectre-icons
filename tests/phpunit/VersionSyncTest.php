<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class VersionSyncTest extends TestCase {

	public function test_version_is_synced_across_all_locations(): void {
		$root = dirname( __DIR__, 2 ) . '/';

		$package_json = json_decode( file_get_contents( $root . 'package.json' ), true );
		$expected      = $package_json['version'];

		$plugin_file = file_get_contents( $root . 'spectre-icons.php' );
		preg_match( '/^\s*\*\s*Version:\s*(\S+)/m', $plugin_file, $header_match );
		preg_match( "/define\\(\\s*'SPECTRE_ICONS_VERSION',\\s*'([^']+)'\\s*\\)/", $plugin_file, $constant_match );

		$readme_txt = file_get_contents( $root . 'readme.txt' );
		preg_match( '/^Stable tag:\s*(\S+)/m', $readme_txt, $stable_tag_match );

		$readme_md = file_get_contents( $root . 'README.md' );
		preg_match( '/\|\s*Current version\/status\s*\|\s*([^\s|]+)\s*\|/i', $readme_md, $readme_md_match );

		$this->assertNotEmpty( $header_match, 'spectre-icons.php is missing a "Version:" plugin header.' );
		$this->assertNotEmpty( $constant_match, 'spectre-icons.php is missing the SPECTRE_ICONS_VERSION constant.' );
		$this->assertNotEmpty( $stable_tag_match, 'readme.txt is missing a "Stable tag:" field.' );
		$this->assertNotEmpty( $readme_md_match, 'README.md is missing the "Current version/status" row in the Repository Snapshot table.' );

		$this->assertSame( $expected, $header_match[1], 'spectre-icons.php "Version:" header does not match package.json.' );
		$this->assertSame( $expected, $constant_match[1], 'SPECTRE_ICONS_VERSION constant does not match package.json.' );
		$this->assertSame( $expected, $stable_tag_match[1], 'readme.txt "Stable tag:" does not match package.json.' );
		$this->assertSame( $expected, $readme_md_match[1], 'README.md "Current version/status" does not match package.json.' );
	}
}
