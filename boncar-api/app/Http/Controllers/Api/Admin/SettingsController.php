<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json(Setting::all());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'required|numeric',
        ]);

        foreach ($validated['settings'] as $settingData) {
            Setting::where('key', $settingData['key'])->update(['value' => $settingData['value']]);
        }

        // Clear the cache so the new settings are used immediately
        Cache::forget('app-settings');

        return response()->json(['message' => 'Settings updated successfully.']);
    }
}