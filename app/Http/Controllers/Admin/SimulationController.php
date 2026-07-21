<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Simulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;

class SimulationController extends Controller
{
    /**
     * Display list of all simulations (admin).
     */
    public function index(): View
    {
        $simulations = Simulation::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.simulations.index', compact('simulations'));
    }

    /**
     * Show the upload form.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.simulations.create', compact('categories'));
    }

    /**
     * Store a newly uploaded simulation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'simulation_file' => 'required|file|mimes:zip|max:51200', // 50MB max
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        $user = auth()->user();

        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $existing = Simulation::where('slug', $slug)->count();
        if ($existing > 0) {
            $slug = $slug.'-'.Str::random(5);
        }

        // Handle simulation zip upload
        $zipFile = $request->file('simulation_file');
        $zipFilename = time().'_'.Str::random(10).'.zip';
        $zipPath = $zipFile->storeAs($slug, $zipFilename, 'simulations');

        // Extract zip and read manifest
        $extractPath = storage_path('app/simulations/'.$slug.'/extracted');
        $zip = new ZipArchive;
        $entryPoint = 'index.html';
        $manifestData = [];

        if ($zip->open(storage_path('app/simulations/'.$zipPath)) === true) {
            $zip->extractTo($extractPath);
            $zip->close();

            // Try to read manifest.json
            $manifestPath = $extractPath.'/manifest.json';
            if (file_exists($manifestPath)) {
                $manifestData = json_decode(file_get_contents($manifestPath), true) ?? [];

                // Use manifest data to override form data
                if (empty($validated['description']) && ! empty($manifestData['description'])) {
                    $validated['description'] = $manifestData['description'];
                }
                if (! empty($manifestData['tags']) && is_array($manifestData['tags'])) {
                    $validated['tags'] = implode(',', $manifestData['tags']);
                }
                if (! empty($manifestData['entryPoint'])) {
                    $entryPoint = $manifestData['entryPoint'];
                }
            }
        }

        // Handle thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif (file_exists($extractPath.'/assets/images/thumbnail.png')) {
            // Try to use thumbnail from zip
            $thumbnailPath = Storage::disk('public')->putFile('thumbnails', $extractPath.'/assets/images/thumbnail.png');
        }

        // Create simulation record
        $simulation = Simulation::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'thumbnail' => $thumbnailPath,
            'version' => $manifestData['version'] ?? '1.0.0',
            'zip_path' => $zipPath,
            'entry_point' => $entryPoint,
            'is_published' => $request->boolean('is_published', false),
            'published_at' => $request->boolean('is_published', false) ? now() : null,
        ]);

        return redirect()->route('admin.simulations.show', $simulation)
            ->with('success', 'Simulation uploaded successfully!');
    }

    /**
     * Display a simulation details (admin).
     */
    public function show(Simulation $simulation): View
    {
        $simulation->load('user');

        return view('admin.simulations.show', compact('simulation'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Simulation $simulation): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.simulations.edit', compact('simulation', 'categories'));
    }

    /**
     * Update a simulation.
     */
    public function update(Request $request, Simulation $simulation): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($simulation->thumbnail) {
                Storage::disk('public')->delete($simulation->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Handle publish state change
        if ($request->boolean('is_published') && ! $simulation->is_published) {
            $validated['published_at'] = now();
        }

        $simulation->update($validated);

        return redirect()->route('admin.simulations.show', $simulation)
            ->with('success', 'Simulation updated successfully!');
    }

    /**
     * Delete a simulation.
     */
    public function destroy(Simulation $simulation): RedirectResponse
    {
        // Delete zip file and extracted folder from simulations disk
        if ($simulation->zip_path) {
            Storage::disk('simulations')->delete($simulation->zip_path);
            // Delete extracted folder
            $extractDir = storage_path('app/simulations/'.$simulation->slug.'/extracted');
            if (is_dir($extractDir)) {
                $this->deleteDirectory($extractDir);
            }
            // Delete the parent slug directory if empty
            $slugDir = storage_path('app/simulations/'.$simulation->slug);
            if (is_dir($slugDir) && $this->isDirectoryEmpty($slugDir)) {
                rmdir($slugDir);
            }
        }

        // Delete thumbnail
        if ($simulation->thumbnail) {
            Storage::disk('public')->delete($simulation->thumbnail);
        }

        $simulation->delete();

        return redirect()->route('admin.simulations.index')
            ->with('success', 'Simulation deleted successfully!');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(Simulation $simulation): RedirectResponse
    {
        $simulation->update([
            'is_published' => ! $simulation->is_published,
            'published_at' => ! $simulation->is_published ? now() : null,
        ]);

        $status = $simulation->is_published ? 'published' : 'unpublished';

        return redirect()->back()->with('success', "Simulation {$status} successfully!");
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Simulation $simulation): RedirectResponse
    {
        $simulation->update([
            'is_featured' => ! $simulation->is_featured,
        ]);

        $status = $simulation->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()->with('success', "Simulation {$status} successfully!");
    }

    /**
     * Recursively delete a directory.
     */
    private function deleteDirectory(string $path): void
    {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $this->deleteDirectory($path.DIRECTORY_SEPARATOR.$file);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    /**
     * Check if a directory is empty.
     */
    private function isDirectoryEmpty(string $path): bool
    {
        $items = array_diff(scandir($path), ['.', '..']);

        return count($items) === 0;
    }
}
