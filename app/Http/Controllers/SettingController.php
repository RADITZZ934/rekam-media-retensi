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
        $this->checkTable();

        $settings = [
            'retention_update_interval' => AppSetting::where('key', 'retention_update_interval')->first()?->value ?? 24,
            'last_retention_update' => AppSetting::where('key', 'last_retention_update')->first()?->value ?? 'Belum pernah',
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
        $this->checkTable();

        if ($request->has('retention_update_interval')) {
            AppSetting::updateOrCreate(
                ['key' => 'retention_update_interval'],
                [
                    'value' => $request->input('retention_update_interval'),
                    'label' => 'Interval Update Retensi (Jam)',
                    'type' => 'number'
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
