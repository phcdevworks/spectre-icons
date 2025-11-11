// Spectre Icons - Simple Debug Test
// Add this to your theme's functions.php temporarily

add_action('elementor/editor/after_enqueue_scripts', function() {
    ?>
    <script>
    jQuery(window).on('load', function() {
        setTimeout(function() {
            console.log('=== SPECTRE ICONS DEBUG ===');
            console.log('1. Config loaded:', typeof window.SpectreElementorIconsConfig);
            console.log('2. Libraries:', window.SpectreElementorIconsConfig);

            if (window.elementor) {
                console.log('3. Elementor ready:', true);
                console.log('4. Icon libraries:', Object.keys(elementor.config.icons || {}));

                // Check our libraries
                ['spectre-lucide', 'spectre-fontawesome'].forEach(function(lib) {
                    if (elementor.config.icons && elementor.config.icons[lib]) {
                        console.log('✓ ' + lib + ' registered:', {
                            icons: elementor.config.icons[lib].icons.length,
                            prefix: elementor.config.icons[lib].prefix
                        });
                    } else {
                        console.error('✗ ' + lib + ' NOT registered');
                    }
                });
            }
            console.log('========================');
        }, 2000);
    });
    </script>
    <?php
}, 999);
