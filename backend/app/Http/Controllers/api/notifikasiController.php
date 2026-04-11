<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\notifikasi_user;

class notifikasiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = notifikasi_user::where('user_id', $user->id)
            ->latest()
            ->limit(30)
            ->get();

        $unread = notifikasi_user::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        return response()->json([
            'items' => $data,
            'unread' => $unread,
        ]);
    }

    public function dibaca($id)
    {
        $user = auth()->user();

        $notif = notifikasi_user::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notif->update(['dibaca' => true]);

        return response()->json(['success' => true]);
    }

    public function dibacaSemua()
    {
        $user = auth()->user();

        notifikasi_user::where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json(['success' => true]);
    }
}
