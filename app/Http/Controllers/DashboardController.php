<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

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

        return view('dashboard', [
            'stats' => $stats,
            'recent_bookmarks' => $recent_bookmarks,
            'recent_history' => $recent_history,
        ]);
    }
}
