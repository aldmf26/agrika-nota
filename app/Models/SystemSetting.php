<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->find($key);

        if (! $setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Simpan / update nilai setting.
     */
    public static function set(string $key, mixed $value, ?string $description = null): static
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'description' => $description,
            ]
        );
    }

    /**
     * Cek apakah QR Code cetak nota diaktifkan.
     */
    public static function isQrEnabled(): bool
    {
        $val = static::get('enable_print_qr', '1');

        return in_array((string) $val, ['1', 'true', 'on', 'yes'], true);
    }
}
