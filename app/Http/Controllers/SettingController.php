<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SettingController extends Controller
{
    /**
     * Get all app settings
     */
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya Administrator yang dapat mengakses halaman ini.',
            ], 403);
        }

        $this->checkTable();

        $settings = [
            'retention_update_interval' => AppSetting::where('key', 'retention_update_interval')->first()?->value ?? 24,
            'retention_update_unit' => AppSetting::where('key', 'retention_update_unit')->first()?->value ?? 'hours',
            'last_retention_update' => AppSetting::where('key', 'last_retention_update')->first()?->value ?? 'Belum pernah',
            'mock_ai_interceptor' => AppSetting::where('key', 'mock_ai_interceptor')->first()?->value === 'true',
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'Administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya Administrator yang dapat mengubah pengaturan ini.',
            ], 403);
        }

        $this->checkTable();

        if ($request->has('retention_update_interval')) {
            AppSetting::updateOrCreate(
                ['key' => 'retention_update_interval'],
                [
                    'value' => $request->input('retention_update_interval'),
                    'label' => 'Interval Update Retensi',
                    'type' => 'number'
                ]
            );
        }

        if ($request->has('retention_update_unit')) {
            AppSetting::updateOrCreate(
                ['key' => 'retention_update_unit'],
                [
                    'value' => $request->input('retention_update_unit'),
                    'label' => 'Satuan Interval Update Retensi',
                    'type' => 'text'
                ]
            );
        }

        if ($request->has('mock_ai_interceptor')) {
            AppSetting::updateOrCreate(
                ['key' => 'mock_ai_interceptor'],
                [
                    'value' => $request->input('mock_ai_interceptor') ? 'true' : 'false',
                    'label' => 'Mock AI Interceptor',
                    'type' => 'boolean'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Ensure table exists without migration (Just-in-time)
     */
    private function checkTable()
    {
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('label');
                $table->string('group')->default('general');
                $table->string('type')->default('text');
                $table->timestamps();
            });

            // Seed defaults
            AppSetting::create([
                'key' => 'retention_update_interval',
                'value' => '24',
                'label' => 'Interval Update Retensi (Jam)',
                'type' => 'number'
            ]);
        }
    }
}
