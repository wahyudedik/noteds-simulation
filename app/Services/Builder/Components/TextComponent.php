<?php

namespace App\Services\Builder\Components;

use App\Services\Builder\BaseComponent;

class TextComponent extends BaseComponent
{
    public function getLabel(): string
    {
        return 'Text';
    }

    public function getIcon(): string
    {
        return 'text';
    }

    public function getSchema(): array
    {
        return [
            'content' => [
                'type' => 'textarea',
                'label' => 'Content',
                'default' => 'Enter your text here...',
            ],
            'tag' => [
                'type' => 'select',
                'label' => 'Tag',
                'options' => ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                'default' => 'p',
            ],
            'fontSize' => [
                'type' => 'select',
                'label' => 'Font Size',
                'options' => ['text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl'],
                'default' => 'text-base',
            ],
            'color' => [
                'type' => 'color',
                'label' => 'Text Color',
                'default' => '#1f2937',
            ],
            'align' => [
                'type' => 'select',
                'label' => 'Alignment',
                'options' => ['left', 'center', 'right'],
                'default' => 'left',
            ],
        ];
    }

    public function render(array $properties): string
    {
        $content = e($properties['content'] ?? '');
        $tag = $properties['tag'] ?? 'p';
        $fontSize = $properties['fontSize'] ?? 'text-base';
        $color = e($properties['color'] ?? '#1f2937');
        $align = $properties['align'] ?? 'left';

        $alignClass = match ($align) {
            'center' => 'text-center',
            'right' => 'text-right',
            default => 'text-left',
        };

        $allowedTags = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $tag = in_array($tag, $allowedTags) ? $tag : 'p';

        $tagClass = match ($tag) {
            'h1' => 'font-bold',
            'h2' => 'font-bold',
            'h3' => 'font-semibold',
            'h4' => 'font-semibold',
            default => '',
        };

        return "<{$tag} class=\"{$fontSize} {$alignClass} {$tagClass}\" style=\"color: {$color}\">{$content}</{$tag}>";
    }
}
