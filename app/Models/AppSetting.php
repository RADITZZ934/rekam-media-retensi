<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $fillable = ['key', 'value', 'label', 'group', 'type'];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        try {
            return self::where('key', $key)->first()?->value ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    /**
     * Just-in-time table check/fix
     */
    public static function checkAndFix()
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

            self::create([
                'key' => 'retention_update_interval',
                'value' => '24',
                'label' => 'Interval Update Retensi (Jam)',
                'type' => 'number'
            ]);
        }
    }
}
