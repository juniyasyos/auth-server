<?php

use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Services\ApplicationRoleSyncService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'iam.backchannel_method' => 'hmac',
        'iam.backchannel_verify' => true,
    ]);
    Http::fake();
});

it('fetches roles with HMAC header when verification enabled', function () {
    $app = Application::factory()->create([
        'callback_url' => 'http://client.test',
        'app_key' => 'xyz',
    ]);

    $service = new ApplicationRoleSyncService();
    $service->fetchClientRoles($app);

    Http::assertSent(function ($request) use ($app) {
        $urlOK = $request->url() === 'http://client.test/api/iam/sync-roles?app_key=xyz';
        $header = config('sso.backchannel.signature_header');
        return $urlOK && ! empty($request->header($header));
    });
});

it('sends no auth headers when verification disabled', function () {
    config(['iam.backchannel_verify' => false]);

    $app = Application::factory()->create([
        'callback_url' => 'http://client.test',
        'app_key' => 'xyz',
    ]);

    $service = new ApplicationRoleSyncService();
    $service->fetchClientRoles($app);

    Http::assertSent(function ($request) use ($app) {
        return $request->url() === 'http://client.test/api/iam/sync-roles?app_key=xyz'
            && empty($request->header(config('sso.backchannel.signature_header')))
            && empty($request->header('Authorization'));
    });
});
