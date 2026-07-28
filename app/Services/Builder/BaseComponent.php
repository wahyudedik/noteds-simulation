<?php

namespace App\Services\Builder;

abstract class BaseComponent
{
    /**
     * Get the human-readable label for this component.
     */
    abstract public function getLabel(): string;

    /**
     * Get the SVG icon identifier for this component.
     */
    abstract public function getIcon(): string;

    /**
     * Get the property schema for this component.
     * Each key is a property name => ['type', 'label', 'default', ...].
     */
    abstract public function getSchema(): array;

    /**
     * Render the component to HTML using its properties.
     */
    abstract public function render(array $properties): string;

    /**
     * Get default property values.
     */
    public function getDefaults(): array
    {
        return collect($this->getSchema())
            ->mapWithKeys(fn (array $def, string $key) => [$key => $def['default'] ?? null])
            ->toArray();
    }
}
