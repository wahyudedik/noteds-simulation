<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private GamificationService $gamification,
    ) {}

    /**
     * Show the public landing page for the Creator Program.
     */
    public function becomeCreatorPage(): View
    {
        return view('creators.become-creator');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $isCreator = $user->isCreator();

        $stats = [
            'bookmarks' => $user->bookmarks()->count(),
            'following' => $user->following()->count(),
            'followers' => $user->followers()->count(),
            'simulations_played' => $user->playHistory()->count(),
            'comments' => $user->comments()->count(),
            'unread_notifications' => $user->unreadNotificationsCount(),
        ];

        $recent_bookmarks = $user->bookmarks()
            ->with('simulation')
            ->latest()
            ->take(5)
            ->get()
            ->pluck('simulation');

        $recent_history = $user->playHistory()
            ->with('simulation')
            ->latest()
            ->take(5)
            ->get()
            ->pluck('simulation');

        $levelProgress = $this->gamification->getLevelProgress($user);
        $levelProgress['streak'] = $user->streak_count ?? 0;

        return view('dashboard', [
            'isCreator' => $isCreator,
            'stats' => $stats,
            'recent_bookmarks' => $recent_bookmarks,
            'recent_history' => $recent_history,
            'levelProgress' => $levelProgress,
        ]);
    }

    /**
     * Handle the user's request to become a creator.
     */
    public function becomeCreator(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isCreator()) {
            return redirect()->route('dashboard')->with('status', 'Kamu sudah menjadi kreator!');
        }

        // Update user role to creator
        $user->update(['role' => 'creator']);

        // Notify all admins/superadmins about the new creator
        $admins = User::where('role', 'superadmin')
            ->orWhere('role', 'admin')
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'creator_request',
                'title' => 'Kreator Baru',
                'body' => "{$user->name} telah bergabung sebagai kreator baru.",
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ],
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Selamat! Kamu sekarang adalah kreator. Mulai buat simulasi interaktifmu!');
    }
}
