<?php

/**
 * Coordinates Spectre icon libraries with Elementor.
 *
 * @package SpectreIcons
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('Spectre_Icons_Elementor_Library_Manager')) :

    final class Spectre_Icons_Elementor_Library_Manager {

        private static $instance = null;

        /**
         * @var Spectre_Icons_Elementor_Settings
         */
        private $settings;

        /**
         * @var array<string,array>
         */
        private $libraries = array();

        private function __construct(Spectre_Icons_Elementor_Settings $settings) {
            $this->settings = $settings;
            $this->load_libraries();
        }

        public static function instance(Spectre_Icons_Elementor_Settings $settings) {
            if (null === self::$instance) {
                self::$instance = new self($settings);
            }
            return self::$instance;
        }

        private function load_libraries() {
            $raw = apply_filters('spectre_icons_elementor_icon_libraries', array());

            if (! is_array($raw)) {
                $this->libraries = array();
                return;
            }

            $validated = array();

            foreach ($raw as $slug => $library) {
                $slug = sanitize_key($slug);
                if ('' === $slug || ! is_array($library)) {
                    continue;
                }

                $library = wp_parse_args(
                    $library,
                    array(
                        'label'  => '',
                        'config' => array(),
                    )
                );

                $validated_library = $this->validate_library_definition($slug, $library);
                if (null !== $validated_library) {
                    $validated[$slug] = $validated_library;
                }
            }

            $this->libraries = $validated;
        }

        private function validate_library_definition($slug, array $library) {
            $label = isset($library['label']) ? (string) $library['label'] : '';

            if ('' === $label || ! is_array($library['config'])) {
                return null;
            }

            $config = wp_parse_args(
                $library['config'],
                array(
                    'name'            => $slug,
                    'label'           => $label,
                    'labelIcon'       => '',
                    'manifest'        => '',
                    'prefix'          => '',
                    'icons'           => array(),
                    'render_callback' => null,
                    'native'          => false,
                    'ver'             => '0.1.0',
                )
            );

            // Enforce: must be callable (don’t over-restrict to [Class, method] only).
            if (empty($config['render_callback']) || ! is_callable($config['render_callback'])) {
                return null;
            }

            // Normalize/sanitize name.
            $name = sanitize_key((string) $config['name']);
            if ('' === $name) {
                $name = $slug;
            }
            $config['name'] = $name;

            // Sanitize label.
            $config['label'] = wp_strip_all_tags((string) $config['label']);
            if ('' === $config['label']) {
                $config['label'] = wp_strip_all_tags($label);
            }
            if ('' === $config['label']) {
                $config['label'] = $slug;
            }

            // Allow only Elementor's eicon-* tokens for the tab icon.
            $icon = (string) $config['labelIcon'];
            if (strlen($icon) > 32 || ! preg_match('/^eicon-[a-z0-9\-]+$/', $icon)) {
                $icon = '';
            }
            $config['labelIcon'] = $icon;

            // Prefix used for classnames (preserve hyphens/trailing hyphen).
            $config['prefix'] = is_string($config['prefix'])
                ? preg_replace('/[^a-z0-9\-_]/i', '', (string) $config['prefix'])
                : '';

            // Icons list must be an array.
            $config['icons'] = is_array($config['icons']) ? $config['icons'] : array();

            $library['config'] = $config;
            $library['label']  = $config['label'];

            return $library;
        }

        public function register_additional_tabs($tabs) {
            // Refresh libraries at the moment Elementor asks for them (prevents stale/late filter issues).
            $this->load_libraries();

            if (! is_array($tabs)) {
                $tabs = array();
            }

            if (empty($this->libraries)) {
                return $tabs;
            }

            $prefs = $this->settings->get_tabs();
            $prefs = is_array($prefs) ? $prefs : array();

            foreach ($this->libraries as $slug => $library) {
                if (empty($library['config']) || ! is_array($library['config'])) {
                    continue;
                }

                $enabled = isset($prefs[$slug]) ? (bool) $prefs[$slug] : true;
                if (! $enabled || isset($tabs[$slug])) {
                    continue;
                }

                $tabs[$slug] = $library['config'];
            }

            return $tabs;
        }
    }

endif;
