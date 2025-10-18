<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Sso\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SsoRedirectController extends Controller
{
    public function __construct(private readonly TokenService $tokens)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app' => ['required', 'string'],
        ]);

        $application = Application::enabled()
            ->where('app_key', $validated['app'])
            ->first();

        if ($application === null) {
            throw ValidationException::withMessages([
                'app' => 'Application is not registered or disabled.',
            ]);
        }

        if (empty($application->callback_url)) {
            throw ValidationException::withMessages([
                'app' => 'Application callback URL is not configured.',
            ]);
        }

        $token = $this->tokens->issue($request->user(), $application);

        $separator = str_contains($application->callback_url, '?') ? '&' : '?';
        $redirectUrl = $application->callback_url . $separator . http_build_query([
            'token' => $token,
        ]);

        return redirect()->away($redirectUrl);
    }
}

