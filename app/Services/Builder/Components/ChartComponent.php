<?php

namespace App\Services\Builder\Components;

use App\Services\Builder\BaseComponent;

class ChartComponent extends BaseComponent
{
    public function getLabel(): string
    {
        return 'Chart';
    }

    public function getIcon(): string
    {
        return 'chart';
    }

    public function getSchema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'default' => 'Chart',
            ],
            'type' => [
                'type' => 'select',
                'label' => 'Chart Type',
                'options' => ['line', 'bar'],
                'default' => 'line',
            ],
            'labels' => [
                'type' => 'text',
                'label' => 'Labels (comma-separated)',
                'default' => 'Jan, Feb, Mar, Apr, May',
            ],
            'values' => [
                'type' => 'text',
                'label' => 'Values (comma-separated)',
                'default' => '10, 20, 15, 25, 30',
            ],
            'color' => [
                'type' => 'color',
                'label' => 'Line/Bar Color',
                'default' => '#3b82f6',
            ],
        ];
    }

    public function render(array $properties): string
    {
        $title = e($properties['title'] ?? 'Chart');
        $type = $properties['type'] ?? 'line';
        $labels = $properties['labels'] ?? 'Jan, Feb, Mar, Apr, May';
        $values = $properties['values'] ?? '10, 20, 15, 25, 30';
        $color = e($properties['color'] ?? '#3b82f6');
        $id = 'chart-'.uniqid();

        $labelsArray = array_map('trim', explode(',', $labels));
        $valuesArray = array_map('trim', explode(',', $values));

        $labelsJson = json_encode($labelsArray);
        $valuesJson = json_encode(array_map('floatval', $valuesArray));

        return <<<HTML
        <div class="space-y-2" x-data="{
            labels: {$labelsJson},
            values: {$valuesJson},
            get maxVal() { return Math.max(...this.values) * 1.2 || 100; }
        }">
            <h4 class="text-sm font-semibold text-gray-700">{$title}</h4>
            <div class="relative h-48 bg-gray-50 rounded-lg p-4 overflow-hidden">
                @if('{$type}' === 'bar')
                <div class="flex items-end justify-around h-full gap-2" x-ref="bars">
                    <template x-for="(val, i) in values" :key="i">
                        <div class="flex flex-col items-center flex-1">
                            <div
                                class="w-full rounded-t transition-all duration-300"
                                :style="'height: ' + (val / maxVal * 100) + '%; background-color: {$color}'"
                            ></div>
                            <span class="text-xs text-gray-500 mt-1" x-text="labels[i]"></span>
                        </div>
                    </template>
                </div>
                @else
                <svg class="w-full h-full" viewBox="0 0 400 180" preserveAspectRatio="none">
                    <template x-for="(val, i) in values" :key="i">
                        <g>
                            <circle
                                :cx="(i / (values.length - 1 || 1)) * 380 + 10"
                                :cy="170 - (val / maxVal * 160)"
                                r="4"
                                fill="{$color}"
                            />
                            <line
                                x1="0" y1="0" x2="0" y2="0"
                                :x1="i > 0 ? ((i - 1) / (values.length - 1 || 1)) * 380 + 10 : 0"
                                :y1="i > 0 ? 170 - (values[i-1] / maxVal * 160) : 170"
                                :x2="(i / (values.length - 1 || 1)) * 380 + 10"
                                :y2="170 - (val / maxVal * 160)"
                                stroke="{$color}"
                                stroke-width="2"
                                fill="none"
                            />
                        </g>
                    </template>
                </svg>
                @endif
            </div>
        </div>
        HTML;
    }
}
