<?php

/**
 * ORLMS - Rate Limiter & DoS Protection Middleware
 *
 * Protects the application against brute force attacks, scraping,
 * and Denial of Service (DoS) traffic bursts.
 */
class RateLimiter
{
    /** @var string Cache directory path for rate limiter state */
    private static ?string $storageDir = null;

    /** @var bool If true, throws Exception instead of calling exit() (for testing) */
    public static bool $throwOnBlock = false;

    /**
     * Get or initialize storage directory for rate limit files.
     */
    private static function getStorageDir(): string
    {
        if (self::$storageDir === null) {
            $dir = defined('ROOT') ? ROOT . '/storage/ratelimit' : sys_get_temp_dir() . '/orlms_ratelimit';
            if (!file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }
            self::$storageDir = $dir;
        }
        return self::$storageDir;
    }

    /**
     * Main entry point to check rate limit for the current request.
     *
     * @param int $globalLimit Max requests allowed per minute for GET requests
     * @param int $sensitiveLimit Max requests allowed per minute for POST/API requests
     * @param int $windowSeconds Time window in seconds (default: 60s)
     */
    public static function check(int $globalLimit = 100, int $sensitiveLimit = 20, int $windowSeconds = 60): void
    {
        // Skip rate limiting when running via pure PHP CLI tool scripts (unless throwOnBlock is active for tests)
        if (php_sapi_name() === 'cli' && empty($_SERVER['HTTP_HOST']) && !self::$throwOnBlock) {
            return;
        }

        $ip = class_exists('GeoIPHelper') ? GeoIPHelper::getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $isSensitive = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' || 
                       str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/') ||
                       str_contains($_SERVER['REQUEST_URI'] ?? '', 'generate');

        $limit = $isSensitive ? $sensitiveLimit : $globalLimit;
        $bucketKey = md5($ip . ($isSensitive ? ':sensitive' : ':global'));

        $now = time();
        $filePath = self::getStorageDir() . '/' . $bucketKey . '.json';

        $timestamps = [];
        $fp = @fopen($filePath, 'c+');

        if ($fp) {
            if (flock($fp, LOCK_EX)) {
                $content = stream_get_contents($fp);
                if (!empty($content)) {
                    $decoded = json_decode($content, true);
                    if (is_array($decoded)) {
                        $timestamps = $decoded;
                    }
                }

                // Filter out timestamps outside the sliding window
                $timestamps = array_values(array_filter($timestamps, function ($t) use ($now, $windowSeconds) {
                    return ($now - $t) < $windowSeconds;
                }));

                $currentHits = count($timestamps);
                $remaining = max(0, $limit - ($currentHits + 1));
                $resetTime = $now + $windowSeconds;

                // Send rate limit headers
                if (!headers_sent()) {
                    header("X-RateLimit-Limit: {$limit}");
                    header("X-RateLimit-Remaining: {$remaining}");
                    header("X-RateLimit-Reset: {$resetTime}");
                }

                if ($currentHits >= $limit) {
                    // Limit exceeded!
                    flock($fp, LOCK_UN);
                    fclose($fp);

                    if (self::$throwOnBlock) {
                        throw new \RuntimeException("Rate limit exceeded ({$limit} req/min). Retry after {$windowSeconds}s.");
                    }

                    self::renderBlockResponse($windowSeconds, $limit);
                    exit();
                }

                // Record current timestamp & update file
                $timestamps[] = $now;
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, json_encode($timestamps));
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

    /**
     * Render HTTP 429 Too Many Requests response.
     */
    private static function renderBlockResponse(int $retryAfter, int $limit): void
    {
        http_response_code(429);
        if (!headers_sent()) {
            header("Retry-After: {$retryAfter}");
        }

        $isJson = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')
            || str_contains($_SERVER['REQUEST_URI'] ?? '', 'chat');

        if ($isJson) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'      => 429,
                'success'     => false,
                'error'       => 'Too Many Requests',
                'reply'       => "Masyado pong mabilis ang inyong pagtatanong (Rate Limit Exceeded). Pakiusap maghintay ng {$retryAfter} segundo bago magtanong muli.",
                'message'     => "Rate limit exceeded ({$limit} req/min). Please try again in {$retryAfter} seconds.",
                'retry_after' => $retryAfter
            ]);
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>429 Too Many Requests | ORLMS Security</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: 'Inter', sans-serif;
                    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
                    color: #f8fafc;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .card {
                    background: rgba(30, 41, 59, 0.7);
                    backdrop-filter: blur(16px);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 480px;
                    width: 100%;
                    text-align: center;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                }
                .icon {
                    width: 72px;
                    height: 72px;
                    background: rgba(239, 68, 68, 0.15);
                    border: 2px solid rgba(239, 68, 68, 0.4);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 24px;
                    color: #ef4444;
                    font-size: 32px;
                }
                h1 { font-size: 24px; font-weight: 700; margin-bottom: 12px; color: #ffffff; }
                p { font-size: 15px; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
                .badge {
                    display: inline-block;
                    background: rgba(239, 68, 68, 0.2);
                    color: #fca5a5;
                    padding: 6px 16px;
                    border-radius: 9999px;
                    font-size: 13px;
                    font-weight: 600;
                    margin-bottom: 24px;
                    border: 1px solid rgba(239, 68, 68, 0.3);
                }
                .btn {
                    display: inline-block;
                    background: #4f46e5;
                    color: white;
                    text-decoration: none;
                    font-weight: 600;
                    padding: 12px 28px;
                    border-radius: 12px;
                    transition: all 0.2s ease;
                }
                .btn:hover { background: #4338ca; transform: translateY(-2px); }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">🛡️</div>
                <div class="badge">HTTP 429 - Rate Limit Exceeded</div>
                <h1>Traffic Spike Detected</h1>
                <p>You have made too many requests in a short period. To protect system stability and prevent DoS attacks, your access is temporarily throttled.</p>
                <p>Please wait <strong><?= $retryAfter ?> seconds</strong> before refreshing this page.</p>
                <a href="javascript:location.reload();" class="btn">Retry Request</a>
            </div>
        </body>
        </html>
        <?php
    }
}
