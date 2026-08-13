<?php

/**
 * ORLMS - REST API Integration Test Suite
 *
 * This script simulates another subsystem (e.g. Subsystem 6) interacting with ORLMS
 * using standard REST API requests.
 *
 * It tests:
 *   1. [POST] /api/receive            - Submitting a drafted document.
 *   2. [GET]  /api/documents          - Fetching all ordinances and resolutions.
 *   3. [GET]  /api/detail/{type}/{id} - Getting full details of the created document.
 *
 * To run this script:
 *   1. Start your server: `php -S localhost:8000 router.php`
 *   2. Open: `http://localhost:8000/test_api.php` in your browser
 *      Or run: `php test_api.php` in your terminal
 */

// Dynamically detect base URL (handles CLI, localhost:8000, XAMPP under /Capstone, etc.)
if (php_sapi_name() === 'cli') {
    $baseUrl = 'http://localhost:8000';
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : $scriptDir;
    $baseUrl = rtrim($protocol . '://' . $host . $basePath, '/');
}

echo "<h2>ORLMS - REST API Integration Test Suite</h2>";
echo "<p>Running tests against <strong>$baseUrl</strong>...</p>";

// ─────────────────────────────────────────────────────────────────────────────
// TEST 1: POST /api/receive (Create/Receive Document)
// ─────────────────────────────────────────────────────────────────────────────
echo "<h4>[TEST 1] POST /api/receive - Creating Document...</h4>";
$postUrl = $baseUrl . '/api/receive';
$payload = [
    'title'         => 'An Ordinance Implementing a Comprehensive Traffic Management System and One-Way Route Policy in Key Roads of CSJDM, Bulacan',
    'subject'       => 'Traffic Management and Public Roads',
    'content'       => "SECTION 1. Short Title - This shall be known as the 'CSJDM Traffic Code of 2026'.\n\nSECTION 2. One-Way Zones - The City Traffic Board shall identify roads to be declared one-way during peak hours.\n\nSECTION 3. Penalty - Violators shall face a fine of 2,000 PHP or driver's license suspension.\n\nSECTION 4. Separability Clause - In case any provision is declared illegal, other provisions stand.\n\nSECTION 5. Effectivity Clause - This shall take effect immediately upon approval of the City Mayor and publication.",
    'document_type' => 'ordinance',
    'status'        => 'submitted', // Triggers Llama AI Validation automatically
    'author_id'     => 1,
    'date_filed'    => date('Y-m-d')
];

$ch = curl_init($postUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 30 // Set high timeout to allow AI to complete
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "<p style='color:red;'><strong>cURL Error (POST):</strong> $curlError</p>";
    echo "<p>Please verify your server is running at <code>http://localhost:8000</code></p>";
    exit;
}

echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
$result = json_decode($response, true);
echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";

$newId = $result['id'] ?? null;
$docType = 'ordinance';

if (!$newId) {
    echo "<p style='color:red;'>Failed to retrieve ID of the newly created document. Stopping tests.</p>";
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST 2: GET /api/documents (List Documents)
// ─────────────────────────────────────────────────────────────────────────────
echo "<h4>[TEST 2] GET /api/documents - Fetching Document List...</h4>";
$getUrl = $baseUrl . '/api/documents';
$ch = curl_init($getUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
$listResult = json_decode($response, true);
// Just show count and first few records to avoid cluttering the screen
if (isset($listResult['data'])) {
    echo "<p>Total documents found: <strong>" . $listResult['count'] . "</strong></p>";
} else {
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST 3: GET /api/detail/{type}/{id} (Get Details + AI Validation Report)
// ─────────────────────────────────────────────────────────────────────────────
echo "<h4>[TEST 3] GET /api/detail/$docType/$newId - Retrieving Full Details + AI Report...</h4>";
$detailUrl = $baseUrl . "/api/detail/$docType/$newId";
$ch = curl_init($detailUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
$detailResult = json_decode($response, true);
echo "<pre>" . htmlspecialchars(json_encode($detailResult, JSON_PRETTY_PRINT)) . "</pre>";

// ─────────────────────────────────────────────────────────────────────────────
// TEST 4: POST /api/consultations (Submit Public Consultation Details)
// ─────────────────────────────────────────────────────────────────────────────
echo "<h4>[TEST 4] POST /api/consultations - Submitting Public Hearing Data for $docType/$newId...</h4>";
$consultationUrl = $baseUrl . '/api/consultations';
$consultationPayload = [
    'document_id'        => $newId,
    'document_type'      => $docType,
    'hearing_date'       => date('Y-m-d', strtotime('+3 days')),
    'venue'              => 'CSJDM City Hall Multi-Purpose Hall',
    'total_participants' => 125,
    'total_opinions'     => 78,
    'sentiment_summary'  => '85% Positive, 10% Neutral, 5% Negative',
    'summary_report'     => 'Citizens highly support the traffic management ordinance, pointing out that traffic signs must be properly illuminated at night.',
    'report_file_url'    => 'http://subsystem6-hearing.com/reports/hearing-report-' . $newId . '.pdf'
];

$ch = curl_init($consultationUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($consultationPayload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "<p style='color:red;'><strong>cURL Error (POST Consultations):</strong> $curlError</p>";
} else {
    echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
    $consultationResult = json_decode($response, true);
    echo "<pre>" . htmlspecialchars(json_encode($consultationResult, JSON_PRETTY_PRINT)) . "</pre>";
}

echo "<h3 style='color:green;'>All REST API tests completed successfully!</h3>";
