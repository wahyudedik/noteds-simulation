<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    /**
     * List published simulations with pagination & filters.
     *
     * GET /api/simulations?category=&search=&sort=latest|popular|trending&page=
     */
    public function index(Request $request): JsonResponse
    {
        $query = Simulation::published()->with('user:id,name,avatar');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'popular' => $query->orderByDesc('play_count'),
            'trending' => $query->orderByDesc('view_count'),
            default => $query->latest('published_at'),
        };

        $simulations = $query->paginate($request->input('limit', 20), [
            'id', 'title', 'slug', 'description', 'category', 'thumbnail',
            'play_count', 'view_count', 'like_count', 'bookmark_count',
            'share_count', 'comment_count', 'average_rating', 'published_at',
        ]);

        return response()->json([
            'success' => true,
            'data' => $simulations,
        ]);
    }

    /**
     * Get a single simulation by slug.
     *
     * GET /api/simulations/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->with([
                'user:id,name,avatar,bio,role',
                'tagModels:id,name,slug',
            ])
            ->first();

        if (! $simulation) {
            return response()->json([
                'success' => false,
                'message' => 'Simulasi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $simulation,
        ]);
    }

    /**
     * Get comments for a simulation.
     *
     * GET /api/simulations/{slug}/comments?page=
     */
    public function comments(string $slug, Request $request): JsonResponse
    {
        $simulation = Simulation::published()->where('slug', $slug)->first();

        if (! $simulation) {
            return response()->json([
                'success' => false,
                'message' => 'Simulasi tidak ditemukan.',
            ], 404);
        }

        $comments = Comment::where('simulation_id', $simulation->id)
            ->with('user:id,name,avatar')
            ->orderByDesc('created_at')
            ->paginate($request->input('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    /**
     * Get trending simulations.
     *
     * GET /api/trending?limit=
     */
    public function trending(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 10), 50);

        $simulations = Simulation::published()
            ->with('user:id,name,avatar')
            ->orderByDesc('play_count')
            ->limit($limit)
            ->get([
                'id', 'title', 'slug', 'description', 'category', 'thumbnail',
                'play_count', 'view_count', 'like_count', 'average_rating',
                'published_at',
            ]);

        return response()->json([
            'success' => true,
            'data' => $simulations,
        ]);
    }

    /**
     * Get all active categories with simulation counts.
     *
     * GET /api/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'slug', 'icon', 'color']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get simulations by a specific creator.
     *
     * GET /api/creator/{id}/simulations?page=
     */
    public function creatorSimulations(int $id, Request $request): JsonResponse
    {
        $creator = User::where('id', $id)
            ->where('role', 'creator')
            ->first();

        if (! $creator) {
            return response()->json([
                'success' => false,
                'message' => 'Kreator tidak ditemukan.',
            ], 404);
        }

        $simulations = Simulation::where('user_id', $creator->id)
            ->published()
            ->orderByDesc('published_at')
            ->paginate($request->input('limit', 20), [
                'id', 'title', 'slug', 'description', 'category', 'thumbnail',
                'play_count', 'view_count', 'like_count', 'average_rating',
                'published_at',
            ]);

        return response()->json([
            'success' => true,
            'data' => $simulations,
        ]);
    }
}
