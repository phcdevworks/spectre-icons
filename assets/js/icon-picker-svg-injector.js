/**
 * Injects SVG icons into Elementor's icon picker preview
 */
(function($) {
    'use strict';

    // Wait for Elementor to load
    $(window).on('elementor:init', function() {

        elementor.on('panel:init', function() {

            // Hook into icon manager rendering
            elementor.hooks.addFilter('controls/icon/custom_icons', function(libraries) {

                // Find our Lucide library
                const lucideLib = libraries.find(lib => lib.name === 'spectre-lucide');

                if (!lucideLib || !lucideLib.icons) {
                    return libraries;
                }

                // Fetch the SVG manifest
                fetch(lucideLib.fetchJson)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.icons) return;

                        // Store SVGs for rendering
                        window.spectreIconSvgs = window.spectreIconSvgs || {};
                        window.spectreIconSvgs['spectre-lucide'] = data.icons;

                        // Inject SVGs into icon elements when they render
                        setTimeout(function() {
                            injectSvgsIntoPickerIcons();
                        }, 500);
                    });

                return libraries;
            });
        });
    });

    // Inject SVGs into the picker icon elements
    function injectSvgsIntoPickerIcons() {
        const svgs = window.spectreIconSvgs?.['spectre-lucide'];
        if (!svgs) return;

        // Find all Lucide icon elements in the picker
        $('.elementor-icons-manager__tab__item__icon').each(function() {
            const $icon = $(this);
            const classes = $icon.attr('class') || '';

            // Match lucide-{slug} class
            const match = classes.match(/lucide-([a-z0-9-]+)/);
            if (match && match[1]) {
                const slug = match[1];
                const svg = svgs[slug];

                if (svg && !$icon.find('svg').length) {
                    $icon.html(svg);
                }
            }
        });
    }

    // Re-inject when tabs change or search happens
    $(document).on('click', '.elementor-icons-manager__tab__item__header', function() {
        setTimeout(injectSvgsIntoPickerIcons, 300);
    });

    $(document).on('input', '.elementor-icons-manager__search', function() {
        setTimeout(injectSvgsIntoPickerIcons, 300);
    });

})(jQuery);
