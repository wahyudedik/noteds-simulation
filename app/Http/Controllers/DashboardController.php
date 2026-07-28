<?php

namespace App\Http\Controllers;

use App\Models\CreatorApplication;
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
        $pendingApplication = null;
        $rejectedApplication = null;

        if (! $isCreator) {
            $pendingApplication = CreatorApplication::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            $rejectedApplication = CreatorApplication::where('user_id', $user->id)
                ->where('status', 'rejected')
                ->latest()
                ->first();
        }

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
            'pendingApplication' => $pendingApplication,
            'rejectedApplication' => $rejectedApplication,
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

        // Check if user already has a pending application
        $existingPending = CreatorApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return redirect()->route('dashboard')->with('error', 'Kamu sudah memiliki pengajuan yang sedang diproses.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:20|max:1000',
        ]);

        // Create application
        CreatorApplication::create([
            'user_id' => $user->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Notify all admins/superadmins about the new application
        $admins = User::where('role', 'superadmin')
            ->orWhere('role', 'admin')
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'creator_request',
                'title' => 'Pengajuan Kreator Baru',
                'body' => "{$user->name} telah mengajukan diri sebagai kreator. Menunggu persetujuan admin.",
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                ],
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Pengajuan kreator berhasil dikirim! Admin akan meninjau aplikasimu dalam 1-3 hari kerja.');
    }

    /**
     * Cancel a pending creator application.
     */
    public function cancelApplication(Request $request): RedirectResponse
    {
        $user = $request->user();

        $application = CreatorApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $application) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada pengajuan yang bisa dibatalkan.');
        }

        $application->update(['status' => 'rejected', 'review_notes' => 'Dibatalkan oleh pengguna']);

        return redirect()->route('dashboard')->with('status', 'Pengajuan kreator berhasil dibatalkan.');
    }
}
