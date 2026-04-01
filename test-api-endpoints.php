<?php

/**
 * Test API endpoints directly
 */

$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiZWNmZmIyMTEyNzdhZjFlZmRlMTc5YzE5ZDRiZTU5ZDllOWQ1MGE4ODdlMjhhMTBiM2RmMzY1MGE0YmRkYWNmNDhhNGQ2NmY0Zjk1YTdkY2EiLCJpYXQiOjE3NzUwNjc3MzMuOTQ0ODMyLCJuYmYiOjE3NzUwNjc3MzMuOTQ0ODM2LCJleHAiOjE4MDY2MDM3MzMuOTEwNDcxLCJzdWIiOiIxIiwic2NvcGVzIjpbIioiXX0.UvSI2tTM0LbRC7VanEOXXlW5O8O_3jVPMQ3WfTHEZ0kOpwGpdFSI8zlQWHaIc-u6ptw6Qk0Qr4x7j4GGW7vjJeglYIlXoTY326Cp8SWVyY8uitnVrOoEb5WevMpQIgb054AvZdjEqcylJNm5dJ9ARC9kBzPaJukoqseHDTwL8UqAtQ3CK5t7ZSwO-eNe9wHAc_tZotT7r7wp7ai4gEhZOgPIalD-U-6ZJlfdPMvBM9bbyZfwBsu4kOInTA6bpr6z-P6mEGnBuM161mt2TJ4rF-tOiRDRgMPxXrLjvU0FzQtUzCJzSpoPCHb6qUcmYPFGX4vxHRoDxed7dd3FRwHGA9vkSeY1EpMDk4lZWs8m31icIGIQkv8sapTZRqzlLmBoyreys6_y3XHXkQY_Q2Z9TrHovbm8JDwQIqL3qZQjDcbIMGozJbfwCYf0M8GE495syweRm4E99EMAsIpqSnSqVpbcs7byHyLAwdF8aKsf0dceF5r1_R8w1jnJb8l-w5NUjoLizbvMxmpkr2xZtkhIcqwJH2RTBeg6kNTtWYN0ERM1GeQ-5bUFGXl34YprZwd6mRDHAoBkBueUcGGzm7-CDRmVL5LNk3RfM56lLtbsJYSHDpHHlcrmksv_IQePxB46sjzI5_TclDN9NBSnSWmyj4u58e_Ci9FGsi10TCJqqEw";

echo "═══════════════════════════════════════════════════════════════\n";
echo "Testing IAM API Endpoints\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Accept: application/json"
]);

// Test 1: Basic endpoint
echo "Test 1: GET /api/users/applications\n";
echo "──────────────────────────────────────────────────────────────\n";

curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8010/api/users/applications");
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Status: " . $httpCode . "\n";
if ($httpCode === "200") {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✓ Valid JSON\n";
        echo "Response structure:\n";
        echo "  - source: " . ($data['source'] ?? 'missing') . "\n";
        echo "  - user_id: " . ($data['user_id'] ?? 'missing') . "\n";
        echo "  - total_accessible_apps: " . ($data['total_accessible_apps'] ?? 0) . "\n";

        if (!empty($data['applications'])) {
            echo "  - applications: " . count($data['applications']) . " app(s)\n";
            foreach ($data['applications'] as $app) {
                echo "    • " . $app['name'] . " (" . $app['app_key'] . ")\n";
                echo "      Status: " . $app['status'] . "\n";
                echo "      URL: " . $app['app_url'] . "\n";
                echo "      Roles: " . $app['roles_count'] . "\n";
            }
        }
        echo "\n✓ Test 1 PASSED\n\n";
    } else {
        echo "✗ Invalid JSON: " . json_last_error_msg() . "\n";
        echo "Response: " . substr($response, 0, 500) . "\n\n";
    }
} else {
    echo "✗ HTTP Error\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 2: Detail endpoint
echo "Test 2: GET /api/users/applications/detail\n";
echo "──────────────────────────────────────────────────────────────\n";

curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8010/api/users/applications/detail");
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Status: " . $httpCode . "\n";
if ($httpCode === "200") {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✓ Valid JSON\n";
        echo "Response structure:\n";
        echo "  - source: " . ($data['source'] ?? 'missing') . "\n";
        echo "  - total_accessible_apps: " . ($data['total_accessible_apps'] ?? 0) . "\n";

        if (!empty($data['applications'])) {
            echo "  - applications: " . count($data['applications']) . " app(s)\n";
            foreach ($data['applications'] as $app) {
                echo "    • " . $app['name'] . " (" . $app['app_key'] . ")\n";
                echo "      Has metadata: " . (isset($app['metadata']) ? 'yes' : 'no') . "\n";
                echo "      Has user_profiles: " . (isset($app['user_profiles']) ? 'yes' : 'no') . "\n";
                if (isset($app['metadata']['logo'])) {
                    echo "      Logo available: " . ($app['metadata']['logo']['available'] ? 'yes' : 'no') . "\n";
                }
            }
        }
        echo "\n✓ Test 2 PASSED\n\n";
    } else {
        echo "✗ Invalid JSON: " . json_last_error_msg() . "\n";
        echo "Response: " . substr($response, 0, 500) . "\n\n";
    }
} else {
    echo "✗ HTTP Error\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

curl_close($ch);

echo "═══════════════════════════════════════════════════════════════\n";
echo "✓ All tests completed\n";
echo "═══════════════════════════════════════════════════════════════\n";
