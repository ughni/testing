<?php

namespace App\Http\Controllers;

use App\Models\NotificationSystem;
use Illuminate\Http\Request;

class NotificationSystemController extends Controller
{
    public function index()
    {
       
       $notifications = NotificationSystem::orderBy('created_at', 'desc')->paginate(10);
        
        // Ngitung ada berapa notif yang belum dibaca (is_read = 0)
        $unreadCount = NotificationSystem::where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAllAsRead()
    {
        // Ubah semua status notif jadi 'sudah dibaca' (is_read = 1)
        NotificationSystem::where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}