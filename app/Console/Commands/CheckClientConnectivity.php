<?php

namespace App\Console\Commands;

use App\Domain\Iam\Models\Application;
use App\Services\JWTTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckClientConnectivity extends Command
{
    protected $signature = 'iam:check-client
                            {app_key=siimut : app_key of the client application to check}
                            {--role-sync-mode= : override role sync mode (pull|push)}
                            {--no-auth : skip signature/jwt header generation for basic connectivity}';

    protected $description = 'Check connectivity to a client application via backchannel endpoints';

    public function handle(): int
    {
        $appKey = $this->argument('app_key');
        $noAuth = $this->option('no-auth');

        $application = Application::where('app_key', $appKey)->first();

        if (! $application) {
            $this->error("Application with app_key='{$appKey}' not found.");
            return self::FAILURE;
        }

        $this->info("Checking client connectivity for app: {$application->app_key} (id: {$application->id})");
        $this->line('Client callback URL: ' . $application->callback_url);
        $this->line('Client backchannel URL: ' . $application->backchannel_url);

        $pipelines = [
            [
                'name' => 'health',
                'method' => 'GET',
                'url' => $this->buildUrl($application, '/api/iam/health'),
                'body' => null,
            ],
        ];

        $results = [];

        foreach ($pipelines as $endpoint) {
            [$ok, $status, $message] = $this->executeEndpoint($application, $endpoint, $noAuth);
            $results[] = [
                'Endpoint' => $endpoint['name'],
                'Method' => $endpoint['method'],
                'URL' => $endpoint['url'],
                'Status' => $status,
                'Result' => $ok ? 'OK' : 'FAIL',
                'Info' => $message,
            ];
        }

        $this->table(array_keys($results[0]), $results);

        return collect($results)->every(fn($r) => $r['Result'] === 'OK') ? self::SUCCESS : self::FAILURE;
    }

    protected function buildUrl(Application $application, string $path): string
    {
        $base = $application->backchannel_url ?: $application->callback_url;

        if (! $base) {
            throw new \RuntimeException('No backchannel or callback url configured for this application');
        }

        $base = rtrim($base, '/');
        return $base . $path . '?app_key=' . urlencode($application->app_key);
    }

    protected function executeEndpoint(Application $application, array $endpoint, bool $noAuth): array
    {
        $headers = ['Accept' => 'application/json'];

        if (! $noAuth) {
            // Health check via bearer token verifying client JWT support
            $token = app(JWTTokenService::class)->generateBackchannelToken($application);
            $headers['Authorization'] = 'Bearer ' . $token;
        }


        try {
            $request = Http::withHeaders($headers)->timeout(15);

            if ($endpoint['method'] === 'POST') {
                $response = $request->post($endpoint['url'], $endpoint['body']);
            } else {
                $response = $request->get($endpoint['url']);
            }

            return [
                $response->successful(),
                $response->status(),
                $response->body(),
            ];
        } catch (\Throwable $e) {
            return [false, 'N/A', $e->getMessage()];
        }
    }
}
