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

        $labelsArray = array_map('trim', explode(',', $labels));
        $valuesArray = array_map('floatval', array_map('trim', explode(',', $values)));
        $count = count($valuesArray);
        $maxVal = max($valuesArray) * 1.2;
        if ($maxVal <= 0) {
            $maxVal = 100;
        }

        if ($type === 'bar') {
            $barsHtml = '';
            for ($i = 0; $i < $count; $i++) {
                $pct = ($valuesArray[$i] / $maxVal) * 100;
                $label = e($labelsArray[$i] ?? '');
                $barsHtml .= <<<BARS
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-full rounded-t transition-all duration-300" style="height: {$pct}%; background-color: {$color}"></div>
                                <span class="text-xs text-gray-500 mt-1">{$label}</span>
                            </div>
                BARS;
            }

            return <<<HTML
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700">{$title}</h4>
                <div class="relative h-48 bg-gray-50 rounded-lg p-4 overflow-hidden">
                    <div class="flex items-end justify-around h-full gap-2">
            {$barsHtml}
                    </div>
                </div>
            </div>
            HTML;
        }

        // Line chart: build static SVG
        $points = [];
        $lines = [];
        for ($i = 0; $i < $count; $i++) {
            $cx = $count > 1 ? ($i / ($count - 1)) * 380 + 10 : 195;
            $cy = 170 - ($valuesArray[$i] / $maxVal * 160);
            $points[] = '<circle cx="'.round($cx, 2).'" cy="'.round($cy, 2).'" r="4" fill="'.$color.'" />';

            if ($i > 0) {
                $prevCx = ($i - 1) / ($count - 1) * 380 + 10;
                $prevCy = 170 - ($valuesArray[$i - 1] / $maxVal * 160);
                $lines[] = '<line x1="'.round($prevCx, 2).'" y1="'.round($prevCy, 2).'" x2="'.round($cx, 2).'" y2="'.round($cy, 2).'" stroke="'.$color.'" stroke-width="2" fill="none" />';
            }
        }

        $svgContent = implode("\n                            ", $lines)."\n                            ".implode("\n                            ", $points);

        return <<<HTML
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700">{$title}</h4>
                <div class="relative h-48 bg-gray-50 rounded-lg p-4 overflow-hidden">
                    <svg class="w-full h-full" viewBox="0 0 400 180" preserveAspectRatio="none">
                            {$svgContent}
                    </svg>
                </div>
            </div>
            HTML;
    }
}
