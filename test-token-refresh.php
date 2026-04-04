<?php

// Test token refresh preservation
$user = \App\Models\User::first();
$app = \App\Domain\Iam\Models\Application::where('app_key', 'siimut')->first();

$tokenService = app(\App\Services\Sso\TokenService::class);
$tokenBuilder = app(\App\Domain\Iam\Services\TokenBuilder::class);

// Create an original token (like from /sso/redirect)
$originalToken = $tokenService->issue($user, $app);

// Decode original token to see its structure
echo "=== Original Token ===\n";
$originalClaims = $tokenBuilder->decode($originalToken);
echo "App in extra: " . ($originalClaims->extra['app'] ?? 'NOT FOUND') . "\n";

// Manually parse to see raw payload
list($header, $payload, $signature) = explode('.', $originalToken);
$decoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
echo "App in raw payload: " . ($decoded['app'] ?? 'NOT FOUND') . "\n";

// Now refresh the token
echo "\n=== Refreshing Token ===\n";
$refreshedToken = $tokenBuilder->refresh($originalToken);

// Decode refreshed token to verify app is preserved
$refreshedClaims = $tokenBuilder->decode($refreshedToken);
echo "App in refreshed extra: " . ($refreshedClaims->extra['app'] ?? 'NOT FOUND') . "\n";

// Manually parse refreshed token
list($header, $payload, $signature) = explode('.', $refreshedToken);
$decoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
echo "App in refreshed raw payload: " . ($decoded['app'] ?? 'NOT FOUND') . "\n";

// Try to verify the token with TokenService
echo "\n=== Verification ===\n";
try {
    $verified = $tokenService->verify($refreshedToken);
    echo "✅ Token verification SUCCEEDED\n";
    echo "Verified app: " . ($verified['app_key'] ?? 'NOT FOUND') . "\n";
} catch (\Exception $e) {
    echo "❌ Token verification FAILED:\n";
    echo $e->getMessage() . "\n";
}
