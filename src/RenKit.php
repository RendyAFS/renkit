<?php

namespace RendyAFS\RenKit;

class RenKit
{
    /**
     * Get the current RenKit version.
     */
    public function version(): string
    {
        return config('renkit.version', '1.0.0');
    }

    /**
     * Get all enabled features.
     *
     * @return array<string, bool>
     */
    public function getFeatures(): array
    {
        return (array) config('renkit.features', []);
    }

    /**
     * Check if a specific feature is enabled.
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) config("renkit.features.{$feature}", false);
    }
}
