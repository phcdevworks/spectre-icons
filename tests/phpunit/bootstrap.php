<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../../' );
}

if ( ! defined( 'SPECTRE_ICONS_PATH' ) ) {
	define( 'SPECTRE_ICONS_PATH', dirname( __DIR__, 2 ) . '/' );
}

if ( ! defined( 'SPECTRE_ICONS_URL' ) ) {
	define( 'SPECTRE_ICONS_URL', 'http://example.org/wp-content/plugins/spectre-icons/' );
}

if ( ! defined( 'SPECTRE_ICONS_VERSION' ) ) {
	define( 'SPECTRE_ICONS_VERSION', 'test' );
}

$GLOBALS['spectre_wp_filters']           = array();
$GLOBALS['spectre_wp_actions']           = array();
$GLOBALS['spectre_wp_options']           = array();
$GLOBALS['spectre_wp_styles']            = array();
$GLOBALS['spectre_wp_scripts']           = array();
$GLOBALS['spectre_wp_inline_styles']     = array();
$GLOBALS['spectre_wp_localized_scripts'] = array();
$GLOBALS['spectre_wp_current_screen']    = (object) array( 'id' => 'plugins' );

function spectre_icons_tests_reset_wordpress_state() {
	$GLOBALS['spectre_wp_filters']           = array();
	$GLOBALS['spectre_wp_actions']           = array();
	$GLOBALS['spectre_wp_options']           = array();
	$GLOBALS['spectre_wp_styles']            = array();
	$GLOBALS['spectre_wp_scripts']           = array();
	$GLOBALS['spectre_wp_inline_styles']     = array();
	$GLOBALS['spectre_wp_localized_scripts'] = array();
	$GLOBALS['spectre_wp_current_screen']    = (object) array( 'id' => 'plugins' );
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		if ( ! isset( $GLOBALS['spectre_wp_filters'][ $hook_name ] ) ) {
			$GLOBALS['spectre_wp_filters'][ $hook_name ] = array();
		}

		if ( ! isset( $GLOBALS['spectre_wp_filters'][ $hook_name ][ $priority ] ) ) {
			$GLOBALS['spectre_wp_filters'][ $hook_name ][ $priority ] = array();
		}

		$GLOBALS['spectre_wp_filters'][ $hook_name ][ $priority ][] = array(
			'callback'      => $callback,
			'accepted_args' => (int) $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook_name, $value ) {
		$args = func_get_args();
		array_shift( $args );

		if ( empty( $GLOBALS['spectre_wp_filters'][ $hook_name ] ) ) {
			return $value;
		}

		ksort( $GLOBALS['spectre_wp_filters'][ $hook_name ] );

		foreach ( $GLOBALS['spectre_wp_filters'][ $hook_name ] as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				$accepted_args = max( 1, (int) $callback['accepted_args'] );
				$call_args     = array_slice( $args, 0, $accepted_args );
				$value         = call_user_func_array( $callback['callback'], $call_args );
				$args[0]       = $value;
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		return add_filter( $hook_name, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook_name ) {
		$args = func_get_args();
		array_shift( $args );

		if ( ! isset( $GLOBALS['spectre_wp_actions'][ $hook_name ] ) ) {
			$GLOBALS['spectre_wp_actions'][ $hook_name ] = 0;
		}

		++$GLOBALS['spectre_wp_actions'][ $hook_name ];

		if ( empty( $GLOBALS['spectre_wp_filters'][ $hook_name ] ) ) {
			return;
		}

		ksort( $GLOBALS['spectre_wp_filters'][ $hook_name ] );

		foreach ( $GLOBALS['spectre_wp_filters'][ $hook_name ] as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				$accepted_args = max( 0, (int) $callback['accepted_args'] );
				call_user_func_array( $callback['callback'], array_slice( $args, 0, $accepted_args ) );
			}
		}
	}
}

if ( ! function_exists( 'did_action' ) ) {
	function did_action( $hook_name ) {
		return isset( $GLOBALS['spectre_wp_actions'][ $hook_name ] ) ? (int) $GLOBALS['spectre_wp_actions'][ $hook_name ] : 0;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		return array_key_exists( $option, $GLOBALS['spectre_wp_options'] ) ? $GLOBALS['spectre_wp_options'][ $option ] : $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value ) {
		$GLOBALS['spectre_wp_options'][ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );
		return preg_replace( '/[^a-z0-9_\-]/', '', $key );
	}
}

if ( ! function_exists( 'sanitize_file_name' ) ) {
	function sanitize_file_name( $filename ) {
		$filename = basename( (string) $filename );
		return preg_replace( '/[^A-Za-z0-9._-]/', '', $filename );
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $value ) {
		return rtrim( (string) $value, '/\\' ) . '/';
	}
}

if ( ! function_exists( 'wp_normalize_path' ) ) {
	function wp_normalize_path( $path ) {
		return str_replace( '\\', '/', (string) $path );
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	function wp_parse_args( $args, $defaults = array() ) {
		return array_merge( $defaults, is_array( $args ) ? $args : array() );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $text ) {
		return trim( strip_tags( (string) $text ) );
	}
}

if ( ! function_exists( 'checked' ) ) {
	function checked( $checked, $current = true, $echo = true ) {
		$result = ( (string) $checked === (string) $current ) ? 'checked="checked"' : '';

		if ( $echo ) {
			echo $result;
		}

		return $result;
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = null ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = null ) {
		return esc_html( $text );
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return true;
	}
}

if ( ! function_exists( 'wp_doing_ajax' ) ) {
	function wp_doing_ajax() {
		return false;
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return true;
	}
}

if ( ! function_exists( 'get_current_screen' ) ) {
	function get_current_screen() {
		return $GLOBALS['spectre_wp_current_screen'];
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false ) {
		$GLOBALS['spectre_wp_styles'][ $handle ] = compact( 'handle', 'src', 'deps', 'ver' );
		return true;
	}
}

if ( ! function_exists( 'wp_add_inline_style' ) ) {
	function wp_add_inline_style( $handle, $data ) {
		if ( ! isset( $GLOBALS['spectre_wp_inline_styles'][ $handle ] ) ) {
			$GLOBALS['spectre_wp_inline_styles'][ $handle ] = '';
		}

		$GLOBALS['spectre_wp_inline_styles'][ $handle ] .= (string) $data;
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		$GLOBALS['spectre_wp_scripts'][ $handle ] = compact( 'handle', 'src', 'deps', 'ver', 'in_footer' );
		return true;
	}
}

if ( ! function_exists( 'wp_script_is' ) ) {
	function wp_script_is( $handle, $status = 'enqueued' ) {
		return isset( $GLOBALS['spectre_wp_scripts'][ $handle ] );
	}
}

if ( ! function_exists( 'wp_dequeue_script' ) ) {
	function wp_dequeue_script( $handle ) {
		unset( $GLOBALS['spectre_wp_scripts'][ $handle ] );
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		$GLOBALS['spectre_wp_localized_scripts'][ $handle ] = array(
			'object_name' => $object_name,
			'data'        => $l10n,
		);

		return true;
	}
}

if ( ! function_exists( 'register_setting' ) ) {
	function register_setting( $option_group, $option_name, $args = array() ) {
		return true;
	}
}

if ( ! function_exists( 'add_settings_section' ) ) {
	function add_settings_section( $id, $title, $callback, $page ) {
		return true;
	}
}

if ( ! function_exists( 'add_settings_field' ) ) {
	function add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() ) {
		return true;
	}
}

if ( ! function_exists( 'add_options_page' ) ) {
	function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '' ) {
		return true;
	}
}

