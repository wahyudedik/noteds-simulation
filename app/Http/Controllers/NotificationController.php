<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request): View|JsonResponse
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($notifications);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): RedirectResponse|JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
        }

        // Redirect to the deep link if available
        if ($notification->data && isset($notification->data['simulation_slug'])) {
            return redirect()->route('simulations.show', $notification->data['simulation_slug'])->with('success', 'Notifikasi ditandai sudah dibaca.');
        }

        return redirect()->route('notifications.index')->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse|JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
