<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Domain\Iam\Models\Application;
use App\Services\Sso\TokenService;
use App\Domain\Iam\Services\TokenBuilder;

class TokenRefreshTest extends TestCase
{
    public function testTokenRefreshPreservesAppField(): void
    {
        // Get a user and application
        $user = User::first();
        $app = Application::where('app_key', 'siimut')->first();

        $tokenService = app(TokenService::class);
        $tokenBuilder = app(TokenBuilder::class);

        // Create an original token (like from /sso/redirect)
        $originalToken = $tokenService->issue($user, $app);

        // Parse original token
        list($header, $payload, $signature) = explode('.', $originalToken);
        $originalDecoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        
        $this->assertArrayHasKey('app', $originalDecoded, 'Original token should have app field');
        $this->assertEquals('siimut', $originalDecoded['app']);

        // Refresh the token
        $refreshedToken = $tokenBuilder->refresh($originalToken);

        // Parse refreshed token
        list($header, $payload, $signature) = explode('.', $refreshedToken);
        $refreshedDecoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        
        $this->assertArrayHasKey('app', $refreshedDecoded, 'Refreshed token should have app field');
        $this->assertEquals('siimut', $refreshedDecoded['app']);

        // Verify the refreshed token works
        $verified = $tokenService->verify($refreshedToken);
        $this->assertNotNull($verified);
    }
}
