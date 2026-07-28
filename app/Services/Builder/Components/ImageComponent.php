<?php

namespace App\Services\Builder\Components;

use App\Services\Builder\BaseComponent;

class ImageComponent extends BaseComponent
{
    public function getLabel(): string
    {
        return 'Image';
    }

    public function getIcon(): string
    {
        return 'image';
    }

    public function getSchema(): array
    {
        return [
            'imageUrl' => [
                'type' => 'text',
                'label' => 'Image URL',
                'default' => '',
            ],
            'alt' => [
                'type' => 'text',
                'label' => 'Alt Text',
                'default' => 'Image description',
            ],
            'caption' => [
                'type' => 'text',
                'label' => 'Caption',
                'default' => '',
            ],
            'maxWidth' => [
                'type' => 'select',
                'label' => 'Max Width',
                'options' => ['max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-xl', 'max-w-2xl', 'w-full'],
                'default' => 'w-full',
            ],
        ];
    }

    public function render(array $properties): string
    {
        $imageUrl = e($properties['imageUrl'] ?? '');
        $alt = e($properties['alt'] ?? 'Image description');
        $caption = e($properties['caption'] ?? '');
        $maxWidth = $properties['maxWidth'] ?? 'w-full';

        $captionHtml = $caption
            ? "<p class=\"text-sm text-gray-500 text-center mt-2\">{$caption}</p>"
            : '';

        if ($imageUrl === '') {
            return <<<HTML
            <div class="{$maxWidth} mx-auto bg-gray-100 rounded-lg flex items-center justify-center h-48">
                <div class="text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm">No image URL set</p>
                </div>
            </div>
            {$captionHtml}
            HTML;
        }

        return <<<HTML
        <figure class="{$maxWidth} mx-auto">
            <img src="{$imageUrl}" alt="{$alt}" class="w-full rounded-lg shadow-sm" loading="lazy" />
            {$captionHtml}
        </figure>
        HTML;
    }
}
