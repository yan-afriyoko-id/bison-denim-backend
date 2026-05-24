<?php

namespace App\Repositories;

use App\Interfaces\ConfigRepositoryInterface;
use App\Models\Config;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * Get all configs
     */
    public function getAll()
    {
        return Config::all();
    }

    /**
     * Get config by key
     */
    public function getByKey(string $key): ?Config
    {
        return Config::byKey($key)->first();
    }

    /**
     * Get value by key
     */
    public function getValue(string $key, mixed $default = null)
    {
        $config = $this->getByKey($key);
        if (!$config) {
            return $default;
        }
        return $config->getCastedValue();
    }

    /**
     * Get all configs
     */
    public function getAllAsKeyValue(): array
    {
        return Config::all()
            ->mapWithKeys(function ($config) {
                return [$config->key => $config->getCastedValue()];
            })
            ->toArray();
    }

    /**
     * Create new config
     */
    public function create(array $data): Config
    {
        return Config::create($data);
    }

    /**
     * Update config by key
     */
    public function updateByKey(string $key, array $data): Config
    {
        $config = $this->getByKey($key);

        if (!$config) {
            throw new \Exception("Config with key '{$key}' not found");
        }

        if (isset($data['value']) && $data['value'] instanceof UploadedFile) {
            $data['value'] = $this->handleFileUpload($key, $data['value']);
        }

        $config->update($data);
        return $config->refresh();
    }

    /**
     * Delete config by key
     */
    public function deleteByKey(string $key): bool
    {
        $config = $this->getByKey($key);
        if (!$config) {
            return false;
        }
        return $config->delete();
    }

    /**
     * Set or update config value by key
     */
    public function set(string $key, mixed $value, array $metadata = []): Config
    {
        $config = $this->getByKey($key);

        $data = array_merge([
            'value' => $value,
        ], $metadata);

        if ($config) {
            $config->update($data);
            return $config->refresh();
        }

        if (!isset($data['type'])) {
            $data['type'] = $this->detectType($value);
        }

        $data['key'] = $key;
        return Config::create($data);
    }

    /**
     * Auto-detect value type
     */
    private function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_array($value) || is_object($value)) {
            return 'json';
        }
        return 'string';
    }

    private function handleFileUpload(string $key, UploadedFile $file): string
    {
        $config = $this->getByKey($key);
        if ($config && $config->value) {
            if (strpos($config->value, 'http') === false && strpos($config->value, 'via.placeholder') === false) {
                $oldPath = $config->value;

                if (str_starts_with($oldPath, '/storage/')) {
                    $oldPath = substr($oldPath, 9);
                } elseif (str_starts_with($oldPath, 'storage/')) {
                    $oldPath = substr($oldPath, 8);
                }

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $path = 'configs/' . $key;
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $storedPath = Storage::disk('public')->putFileAs($path, $file, $filename);

        return $storedPath;
    }
}
