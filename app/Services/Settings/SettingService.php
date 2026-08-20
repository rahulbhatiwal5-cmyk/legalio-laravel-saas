<?php

namespace App\Services\Settings;

use App\Models\Setting;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    public function allByType(string $type, ?string $modelRef = null): array
    {
        $query = Setting::query()->where('type', $type);

        if ($modelRef !== null) {
            $query->where('model_ref', $modelRef);
        }

        return $query->pluck('value', 'key')->all();
    }

    public function getMany(?string $type = null, ?string $modelRef = null): array
    {
        if ($type !== null) {
            return $this->allByType($type, $modelRef);
        }

        return Setting::query()->pluck('value', 'key')->all();
    }
}
