<?php

function waf_init() {
    $rules_file = __DIR__ . '/rules.json';
    $config_file = __DIR__ . '/config.json';
    $log_dir = __DIR__ . '/logs';

    // Ensure log directory exists
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    // Load rules and config
    $rules_data = json_decode(file_get_contents($rules_file), true);
    $config_data = file_exists($config_file) ? json_decode(file_get_contents($config_file), true) : [];

    // Determine enabled categories
    $enabled_categories = $config_data['enabled_categories'] ?? array_keys($rules_data);
    
    // Get whitelisted parameters (parameters to skip WAF checking)
    $whitelisted_params = $config_data['whitelisted_params'] ?? ['token'];

    // Merge input sources
    $inputs = array_merge($_GET, $_POST, $_COOKIE);

    foreach ($rules_data as $category => $rules) {
        // Skip disabled categories
        if (!in_array($category, $enabled_categories)) continue;

        foreach ($inputs as $key => $value) {
            // Skip whitelisted parameters
            if (in_array(strtolower($key), array_map('strtolower', $whitelisted_params))) continue;
            
            if (!is_string($value)) continue; // Skip arrays/files

            $val = strtolower($value);
            foreach ($rules as $pattern) {
                if (@preg_match("/$pattern/i", $val)) {
                    if (preg_match("/$pattern/i", $val)) {
                        waf_block($key, $value, $category, $pattern);
                    }
                }
            }
        }
    }
}

/**
 * Handle blocked requests
 */
function waf_block($key, $value, $category, $pattern) {
    $log_file = __DIR__ . '/logs/waf-' . date('Y-m-d') . '.log';
    $log_entry = sprintf(
        "[%s] BLOCK [%s] key=%s pattern=%s value=%s\n",
        date('c'),
        strtoupper($category),
        $key,
        $pattern,
        substr($value, 0, 100)
    );

    file_put_contents($log_file, $log_entry, FILE_APPEND);

    http_response_code(403);
    exit("Access denied by PitchPoint($category rule). Be a good boy.");
}

// Initialize WAF
waf_init();
?>
