<?php

namespace App\Services;

use App\Models\ExperienceProject;
use App\Models\ExperienceTemplate;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExperienceBuilderService
{
    /**
     * Get all active templates.
     */
    public function getTemplates(): Collection
    {
        return ExperienceTemplate::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get a single template by slug.
     */
    public function getTemplate(string $slug): ExperienceTemplate
    {
        return ExperienceTemplate::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Create a new project from a template.
     */
    public function createProject(
        User $user,
        ?ExperienceTemplate $template,
        string $title,
        ?string $description = null,
    ): ExperienceProject {
        $config = $template
            ? $this->buildDefaultConfig($template)
            : ['components' => []];

        return ExperienceProject::create([
            'user_id' => $user->id,
            'template_id' => $template?->id,
            'title' => $title,
            'description' => $description,
            'config' => $config,
            'status' => 'draft',
            'slug' => Str::slug($title).'-'.Str::random(5),
        ]);
    }

    /**
     * Update a project's configuration.
     */
    public function updateProject(ExperienceProject $project, array $data): ExperienceProject
    {
        $project->update([
            'title' => $data['title'] ?? $project->title,
            'description' => $data['description'] ?? $project->description,
            'config' => $data['config'] ?? $project->config,
        ]);

        return $project->fresh();
    }

    /**
     * Get all projects for a user.
     */
    public function getUserProjects(User $user): Collection
    {
        return ExperienceProject::forUser($user->id)
            ->with('template')
            ->latest()
            ->get();
    }

    /**
     * Build default config from a template schema.
     */
    private function buildDefaultConfig(ExperienceTemplate $template): array
    {
        $components = collect($template->getComponents())->map(function (array $component) {
            return [
                'id' => (string) Str::uuid(),
                'type' => $component['type'],
                'label' => $component['label'] ?? $component['type'],
                'properties' => $component['default'] ?? [],
            ];
        })->toArray();

        return [
            'components' => $components,
            'settings' => $template->default_config['settings'] ?? [],
        ];
    }
}
