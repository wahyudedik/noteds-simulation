<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount('simulations');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['simulations' => function ($q) {
            $q->latest()->take(10);
        }, 'badges']);

        $stats = [
            'simulations' => $user->simulations()->count(),
            'published' => $user->simulations()->where('is_published', true)->count(),
            'total_views' => $user->simulations()->sum('view_count'),
            'total_plays' => $user->simulations()->sum('play_count'),
            'comments' => $user->comments()->count(),
            'followers' => $user->followers()->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Update user role (AJAX-friendly).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:user,creator,admin,superadmin',
        ]);

        $currentUser = auth()->user();

        // Prevent non-superadmin from assigning superadmin role
        if ($validated['role'] === 'superadmin' && ! $currentUser->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Hanya superadmin yang dapat menetapkan role superadmin.');
        }

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "Role pengguna berhasil diubah menjadi {$validated['role']}.");
    }

    /**
     * Approve user as creator.
     */
    public function approveCreator(User $user): RedirectResponse
    {
        if (in_array($user->role, ['superadmin', 'admin', 'creator'])) {
            return redirect()->back()->with('error', 'Pengguna ini sudah memiliki role creator atau lebih tinggi.');
        }

        $user->update(['role' => 'creator']);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "Pengguna {$user->name} berhasil disetujui sebagai creator.");
    }

    /**
     * Deactivate user account.
     */
    public function deactivate(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['email_verified_at' => null]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "Akun pengguna {$user->name} berhasil dinonaktifkan.");
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
