<?php

/**
 * WordPress function stubs for development environment.
 * These functions are normally provided by WordPress core.
 *
 * @package SpectreElementorIcons
 */

if (!function_exists('plugin_dir_url')) {
	function plugin_dir_url($file)
	{
		return '';
	}
}

if (!function_exists('add_action')) {
	function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		return true;
	}
}

if (!function_exists('add_filter')) {
	function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
	{
		return true;
	}
}

if (!function_exists('apply_filters')) {
	function apply_filters($hook, $value, ...$args)
	{
		return $value;
	}
}

if (!function_exists('wp_enqueue_style')) {
	function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all')
	{
		return null;
	}
}

if (!function_exists('wp_enqueue_script')) {
	function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false)
	{
		return null;
	}
}

if (!function_exists('wp_register_script')) {
	function wp_register_script($handle, $src, $deps = [], $ver = false, $in_footer = false)
	{
		return true;
	}
}

if (!function_exists('wp_localize_script')) {
	function wp_localize_script($handle, $object_name, $l10n)
	{
		return true;
	}
}

if (!function_exists('add_options_page')) {
	function add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback = '')
	{
		return '';
	}
}

if (!function_exists('register_setting')) {
	function register_setting($option_group, $option_name, $args = [])
	{
		return null;
	}
}

if (!function_exists('add_settings_section')) {
	function add_settings_section($id, $title, $callback, $page)
	{
		return null;
	}
}

if (!function_exists('add_settings_field')) {
	function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = [])
	{
		return null;
	}
}

if (!function_exists('get_option')) {
	function get_option($option, $default = false)
	{
		return $default;
	}
}

if (!function_exists('wp_parse_args')) {
	function wp_parse_args($args, $defaults = [])
	{
		if (is_object($args)) {
			$parsed_args = get_object_vars($args);
		} elseif (is_array($args)) {
			$parsed_args = &$args;
		} else {
			parse_str($args, $parsed_args);
		}
		if (is_array($defaults)) {
			return array_merge($defaults, $parsed_args);
		}
		return $parsed_args;
	}
}

if (!function_exists('current_user_can')) {
	function current_user_can($capability, ...$args)
	{
		return true;
	}
}

if (!function_exists('settings_errors')) {
	function settings_errors($setting = '', $sanitize = false, $hide_on_update = false)
	{
		return null;
	}
}

if (!function_exists('settings_fields')) {
	function settings_fields($option_group)
	{
		echo '';
	}
}

if (!function_exists('do_settings_sections')) {
	function do_settings_sections($page)
	{
		echo '';
	}
}

if (!function_exists('submit_button')) {
	function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null)
	{
		echo '';
	}
}

if (!function_exists('__')) {
	function __($text, $domain = 'default')
	{
		return $text;
	}
}

if (!function_exists('esc_html__')) {
	function esc_html__($text, $domain = 'default')
	{
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('esc_html_e')) {
	function esc_html_e($text, $domain = 'default')
	{
		echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('esc_attr')) {
	function esc_attr($text)
	{
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('esc_html')) {
	function esc_html($text)
	{
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('checked')) {
	function checked($checked, $current = true, $echo = true)
	{
		$result = '';
		if ($checked == $current) {
			$result = ' checked="checked"';
		}
		if ($echo) {
			echo $result;
		}
		return $result;
	}
}

if (!function_exists('sanitize_key')) {
	function sanitize_key($key)
	{
		return preg_replace('/[^a-z0-9_\-]/', '', strtolower($key));
	}
}

if (!function_exists('sanitize_text_field')) {
	function sanitize_text_field($str)
	{
		return strip_tags($str);
	}
}

if (!function_exists('wp_kses_post')) {
	function wp_kses_post($data)
	{
		return strip_tags($data);
	}
}

if (!function_exists('trailingslashit')) {
	function trailingslashit($string)
	{
		return rtrim($string, '/\\') . '/';
	}
}

if (!function_exists('sanitize_html_class')) {
	function sanitize_html_class($class, $fallback = '')
	{
		$sanitized = preg_replace('/[^a-z0-9_\-]/', '', strtolower($class));
		return $sanitized ?: $fallback;
	}
}
