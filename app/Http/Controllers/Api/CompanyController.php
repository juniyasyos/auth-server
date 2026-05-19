<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show(Request $request, SettingService $settingService): JsonResponse
    {
        return response()->json([
            'name' => $settingService->get('company.name', 'Perusahaan Anandan'),
            'tagline' => $settingService->get('company.tagline', 'Melayani dengan Hati dan Profesionalisme'),
            'logo' => $settingService->get('company.logo', '/images/company/logo.png'),
            'address' => $settingService->get('company.address', 'Jl. Raya Kesehatan No. 123, Kecamatan Sejahtera'),
            'city' => $settingService->get('company.city', 'Jember'),
            'postal_code' => $settingService->get('company.postal_code', '68121'),
            'phone' => $settingService->get('company.phone', '(0331) 123456'),
            'email' => $settingService->get('company.email', 'info@citrahusada.co.id'),
            'website' => $settingService->get('company.website', 'https://citrahusada.co.id'),
            'director_name' => $settingService->get('company.director_name', 'dr. Andi Pratama, M.Kes'),
            'director_title' => $settingService->get('company.director_title', 'Direktur Utama'),
        ]);
    }
}
