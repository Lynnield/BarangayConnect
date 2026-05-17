<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'setting_group', 'setting_type', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('setting_key', $key)->first();

            return $setting ? $setting->setting_value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    public static function setWithMeta(
        string $key,
        mixed $value,
        string $group = 'general',
        string $type = 'string',
        ?string $description = null
    ): void {
        static::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_group' => $group,
                'setting_type' => $type,
                'description' => $description,
            ]
        );
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return filter_var(static::get($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function int(string $key, int $default = 0): int
    {
        return (int) static::get($key, (string) $default);
    }

    public static function getGroup(string $group): array
    {
        return static::where('setting_group', $group)->pluck('setting_value', 'setting_key')->toArray();
    }
}
