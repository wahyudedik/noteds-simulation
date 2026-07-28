<?php

namespace App\Services\Builder;

use App\Services\Builder\Components\ChartComponent;
use App\Services\Builder\Components\ImageComponent;
use App\Services\Builder\Components\QuizComponent;
use App\Services\Builder\Components\SliderComponent;
use App\Services\Builder\Components\TextComponent;

class ComponentRegistry
{
    /**
     * Map of component type → class.
     */
    private const COMPONENTS = [
        'text' => TextComponent::class,
        'slider' => SliderComponent::class,
        'chart' => ChartComponent::class,
        'image' => ImageComponent::class,
        'quiz' => QuizComponent::class,
    ];

    /**
     * Get a component instance by type.
     */
    public function getComponent(string $type): ?BaseComponent
    {
        $class = self::COMPONENTS[$type] ?? null;

        return $class ? new $class : null;
    }

    /**
     * Get schema definition for a component type.
     */
    public function getSchema(string $type): ?array
    {
        $component = $this->getComponent($type);

        return $component?->getSchema();
    }

    /**
     * Get all available components with their schemas.
     */
    public function getAvailableComponents(): array
    {
        $components = [];

        foreach (self::COMPONENTS as $type => $class) {
            /** @var BaseComponent $instance */
            $instance = new $class;
            $components[$type] = [
                'type' => $type,
                'label' => $instance->getLabel(),
                'icon' => $instance->getIcon(),
                'schema' => $instance->getSchema(),
            ];
        }

        return $components;
    }

    /**
     * Render a component to HTML.
     */
    public function render(string $type, array $properties): ?string
    {
        $component = $this->getComponent($type);

        return $component?->render($properties);
    }

    /**
     * Get all supported component types.
     */
    public function getTypes(): array
    {
        return array_keys(self::COMPONENTS);
    }
}
