<?php

namespace App\Services\Sso;

use App\Models\Application;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use JsonException;
use RuntimeException;

class TokenService
{
    /**
     * Issue a signed JWT for the given user and application.
     */
    public function issue(Authenticatable $user, Application $application): string
    {
        $secret = $this->getSecret();

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $issuedAt = Carbon::now();
        $expiresAt = $issuedAt->clone()->addSeconds($this->getTtl());

        $payload = [
            'iss' => $this->getIssuer(),
            'sub' => $user->getAuthIdentifier(),
            'email' => method_exists($user, 'getAttribute') ? $user->getAttribute('email') : null,
            'app' => $application->app_key,
            'iat' => $issuedAt->getTimestamp(),
            'exp' => $expiresAt->getTimestamp(),
        ];

        $headerSegment = $this->base64UrlEncode($this->encodeJson($header));
        $payloadSegment = $this->base64UrlEncode($this->encodeJson($payload));

        $signature = hash_hmac('sha256', $headerSegment . '.' . $payloadSegment, $secret, true);
        $signatureSegment = $this->base64UrlEncode($signature);

        return implode('.', [$headerSegment, $payloadSegment, $signatureSegment]);
    }

    /**
     * Verify the incoming token and return its payload.
     *
     * @return array<string, mixed>
     */
    public function verify(string $token): array
    {
        $secret = $this->getSecret();

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Malformed SSO token.');
        }

        [$headerSegment, $payloadSegment, $signatureSegment] = $parts;

        $expectedSignature = hash_hmac('sha256', $headerSegment . '.' . $payloadSegment, $secret, true);
        $providedSignature = $this->base64UrlDecode($signatureSegment);

        if (! hash_equals($expectedSignature, $providedSignature)) {
            throw new RuntimeException('Invalid SSO token signature.');
        }

        $payload = $this->decodeJson($this->base64UrlDecode($payloadSegment));

        if (! is_array($payload)) {
            throw new RuntimeException('Invalid SSO token payload.');
        }

        if (($payload['iss'] ?? null) !== $this->getIssuer()) {
            throw new RuntimeException('Invalid SSO issuer.');
        }

        $expiresAt = Carbon::createFromTimestamp($payload['exp'] ?? 0);

        if ($expiresAt->isPast()) {
            throw new RuntimeException('SSO token has expired.');
        }

        $applicationKey = $payload['app'] ?? null;

        if (! is_string($applicationKey) || empty($applicationKey)) {
            throw new RuntimeException('SSO token application is missing.');
        }

        $application = Application::enabled()
            ->where('app_key', $applicationKey)
            ->first();

        if ($application === null) {
            throw new RuntimeException('SSO application is not available.');
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        if ($decoded === false) {
            throw new RuntimeException('Failed decoding SSO token segment.');
        }

        return $decoded;
    }

    /**
     * @param  array<mixed>  $data
     */
    private function encodeJson(array $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Unable to encode SSO token segment.', 0, $exception);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJson(string $data): ?array
    {
        try {
            $decoded = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Unable to decode SSO token segment.', 0, $exception);
        }

        return is_array($decoded) ? $decoded : null;
    }

    private function getSecret(): string
    {
        $secret = (string) Config::get('sso.secret');

        if (empty($secret)) {
            throw new RuntimeException('SSO secret is not configured.');
        }

        return $secret;
    }

    private function getIssuer(): string
    {
        $issuer = (string) Config::get('sso.issuer');

        return $issuer !== '' ? $issuer : 'iam-server';
    }

    private function getTtl(): int
    {
        $ttl = (int) Config::get('sso.ttl', 300);

        return $ttl > 0 ? $ttl : 300;
    }
}
