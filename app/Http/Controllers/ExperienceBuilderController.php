<?php

namespace App\Http\Controllers;

use App\Models\ExperienceProject;
use App\Services\Builder\ComponentRegistry;
use App\Services\Builder\ExportService;
use App\Services\Builder\PublishService;
use App\Services\ExperienceBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExperienceBuilderController extends Controller
{
    public function __construct(
        private ExperienceBuilderService $builderService,
    ) {}

    /**
     * Builder dashboard — list user's projects.
     */
    public function index(): View
    {
        $projects = $this->builderService->getUserProjects(Auth::user());

        return view('studio.builder.index', compact('projects'));
    }

    /**
     * Template selection page.
     */
    public function templates(): View
    {
        $templates = $this->builderService->getTemplates();

        return view('studio.builder.templates', compact('templates'));
    }

    /**
     * Create a new project from a template (or blank).
     */
    public function createProject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_slug' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $template = null;
        if (! empty($validated['template_slug'])) {
            $template = $this->builderService->getTemplate($validated['template_slug']);
        }

        $project = $this->builderService->createProject(
            Auth::user(),
            $template,
            $validated['title'],
            $validated['description'] ?? null,
        );

        return redirect()
            ->route('studio.builder.projects.edit', $project->slug)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Editor page for a project.
     */
    public function edit(ExperienceProject $project): View
    {
        $this->authorizeProject($project);

        $project->load('template');
        $availableComponents = (new ComponentRegistry)->getAvailableComponents();

        return view('studio.builder.edit', compact('project', 'availableComponents'));
    }

    /**
     * Save project updates (AJAX).
     */
    public function update(Request $request, ExperienceProject $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'config' => 'required|array',
        ]);

        $this->builderService->updateProject($project, $validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'version' => $project->fresh()->version]);
        }

        return back()->with('success', 'Project saved.');
    }

    /**
     * Preview the project (AJAX returns HTML).
     */
    public function preview(ExperienceProject $project)
    {
        $this->authorizeProject($project);

        $registry = new ComponentRegistry;
        $components = $project->getComponents();

        $html = '';
        foreach ($components as $component) {
            $html .= $registry->render($component['type'], $component['properties'] ?? []);
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Show publish modal for a project.
     */
    public function publish(ExperienceProject $project): View
    {
        $this->authorizeProject($project);

        $categories = DB::table('simulations')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('studio.builder.publish', compact('project', 'categories'));
    }

    /**
     * Handle publish form submission (AJAX).
     */
    public function publishConfirm(ExperienceProject $project, Request $request): JsonResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'subcategory' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        try {
            $publishService = new PublishService;
            $simulation = $publishService->publish($project, [
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'description' => $validated['description'] ?? null,
                'thumbnail' => $request->file('thumbnail'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Experience published to platform!',
                'simulation_id' => $simulation->id,
                'redirect' => route('simulations.show', $simulation->slug),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unpublish a project from the platform.
     */
    public function unpublish(ExperienceProject $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $publishService = new PublishService;
        $publishService->unpublish($project);

        return back()->with('success', 'Experience unpublished from platform.');
    }

    /**
     * Export a project to ZIP file.
     */
    public function export(ExperienceProject $project)
    {
        $this->authorizeProject($project);

        $exportService = new ExportService;
        $zipPath = $exportService->export($project);

        return response()->download($zipPath, $project->slug.'.zip')->deleteFileAfterSend(true);
    }

    /**
     * Delete a project.
     */
    public function destroy(ExperienceProject $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $project->delete();

        return redirect()
            ->route('studio.builder.index')
            ->with('success', 'Project deleted.');
    }

    /**
     * Ensure the authenticated user owns this project.
     */
    private function authorizeProject(ExperienceProject $project): void
    {
        abort_if(
            $project->user_id !== Auth::id(),
            403,
            'You do not have access to this project.'
        );
    }
}
