<?php

namespace App\Services\Builder\Components;

use App\Services\Builder\BaseComponent;

class QuizComponent extends BaseComponent
{
    public function getLabel(): string
    {
        return 'Quiz';
    }

    public function getIcon(): string
    {
        return 'quiz';
    }

    public function getSchema(): array
    {
        return [
            'question' => [
                'type' => 'textarea',
                'label' => 'Question',
                'default' => 'What is the correct answer?',
            ],
            'options' => [
                'type' => 'text',
                'label' => 'Options (comma-separated)',
                'default' => 'Option A, Option B, Option C, Option D',
            ],
            'correctIndex' => [
                'type' => 'number',
                'label' => 'Correct Answer Index (0-based)',
                'default' => 0,
            ],
            'explanation' => [
                'type' => 'textarea',
                'label' => 'Explanation',
                'default' => '',
            ],
        ];
    }

    public function render(array $properties): string
    {
        $question = e($properties['question'] ?? 'What is the correct answer?');
        $optionsRaw = $properties['options'] ?? 'Option A, Option B, Option C, Option D';
        $correctIndex = (int) ($properties['correctIndex'] ?? 0);
        $explanation = e($properties['explanation'] ?? '');
        $id = 'quiz-'.uniqid();

        $options = array_map('trim', explode(',', $optionsRaw));

        $optionsHtml = '';
        foreach ($options as $index => $option) {
            $escapedOption = e($option);
            $optionsHtml .= <<<HTML
            <label
                class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 cursor-pointer transition-all hover:border-blue-300"
                x-on:click="selected = {$index}; showFeedback = true; isCorrect = ({$index} === {$correctIndex})"
                :class="{ 'border-green-500 bg-green-50': showFeedback && {$index} === {$correctIndex}, 'border-red-500 bg-red-50': showFeedback && selected === {$index} && {$index} !== {$correctIndex} }"
            >
                <span class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center text-sm font-medium text-gray-600"
                    :class="{ 'border-green-500 bg-green-500 text-white': showFeedback && {$index} === {$correctIndex}, 'border-red-500 bg-red-500 text-white': showFeedback && selected === {$index} && {$index} !== {$correctIndex} }"
                >
                    {$index}
                </span>
                <span class="text-sm text-gray-700">{$escapedOption}</span>
            </label>
            HTML;
        }

        $explanationHtml = $explanation !== ''
            ? <<<HTML
            <div x-show="showFeedback" x-transition class="mt-3 p-3 rounded-lg bg-blue-50 text-sm text-blue-800">
                💡 {$explanation}
            </div>
            HTML
            : '';

        return <<<HTML
        <div class="space-y-4" x-data="{ selected: null, showFeedback: false, isCorrect: false }">
            <h4 class="font-semibold text-gray-800">{$question}</h4>
            <div class="space-y-2">
                {$optionsHtml}
            </div>
            {$explanationHtml}
        </div>
        HTML;
    }
}
