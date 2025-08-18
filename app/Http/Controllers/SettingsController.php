<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function __construct(
        private TmdbService $tmdbService
    ) {}

    /**
     * Get countries for settings dropdown
     */
    public function getCountries()
    {
        try {
            $countries = $this->tmdbService->getCountries();
            return response()->json($countries);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch countries'], 500);
        }
    }

    /**
     * Update user's country setting
     */
    public function updateCountry(Request $request)
    {
        Log::debug('Updating user country', ['country' => $request->country]);
        $request->validate([
            'country' => 'required|string|size:2',
        ]);

        $user = $request->user();
        $user->update([
            'country' => $request->country,
        ]);

        return response()->json(['message' => 'Country updated successfully']);
    }
}
