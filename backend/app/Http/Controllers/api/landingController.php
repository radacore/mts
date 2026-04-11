<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\classroom;
use App\Models\data_siswa;
use App\Models\inventaris;
use App\Models\katalog;
use App\Models\pinjam_alat;
use App\Models\pinjam_lab;
use App\Models\pinjam_lain;
use App\Models\SiteSetting;
use App\Models\silde;
use App\Models\User;
use Illuminate\Http\Request;

class landingController extends Controller
{
    // Public: Get statistics for landing page
    public function getStats()
    {
        $now = now();
        $year = (int) $now->year;

        $labPerBulan = pinjam_lab::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $alatPerBulan = pinjam_alat::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $lainPerBulan = pinjam_lain::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $peminjamanPerBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $peminjamanPerBulan[] = [
                'bulan' => $m,
                'total' => (int) ($labPerBulan[$m] ?? 0) + (int) ($alatPerBulan[$m] ?? 0) + (int) ($lainPerBulan[$m] ?? 0),
            ];
        }

        $jumlahJenisAlat = (int) inventaris::selectRaw('COUNT(DISTINCT nabar) as total')
            ->value('total');

        $totalUnitAlat = (int) inventaris::sum('jml');

        return response()->json([
            'guru' => User::where('role_id', 3)->count(),
            'siswa' => data_siswa::count(),
            'katalog' => katalog::count(),
            'classroom' => classroom::count(),
            'tahun_aktif' => $year,
            'peminjaman_per_bulan' => $peminjamanPerBulan,
            'jumlah_jenis_alat' => $jumlahJenisAlat,
            'total_unit_alat' => $totalUnitAlat,
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
