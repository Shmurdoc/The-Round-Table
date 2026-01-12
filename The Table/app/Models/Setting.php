<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, $value, string $type = 'string', ?string $group = null): void
    {
        $data = [
            'value' => is_array($value) ? json_encode($value) : $value,
            'type' => $type,
        ];
        
        if ($group !== null) {
            $data['group'] = $group;
        }
        
        static::updateOrCreate(
            ['key' => $key],
            $data
        );
    }

    private static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function getValueAttribute($value)
    {
        return static::castValue($value, $this->type);
    }
}
