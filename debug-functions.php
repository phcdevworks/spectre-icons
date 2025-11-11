/**
 * DEBUG SCRIPT - Add to WordPress functions.php temporarily
 *
 * This will help diagnose the icon picker issue
 */

// Debug: Log what's being registered
add_action('elementor/icons_manager/additional_tabs', function($tabs) {
    error_log('=== SPECTRE ICONS DEBUG ===');
    error_log('Additional tabs being registered: ' . print_r(array_keys($tabs), true));

    foreach ($tabs as $slug => $config) {
        if (strpos($slug, 'spectre-') === 0) {
            error_log("Library: $slug");
            error_log("  - Label: " . ($config['label'] ?? 'N/A'));
            error_log("  - Prefix: " . ($config['prefix'] ?? 'N/A'));
            error_log("  - Icon count: " . (isset($config['icons']) ? count($config['icons']) : 0));
            error_log("  - fetchJson: " . ($config['fetchJson'] ?? 'N/A'));
            error_log("  - Has render_callback: " . (isset($config['render_callback']) ? 'YES' : 'NO'));
        }
    }
    error_log('=== END DEBUG ===');

    return $tabs;
}, 999);

// Debug: Output JavaScript console log
add_action('elementor/editor/footer', function() {
    ?>
    <script>
    console.group('🎨 Spectre Icons Debug');
    console.log('Config:', window.SpectreElementorIconsConfig);
    console.log('Libraries:', window.SpectreElementorIconsConfig?.libraries);

    // Check if Elementor has our icons
    setTimeout(() => {
        if (window.elementor?.config?.icons) {
            console.log('Elementor Icons Manager:', elementor.config.icons);

            const spectreIcons = Object.keys(elementor.config.icons).filter(k => k.startsWith('spectre-'));
            console.log('Spectre libraries registered:', spectreIcons);

            spectreIcons.forEach(lib => {
                console.log(`${lib}:`, {
                    label: elementor.config.icons[lib].label,
                    iconCount: elementor.config.icons[lib].icons?.length || 0,
                    fetchJson: elementor.config.icons[lib].fetchJson
                });
            });
        }
        console.groupEnd();
    }, 1000);
    </script>
    <?php
}, 999);

// Debug: Check icon rendering
add_filter('elementor/icons_manager/additional_tabs', function($tabs) {
    // Force add debug test icon
    if (isset($tabs['spectre-lucide'])) {
        error_log('Lucide config check:');
        error_log('  Icons array type: ' . gettype($tabs['spectre-lucide']['icons']));
        error_log('  First 5 icons: ' . print_r(array_slice($tabs['spectre-lucide']['icons'], 0, 5), true));
    }
    return $tabs;
}, 1000);
