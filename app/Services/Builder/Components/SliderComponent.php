<?php

namespace App\Services\Builder\Components;

use App\Services\Builder\BaseComponent;

class SliderComponent extends BaseComponent
{
    public function getLabel(): string
    {
        return 'Slider';
    }

    public function getIcon(): string
    {
        return 'slider';
    }

    public function getSchema(): array
    {
        return [
            'label' => [
                'type' => 'text',
                'label' => 'Label',
                'default' => 'Parameter',
            ],
            'min' => [
                'type' => 'number',
                'label' => 'Min Value',
                'default' => 0,
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Max Value',
                'default' => 100,
            ],
            'step' => [
                'type' => 'number',
                'label' => 'Step',
                'default' => 1,
            ],
            'defaultValue' => [
                'type' => 'number',
                'label' => 'Default Value',
                'default' => 50,
            ],
            'unit' => [
                'type' => 'text',
                'label' => 'Unit',
                'default' => '',
            ],
        ];
    }

    public function render(array $properties): string
    {
        $label = e($properties['label'] ?? 'Parameter');
        $min = (int) ($properties['min'] ?? 0);
        $max = (int) ($properties['max'] ?? 100);
        $step = (int) ($properties['step'] ?? 1);
        $default = (int) ($properties['defaultValue'] ?? 50);
        $unit = e($properties['unit'] ?? '');
        $id = 'slider-'.uniqid();

        return <<<HTML
        <div class="space-y-2" x-data="{ value: {$default} }">
            <div class="flex items-center justify-between">
                <label for="{$id}" class="text-sm font-medium text-gray-700">{$label}</label>
                <span class="text-sm font-bold text-blue-600" x-text="value + '{$unit}'"></span>
            </div>
            <input
                type="range"
                id="{$id}"
                min="{$min}"
                max="{$max}"
                step="{$step}"
                x-model="value"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
            />
            <div class="flex justify-between text-xs text-gray-400">
                <span>{$min}{$unit}</span>
                <span>{$max}{$unit}</span>
            </div>
        </div>
        HTML;
    }
}
