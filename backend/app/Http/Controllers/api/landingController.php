<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\classroom;
use App\Models\data_siswa;
use App\Models\katalog;
use App\Models\SiteSetting;
use App\Models\silde;
use App\Models\User;
use Illuminate\Http\Request;

class landingController extends Controller
{
    // Public: Get statistics for landing page
    public function getStats()
    {
        return response()->json([
            'guru' => User::where('role_id', 3)->count(),
            'siswa' => data_siswa::count(),
            'katalog' => katalog::count(),
            'classroom' => classroom::count(),
        ]);
    }

    // Public: Get slides for landing page
    public function getSlides()
    {
        $data = silde::where('status', 'on')->latest()->get();
        return response()->json($data);
    }

    // Public: Get site settings (maps, etc)
    public function getSiteSettings()
    {
        return response()->json([
            'maps_embed_url' => SiteSetting::getValue('maps_embed_url', ''),
            'school_name' => SiteSetting::getValue('school_name', 'E-IPA Lab'),
            'school_address' => SiteSetting::getValue('school_address', ''),
            'school_address_detail' => SiteSetting::getValue('school_address_detail', ''),
            'school_email' => SiteSetting::getValue('school_email', ''),
            'school_phone' => SiteSetting::getValue('school_phone', ''),
        ]);
    }

    // Admin: Update site settings
    public function updateSiteSettings(Request $request)
    {
        $keys = [
            'maps_embed_url', 
            'school_name', 
            'school_address',
            'school_address_detail',
            'school_email',
            'school_phone'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::setValue($key, $request->input($key));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan'
        ]);
    }
}
