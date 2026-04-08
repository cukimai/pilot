<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @var string */
    protected $primaryKey = 'key';

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var array<int, string> */
    protected $guarded = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
