<?php
/**
 * Basic Rate Limiter for DDoS Protection
 * 
 * Tracks requests by IP address and enforces limits per time window.
 */

if (!function_exists('rate_limit_check')) {
    /**
     * Check if an IP address has exceeded the rate limit.
     * 
     * @param string $identifier Unique identifier (e.g., IP address, email)
     * @param int $maxRequests Maximum number of requests allowed
     * @param int $timeWindow Time window in seconds (default: 60 = 1 minute)
     * @param string $action Optional action name for different limits per endpoint
     * @return bool True if allowed, false if rate limited
     */
    function rate_limit_check(string $identifier, int $maxRequests = 5, int $timeWindow = 60, string $action = 'default'): bool
    {
        $rateLimitDir = __DIR__ . '/../rate_limit';
        
        // Ensure rate limit directory exists
        if (!file_exists($rateLimitDir)) {
            mkdir($rateLimitDir, 0755, true);
        }
        
        // Sanitize identifier for filename (hash it for security)
        $safeId = hash('sha256', $identifier . $action);
        $rateLimitFile = $rateLimitDir . '/' . $safeId . '.json';
        
        $now = time();
        $requests = [];
        
        // Load existing requests
        if (file_exists($rateLimitFile)) {
            $data = @json_decode(file_get_contents($rateLimitFile), true);
            if (is_array($data)) {
                $requests = $data;
            }
        }
        
        // Remove expired entries (older than time window)
        $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Check if limit exceeded
        if (count($requests) >= $maxRequests) {
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        
        // Save updated requests
        file_put_contents($rateLimitFile, json_encode(array_values($requests)), LOCK_EX);
        
        return true;
    }
    
    /**
     * Get the remaining requests for an identifier.
     * 
     * @param string $identifier Unique identifier
     * @param int $maxRequests Maximum number of requests allowed
     * @param int $timeWindow Time window in seconds
     * @param string $action Optional action name
     * @return int Number of remaining requests
     */
    function rate_limit_remaining(string $identifier, int $maxRequests = 5, int $timeWindow = 60, string $action = 'default'): int
    {
        $rateLimitDir = __DIR__ . '/../rate_limit';
        $safeId = hash('sha256', $identifier . $action);
        $rateLimitFile = $rateLimitDir . '/' . $safeId . '.json';
        
        if (!file_exists($rateLimitFile)) {
            return $maxRequests;
        }
        
        $data = @json_decode(file_get_contents($rateLimitFile), true);
        if (!is_array($data)) {
            return $maxRequests;
        }
        
        $now = time();
        $requests = array_filter($data, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        return max(0, $maxRequests - count($requests));
    }
    
    /**
     * Get client IP address.
     * 
     * @return string IP address
     */
    function get_client_ip(): string
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Clean up old rate limit files (older than specified seconds).
     * Call this periodically via cron or on low-traffic periods.
     * 
     * @param int $maxAge Maximum age in seconds (default: 1 hour)
     * @return int Number of files deleted
     */
    function rate_limit_cleanup(int $maxAge = 3600): int
    {
        $rateLimitDir = __DIR__ . '/../rate_limit';
        
        if (!file_exists($rateLimitDir)) {
            return 0;
        }
        
        $deleted = 0;
        $now = time();
        $files = glob($rateLimitDir . '/*.json');
        
        foreach ($files as $file) {
            if (filemtime($file) < ($now - $maxAge)) {
                @unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
}

