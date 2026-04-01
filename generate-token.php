<?php

/**
 * Generate Passport token for API testing
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "═══════════════════════════════════════════════════════════════\n";
echo "Generating Passport Token for Testing\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$user = User::find(1);

if (!$user) {
    echo "❌ User ID 1 not found\n";
    exit(1);
}

echo "✓ User found: " . $user->name . " (NIP: " . $user->nip . ")\n";

// Delete old tokens
$oldCount = $user->tokens()->count();
$user->tokens()->delete();
echo "✓ Deleted $oldCount old tokens\n\n";

// Create new token
$result = $user->createToken('api-test-token', ['*']);
$token = $result->accessToken;

echo "═══════════════════════════════════════════════════════════════\n";
echo "✓ NEW TOKEN GENERATED\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\nToken:\n" . $token . "\n\n";

echo "Usage:\n";
echo "curl -H 'Authorization: Bearer $token' \\\n";
echo "  http://127.0.0.1:8010/api/users/applications\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
