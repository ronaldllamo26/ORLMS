<?php
/**
 * ORLMS - GeoIP Helper
 *
 * Utility class to detect visitor client IP address and lookup
 * geographic location (City, Region, Country).
 */

class GeoIPHelper
{
    /**
     * Gets the real IP address of the client, considering proxy headers.
     *
     * @return string
     */
    public static function getClientIp(): string
    {
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipSources as $key) {
            if (!empty($_SERVER[$key])) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Looks up location (City, Region, Country) for a given IP address.
     *
     * @param string|null $ip
     * @return string
     */
    public static function getLocation(?string $ip = null): string
    {
        $ip = $ip ?? self::getClientIp();

        // Check if IP is localhost / private loopback
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return 'Local Network (Localhost)';
        }

        try {
            // Use ip-api.com free endpoint with 2-second timeout
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2,
                    'header'  => "User-Agent: ORLMS-GeoIP/1.0\r\n"
                ]
            ]);

            $url = "http://ip-api.com/json/" . urlencode($ip) . "?fields=status,country,regionName,city";
            $json = @file_get_contents($url, false, $context);

            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    $parts = array_filter([
                        $data['city'] ?? '',
                        $data['regionName'] ?? '',
                        $data['country'] ?? ''
                    ]);
                    return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully on timeout/error
        }

        return 'Unknown Location';
    }
}