if ( ! function_exists( 'settings_fields' ) ) {
	function settings_fields( $option_group ) {
		echo '';
	}
}

if ( ! function_exists( 'do_settings_sections' ) ) {
	function do_settings_sections( $page ) {
		echo '';
	}
}

if ( ! function_exists( 'submit_button' ) ) {
	function submit_button( $text = null ) {
		echo '<button type="submit">Save</button>';
	}
}

require_once SPECTRE_ICONS_PATH . 'includes/class-spectre-icons-svg-sanitizer.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-manifest-renderer.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/icon-libraries.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-settings.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/class-spectre-icons-elementor-library-manager.php';
require_once SPECTRE_ICONS_PATH . 'includes/elementor/integration-hooks.php';

abstract class Spectre_Icons_PHPUnit_Test_Case extends TestCase {

	/**
	 * @var string[]
	 */
	private $temp_files = array();

	protected function setUp(): void {
		parent::setUp();

		spectre_icons_tests_reset_wordpress_state();
		add_filter( 'spectre_icons_elementor_icon_libraries', 'spectre_icons_elementor_register_manifest_libraries' );
		$this->reset_static_property( 'Spectre_Icons_Elementor_Manifest_Renderer', 'libraries', array() );
		$this->reset_static_property( 'Spectre_Icons_Elementor_Manifest_Renderer', 'icons_cache', array() );
		$this->reset_static_property( 'Spectre_Icons_Elementor_Library_Manager', 'instance', null );
	}

	protected function tearDown(): void {
		foreach ( $this->temp_files as $temp_file ) {
			if ( is_file( $temp_file ) ) {
				unlink( $temp_file );
			}
		}

		parent::tearDown();
	}

	protected function create_temp_manifest( array $payload ): string {
		$path = tempnam( sys_get_temp_dir(), 'spectre-icons-' );
		file_put_contents( $path, wp_json_encode( $payload ) );
		$this->temp_files[] = $path;

		return $path;
	}

	private function reset_static_property( string $class_name, string $property_name, $value ): void {
		$reflection = new ReflectionProperty( $class_name, $property_name );
		$reflection->setAccessible( true );
		$reflection->setValue( null, $value );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $value, $flags = 0, $depth = 512 ) {
		return json_encode( $value, $flags, $depth );
	}
}
