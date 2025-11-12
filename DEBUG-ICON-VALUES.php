<?php

/**
 * DEBUG: Add this to your functions.php to see what Elementor sends
 *
 * Upload this plugin, then add this to your theme's functions.php:
 *
 * add_filter('elementor/icons_manager/additional_tabs', function($tabs) {
 *     error_log('ELEMENTOR TABS: ' . print_r(array_keys($tabs), true));
 *     return $tabs;
 * }, 999);
 *
 * add_action('elementor/frontend/before_render', function($element) {
 *     $settings = $element->get_settings();
 *     if (!empty($settings['icon']) || !empty($settings['selected_icon'])) {
 *         error_log('ICON DATA: ' . print_r([
 *             'icon' => $settings['icon'] ?? null,
 *             'selected_icon' => $settings['selected_icon'] ?? null,
 *         ], true));
 *     }
 * });
 *
 * Then check your WordPress debug.log file
 */

// Add this filter to YOUR PLUGIN to debug what render_callback receives
add_filter('elementor/icons_manager/additional_tabs', function ($tabs) {

	// Wrap the render_callback to log what it receives
	foreach ($tabs as $tab_name => &$tab_config) {
		if (isset($tab_config['render_callback']) && is_callable($tab_config['render_callback'])) {
			$original_callback = $tab_config['render_callback'];

			$tab_config['render_callback'] = function ($icon, $attributes = [], $tag = 'span') use ($original_callback, $tab_name) {
				// Log what Elementor is sending
				error_log(sprintf(
					"RENDER CALLBACK [%s]:\n  Icon: %s\n  Attributes: %s\n  Tag: %s",
					$tab_name,
					print_r($icon, true),
					print_r($attributes, true),
					$tag
				));

				// Call the original callback
				return call_user_func_array($original_callback, [$icon, $attributes, $tag]);
			};
		}
	}

	return $tabs;
}, 9999);
