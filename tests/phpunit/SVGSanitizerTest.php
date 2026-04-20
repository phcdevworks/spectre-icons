<?php

declare(strict_types=1);

final class SVGSanitizerTest extends Spectre_Icons_PHPUnit_Test_Case {

	public function test_sanitize_preserves_safe_svg_content(): void {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $sanitized );
		$this->assertStringContainsString( 'stroke="currentColor"', $sanitized );
		$this->assertStringContainsString( '<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"', $sanitized );
		$this->assertStringContainsString( '<circle cx="12" cy="13" r="3"', $sanitized );
	}

	public function test_sanitize_preserves_newly_added_safe_attributes(): void {
		$svg = '<svg fill-rule="evenodd" clip-rule="evenodd" opacity="0.5" fill-opacity="0.8" stroke-opacity="0.9"><path d="M0 0h24v24H0z"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'fill-rule="evenodd"', $sanitized );
		$this->assertStringContainsString( 'clip-rule="evenodd"', $sanitized );
		$this->assertStringContainsString( 'opacity="0.5"', $sanitized );
		$this->assertStringContainsString( 'fill-opacity="0.8"', $sanitized );
		$this->assertStringContainsString( 'stroke-opacity="0.9"', $sanitized );
	}

	public function test_sanitize_preserves_dashed_and_vector_attributes(): void {
		$svg = '<svg stroke-dasharray="5,5" stroke-dashoffset="10" vector-effect="non-scaling-stroke"><path d="M0 0h24v24H0z"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'stroke-dasharray="5,5"', $sanitized );
		$this->assertStringContainsString( 'stroke-dashoffset="10"', $sanitized );
		$this->assertStringContainsString( 'vector-effect="non-scaling-stroke"', $sanitized );
	}

	public function test_sanitize_preserves_layout_attributes(): void {
		$svg = '<svg preserveAspectRatio="xMidYMid meet" overflow="visible"><path d="M0 0h24v24H0z"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'preserveAspectRatio="xMidYMid meet"', $sanitized );
		$this->assertStringContainsString( 'overflow="visible"', $sanitized );
	}

	public function test_sanitize_preserves_accessibility_and_identity_attributes(): void {
		$svg = '<svg id="my-icon" aria-label="Camera" aria-labelledby="title-id" aria-describedby="desc-id"><path d="M0 0h24v24H0z"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'id="my-icon"', $sanitized );
		$this->assertStringContainsString( 'aria-label="Camera"', $sanitized );
		$this->assertStringContainsString( 'aria-labelledby="title-id"', $sanitized );
		$this->assertStringContainsString( 'aria-describedby="desc-id"', $sanitized );
	}

	public function test_sanitize_preserves_rounded_rect_attributes(): void {
		$svg = '<svg><rect x="0" y="0" width="10" height="10" rx="2" ry="2"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'rx="2"', $sanitized );
		$this->assertStringContainsString( 'ry="2"', $sanitized );
	}

	public function test_sanitize_handles_self_closing_svg_tag(): void {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( '<svg', $sanitized );
		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $sanitized );
	}

	public function test_sanitize_removes_dangerous_tags_and_attributes(): void {
		$svg = '<svg onclick="alert(1)"><script>alert(2)</script><path d="M0 0h24v24H0z"/><foreignObject>Dangerous</foreignObject></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringNotContainsString( 'onclick=', $sanitized );
		$this->assertStringNotContainsString( '<script', $sanitized );
		$this->assertStringNotContainsString( '<foreignObject', $sanitized );
		$this->assertStringContainsString( '<path d="M0 0h24v24H0z"', $sanitized );
	}

	public function test_sanitize_preserves_accessibility_and_id_attributes(): void {
		$svg = '<svg id="my-icon" aria-label="Icon Label" aria-labelledby="title-id" aria-describedby="desc-id"><title id="title-id">Title</title><desc id="desc-id">Description</desc><path d="M0 0h24v24H0z"/></svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( 'id="my-icon"', $sanitized );
		$this->assertStringContainsString( 'aria-label="Icon Label"', $sanitized );
		$this->assertStringContainsString( 'aria-labelledby="title-id"', $sanitized );
		$this->assertStringContainsString( 'aria-describedby="desc-id"', $sanitized );
		$this->assertStringContainsString( 'id="title-id"', $sanitized );
		$this->assertStringContainsString( 'id="desc-id"', $sanitized );
	}

	public function test_sanitize_handles_multi_line_self_closing_svg_tag(): void {
		$svg = '<svg
			xmlns="http://www.w3.org/2000/svg"
			width="24"
			height="24"
			viewBox="0 0 24 24"
		/>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( '<svg', $sanitized );
		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $sanitized );
	}

	public function test_sanitize_handles_multi_line_svg_block(): void {
		$svg = '<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 24 24"
		>
			<path d="M0 0h24v24H0z"/>
		</svg>';

		$sanitized = Spectre_Icons_SVG_Sanitizer::sanitize( $svg );

		$this->assertStringContainsString( '<svg', $sanitized );
		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $sanitized );
		$this->assertStringContainsString( '<path', $sanitized );
	}

	public function test_sanitize_handles_empty_or_invalid_input(): void {
		$this->assertSame( '', Spectre_Icons_SVG_Sanitizer::sanitize( '' ) );
		$this->assertSame( '', Spectre_Icons_SVG_Sanitizer::sanitize( '   ' ) );
		$this->assertSame( '', Spectre_Icons_SVG_Sanitizer::sanitize( 'not an svg' ) );
	}
}
