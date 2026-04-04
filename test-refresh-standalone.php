#!/usr/bin/env php
<?php

require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Domain\Iam\Models\Application;
use App\Services\Sso\TokenService;
use App\Domain\Iam\Services\TokenBuilder;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test token refresh preservation
$user = User::first();
$app = Application::where('app_key', 'siimut')->first();

$tokenService = app(TokenService::class);
$tokenBuilder = app(TokenBuilder::class);

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
