<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\data_katalog;
use App\Models\jumlah_pinjam;
use App\Models\jumlah_pinjam_alat;
use App\Models\classroom;
use App\Models\informasi_terkini;
use App\Models\inventaris;
use App\Models\inventaris_mutation;
use App\Models\notifikasi_user;
use App\Models\pinjam_alat;
use App\Models\pinjam_lab;
use App\Models\pinjam_lain;
use App\Services\InventoryStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class peminjamanController extends Controller
{
    private function normalizeAlasanPenolakan(?string $alasan): ?string
    {
        if ($alasan === null) {
            return null;
        }

        $trimmed = trim($alasan);

        return $trimmed === '' ? null : $trimmed;
    }

    private function validateAlasanPenolakan(string $status, ?string $alasan)
    {
        if ($status !== 'ditolak') {
            return null;
        }

        if ($alasan === null) {
            return response()->json([
                'message' => 'Alasan penolakan wajib diisi saat status ditolak.',
            ], 422);
        }

        if (mb_strlen($alasan) > 2000) {
            return response()->json([
                'message' => 'Alasan penolakan maksimal 2000 karakter.',
            ], 422);
        }

        return null;
    }

    private function validateTransisiStatusPeminjaman(string $jenis, ?string $statusSaatIni, string $statusBaru)
    {
        $statusSaatIni = $statusSaatIni ?: 'diajukan';

        $transisi = [
            'lab' => [
                'diajukan' => ['disetujui', 'ditolak'],
            ],
            'alat' => [
                'diajukan' => ['disetujui', 'ditolak'],
                'disetujui' => ['dikembalikan'],
            ],
            'lain' => [
                'diajukan' => ['disetujui', 'ditolak'],
            ],
        ];

        if (in_array($statusBaru, $transisi[$jenis][$statusSaatIni] ?? [], true)) {
            return null;
        }

        return response()->json([
            'message' => 'Status peminjaman sudah final atau transisi status tidak valid.',
        ], 422);
    }

    private function guruCanUseKelas($kelasId): bool
    {
        $user = Auth()->User();

        if (!$user || (int) $user->role_id !== 3) {
            return true;
        }

        return classroom::where('user_id', $user->id)
            ->where('kelas_id', $kelasId)
            ->exists();
    }

    private function rejectUnauthorizedGuruKelas()
    {
        return response()->json([
            'message' => 'Kelas tidak tersedia di Ruang Praktikum Anda.',
        ], 422);
    }

    /**
     * Hitung stok efektif untuk satu inventaris pada rentang waktu peminjaman alat.
     *
     * Stok efektif = inventaris.jml - SUM(diberi) dari peminjaman aset
     * yang berstatus 'disetujui' (belum 'dikembalikan'), pada katalog yang
     * memuat inventaris ini, dan rentang waktunya overlap dengan rentang baru.
     *
     * Untuk barang habis_pakai, reservasi tidak dihitung — stok mereka dikurangi
     * permanen lewat inventaris_mutations saat disetujui (lihat Tahap 3).
     *
     * @param  int    $inventarisId
     * @param  string $tglPakai     YYYY-MM-DD
     * @param  string $jamPakai     HH:mm[:ss]
     * @param  string $tglKembali   YYYY-MM-DD
     * @param  string $jamKembali   HH:mm[:ss]
     * @param  int|null $ignorePinjamAlatId  id pinjam_alat yang sedang diedit
     * @return int    stok yang masih bisa dipinjam pada rentang itu (min 0)
     */
    private function stokEfektifInventaris(
        int $inventarisId,
        string $tglPakai,
        string $jamPakai,
        string $tglKembali,
        string $jamKembali,
        $ignorePinjamAlatId = null
    ): int {
        $inv = inventaris::find($inventarisId);
        if (!$inv) {
            return 0;
        }
        $stokTotal = (int) $inv->jml;

        // Untuk barang habis_pakai, stok tidak direservasi; jadi pakai stok total.
        if (($inv->jenis_barang ?? 'aset') === 'habis_pakai') {
            return max($stokTotal, 0);
        }

        $mulaiBaru = $tglPakai . ' ' . $jamPakai;
        $selesaiBaru = $tglKembali . ' ' . $jamKembali;

        // Cari semua data_katalog yang merujuk inventaris ini
        $dataKatalogIds = DB::table('data_katalogs')
            ->where('inventaris_id', $inventarisId)
            ->pluck('id');

        if ($dataKatalogIds->isEmpty()) {
            return max($stokTotal, 0);
        }

        $query = DB::table('jumlah_pinjam_alats as jpa')
            ->join('pinjam_alats as pa', 'jpa.pinjam_alat_id', '=', 'pa.id')
            ->whereIn('jpa.data_katalog_id', $dataKatalogIds)
            ->where('pa.status', 'disetujui')
            ->whereRaw("CONCAT(pa.tgl_pakai, ' ', pa.jam_pakai) < ?", [$selesaiBaru])
            ->whereRaw("CONCAT(pa.tgl_kembali, ' ', pa.jam_kembali) > ?", [$mulaiBaru]);

        if (!empty($ignorePinjamAlatId)) {
            $query->where('pa.id', '!=', $ignorePinjamAlatId);
        }

        $terpakai = (int) $query->sum('jpa.diberi');

        return max($stokTotal - $terpakai, 0);
    }

    /**
     * Validasi bentrok jadwal pada tabel peminjaman tertentu.
     *
     * Rule overlap: start_baru < selesai_lama DAN selesai_baru > start_lama.
     * Hanya cek status aktif: 'diajukan' & 'disetujui'. Status 'ditolak'
     * tidak dihitung agar slot kembali bebas.
     *
     * @param  string $model       FQCN model (mis. pinjam_lab::class)
     * @param  string $kolomTgl    nama kolom tanggal (mis. 'tgl')
     * @param  string $kolomMulai  nama kolom jam mulai (mis. 'jam' atau 'mulai')
     * @param  string $kolomSelesai nama kolom jam selesai
     * @param  string $tgl         nilai tanggal request
     * @param  string $jamMulai    nilai jam mulai request
     * @param  string $jamSelesai  nilai jam selesai request
     * @param  int|null $ignoreId  id record yang sedang diedit (skip dirinya sendiri)
     * @return \Illuminate\Http\JsonResponse|null  null bila aman; JsonResponse 409 bila bentrok
     */
    private function cekBentrokJadwal(
        string $model,
        string $kolomTgl,
        string $kolomMulai,
        string $kolomSelesai,
        string $tgl,
        string $jamMulai,
        string $jamSelesai,
        $ignoreId = null
    ) {
        if ($jamMulai >= $jamSelesai) {
            return response()->json([
                'message' => 'Jam selesai harus lebih besar dari jam mulai.',
            ], 422);
        }

        $query = $model::where($kolomTgl, $tgl)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->where($kolomMulai, '<', $jamSelesai)
            ->where($kolomSelesai, '>', $jamMulai);

        if (!empty($ignoreId)) {
            $query->where('id', '!=', $ignoreId);
        }

        $bentrok = $query->first();

        if ($bentrok) {
            $mulai = substr((string) $bentrok->{$kolomMulai}, 0, 5);
            $selesai = substr((string) $bentrok->{$kolomSelesai}, 0, 5);
            return response()->json([
                'message' => "Jadwal bentrok dengan peminjaman lain pada tanggal {$tgl} jam {$mulai}-{$selesai} (status: {$bentrok->status}).",
                'bentrok' => [
                    'id' => $bentrok->id,
                    'tgl' => $tgl,
                    'mulai' => $mulai,
                    'selesai' => $selesai,
                    'status' => $bentrok->status,
                ],
            ], 409);
        }

        return null;
    }

    /**
     * Saat peminjaman alat disetujui:
     * - Untuk barang habis_pakai → catat mutasi 'keluar' (stok berkurang permanen).
     * - Untuk aset → tidak ada mutasi (reservasi virtual via stok efektif).
     *
     * Idempotent: melewati item yang sudah pernah dicatat (cek keterangan).
     */
    private function prosesMutasiAlatDisetujui(pinjam_alat $proses): void
    {
        $items = jumlah_pinjam_alat::with('data_katalog.inventaris')
            ->where('pinjam_alat_id', $proses->id)
            ->where('diberi', '>', 0)
            ->get();

        foreach ($items as $jpa) {
            $inv = optional($jpa->data_katalog)->inventaris;
            if (!$inv) {
                continue;
            }
            if (($inv->jenis_barang ?? 'aset') !== 'habis_pakai') {
                continue;
            }

            $stockService = app(InventoryStockService::class);
            $stockService->ensureConsumableInitialStock($inv, (int) date('Y'), auth()->id());

            $keterangan = "Peminjaman alat #{$proses->id} disetujui (consumable)";
            $sudahDicatat = inventaris_mutation::where('inventaris_id', $inv->id)
                ->where('jenis', 'keluar')
                ->where('keterangan', $keterangan)
                ->exists();

            if (!$sudahDicatat) {
                inventaris_mutation::create([
                    'inventaris_id' => $inv->id,
                    'tahun' => (int) date('Y'),
                    'qty' => (int) $jpa->diberi,
                    'jenis' => 'keluar',
                    'keterangan' => $keterangan,
                    'created_by' => auth()->id(),
                ]);
            }

            $stockService->sync($inv->fresh());
        }
    }

    /**
     * Saat peminjaman alat dikembalikan:
     * - Terima opsional payload 'pengembalian' (array) berisi kondisi per item:
     *   [{ jpa_id, rusak }]. Sisa (utuh) dihitung otomatis.
     * - Untuk aset: rusak memindahkan kondisi baik → rusak tanpa mengurangi total stok.
     *   Yang utuh → reservasi otomatis dilepas (cuma status berubah, tidak ada mutasi).
     * - Untuk consumable: pengembalian tidak relevan (sudah jadi mutasi keluar saat disetujui).
     *
     * Jika payload tidak dikirim, default semua utuh.
     */
    private function prosesMutasiAlatDikembalikan(pinjam_alat $proses, array $payload = []): void
    {
        $byJpaId = [];
        foreach ($payload as $row) {
            if (!empty($row['jpa_id'])) {
                $byJpaId[(int) $row['jpa_id']] = $row;
            }
        }

        $items = jumlah_pinjam_alat::with('data_katalog.inventaris')
            ->where('pinjam_alat_id', $proses->id)
            ->where(function ($query) {
                $query->where('diberi', '>', 0)
                    ->orWhere(function ($q) {
                        $q->where(function ($qq) {
                                $qq->whereNull('diberi')->orWhere('diberi', 0);
                            })
                            ->where('minta', '>', 0);
                    });
            })
            ->get();

        foreach ($items as $jpa) {
            $inv = optional($jpa->data_katalog)->inventaris;
            if (!$inv) {
                continue;
            }
            $isHabisPakai = (($inv->jenis_barang ?? 'aset') === 'habis_pakai');

            $diberi = $this->jumlahDiberikanAlat($jpa);
            $info = $byJpaId[$jpa->id] ?? [];
            $rusak = max(0, (int) ($info['rusak'] ?? 0));
            $rusak = min($rusak, $diberi);

            // Simpan kondisi pengembalian ke jumlah_pinjam_alats (audit per-peminjaman).
            // Untuk habis_pakai: rusak dipaksa 0 (sudah keluar permanen saat disetujui).
            // Field hilang tidak digunakan lagi dan selalu di-reset 0 untuk pengembalian baru.
            $jpa->update([
                'rusak' => $isHabisPakai ? 0 : $rusak,
                'hilang' => 0,
            ]);

            // Consumable tidak buat mutasi tambahan (sudah keluar saat disetujui)
            if ($isHabisPakai) {
                continue;
            }

            if ($rusak > 0) {
                $keteranganRusak = "Peminjaman alat #{$proses->id} dikembalikan - rusak: {$rusak}";
                $sudahDicatatRusak = inventaris_mutation::where('inventaris_id', $inv->id)
                    ->where('jenis', 'rusak')
                    ->where('keterangan', $keteranganRusak)
                    ->exists();

                if (!$sudahDicatatRusak) {
                    inventaris_mutation::create([
                        'inventaris_id' => $inv->id,
                        'tahun' => (int) date('Y'),
                        'qty' => $rusak,
                        'jenis' => 'rusak',
                        'kondisi_asal' => 'baik',
                        'keterangan' => $keteranganRusak,
                        'created_by' => auth()->id(),
                    ]);

                    $inv->konbaik = max((int) $inv->konbaik - $rusak, 0);
                    $inv->konrusak = (int) $inv->konrusak + $rusak;
                    $inv->save();
                }
            }
        }
    }

    /**
     * Tambahkan field ringkasan kondisi pengembalian agar bisa ditampilkan
     * di list view (chip status):
     * - total_diberi, total_rusak, total_hilang
     * - has_kerusakan (bool)
     */
    private function appendRingkasanKondisi($pinjamAlat): void
    {
        $items = $pinjamAlat->jumlahPinjamAlats ?? collect();
        $totalDiberi = 0; $totalRusak = 0; $totalHilang = 0;
        foreach ($items as $jp) {
            $totalDiberi += $this->jumlahDiberikanAlat($jp);
            $totalRusak += (int) ($jp->rusak ?? 0);
            $totalHilang += (int) ($jp->hilang ?? 0);
        }
        $pinjamAlat->total_diberi = $totalDiberi;
        $pinjamAlat->total_rusak = $totalRusak;
        $pinjamAlat->total_hilang = $totalHilang;
        $pinjamAlat->has_kerusakan = ($totalRusak + $totalHilang) > 0;
    }

    /**
     * Pastikan pengembalian lama tetap menampilkan item walaupun kolom
     * `diberi` belum pernah diisi. Data lama memakai `minta` sebagai dasar.
     */
    private function jumlahDiberikanAlat($row): int
    {
        $diberi = (int) ($row->diberi ?? 0);
        if ($diberi > 0) {
            return $diberi;
        }

        return (int) ($row->minta ?? 0);
    }

    public function index()
    {
        $user = Auth()->User();
        // Laboran (2) & Admin (1) bisa lihat semua
        if ($user->role_id == 2 || $user->role_id == 1) {
            $data = pinjam_lab::with(['kelas','katalog','User.bioguru','modulLkpd.uploader'])->latest()->get();
        } else {
            // Guru/Siswa hanya lihat punya sendiri
            $data = pinjam_lab::with(['kelas','katalog','User.bioguru','modulLkpd.uploader'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return response()->json($data);
    }
    public function pinjamLabPost(Request $request)
    {
        $request->validate([
            'kelas_id'=>'required',
            'topik_id'=>'required',
            'tgl'=>'required',
            'jam'=>'required',
            'jam_selesai'=>'required',
            'pekan'=>'required',
            'modul_lkpd_ids'=>'nullable|array',
            'modul_lkpd_ids.*'=>'exists:modul_lkpd,id',
        ]);

        if (!$this->guruCanUseKelas($request->kelas_id)) {
            return $this->rejectUnauthorizedGuruKelas();
        }

        $isClosed = informasi_terkini::where('status', 'aktif')
            ->where('tipe', 'penutupan_lab')
            ->where(function ($q) {
                $q->whereNull('mulai_at')->orWhere('mulai_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('selesai_at')->orWhere('selesai_at', '>=', now());
            })
            ->exists();

        if ($isClosed) {
            return response()->json([
                'message' => 'Peminjaman lab ditutup sementara karena ada informasi kegiatan aktif.',
            ], 422);
        }

        if ($error = $this->cekBentrokJadwal(
            pinjam_lab::class,
            'tgl',
            'jam',
            'jam_selesai',
            $request->tgl,
            $request->jam,
            $request->jam_selesai,
            $request->id
        )) {
            return $error;
        }

        $data=pinjam_lab::updateOrCreate(['id'=>$request->id],[
            'katalog_id'=>$request->topik_id,
            'kelas_id'=>$request->kelas_id,
            'user_id'=>Auth()->User()->id,
            'tgl'=>$request->tgl,
            'jam'=>$request->jam,
            'jam_selesai'=>$request->jam_selesai,
            'pekan'=>$request->pekan,
            'peminjam'=>Auth()->User()->name,
            'status'=>'diajukan',
            'alasan_penolakan' => null,
        ]);
        $data->modulLkpd()->sync($request->modul_lkpd_ids ?? []);
        return response()->json($data->load('modulLkpd.uploader'));
    }
    public function pinjamLabEdit($id)
    {
        $data=pinjam_lab::with('modulLkpd.uploader')->where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamLabHapus($id)
    {
        if($id){
            $hapus=pinjam_lab::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function peminjamanLab()
    {
        $data=pinjam_lab::with(['kelas','katalog','user','modulLkpd.uploader'])->with(['user.bioguru'])->whereIn('status',['diajukan','disetujui','ditolak'])->latest()->get();
        return response()->json($data);
    }
    public function peminjamanAlat()
    {
        $data=pinjam_alat::with(['kelas','katalog','user','modulLkpd.uploader','jumlahPinjamAlats'])->with(['user.bioguru'])->whereIn('status',['diajukan','disetujui','ditolak','dikembalikan'])->latest()->get();
        $data->each(function ($row) { $this->appendRingkasanKondisi($row); });
        return response()->json($data);
    }
    public function peminjamanLabProses($id,$data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman lab.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        $alasanPenolakan = $this->normalizeAlasanPenolakan(request('alasan_penolakan'));
        if ($error = $this->validateAlasanPenolakan($data, $alasanPenolakan)) {
            return $error;
        }

        if($id){
            $proses=pinjam_lab::find($id);
            if (!$proses) {
                return response()->json([
                    'message' => 'Data peminjaman lab tidak ditemukan.',
                ], 404);
            }

            if ($error = $this->validateTransisiStatusPeminjaman('lab', $proses->status, $data)) {
                return $error;
            }

            $proses->update([
                'status'=> $data,
                'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
            ]);

            $pesan = 'Pengajuan peminjaman lab Anda telah ' . $data . '.';
            if ($data === 'ditolak' && $alasanPenolakan) {
                $pesan .= ' Alasan: ' . $alasanPenolakan;
            }

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Lab',
                    'pesan' => $pesan,
                    'tipe' => 'pinjam_lab_status',
                    'tautan' => '/pinjam-lab',
                    'meta' => [
                        'pinjam_lab_id' => $proses->id,
                        'status' => $data,
                        'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function peminjamanAlatProses($id,$data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman alat.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak', 'dikembalikan'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        $alasanPenolakan = $this->normalizeAlasanPenolakan(request('alasan_penolakan'));
        if ($error = $this->validateAlasanPenolakan($data, $alasanPenolakan)) {
            return $error;
        }

        if($id){
            $proses=pinjam_alat::find($id);
            if (!$proses) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.',
                ], 404);
            }

            if ($error = $this->validateTransisiStatusPeminjaman('alat', $proses->status, $data)) {
                return $error;
            }

            DB::transaction(function () use ($proses, $data, $alasanPenolakan) {
                $proses->update([
                    'status'=> $data,
                    'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
                ]);

                // Trigger mutasi stok inventaris sesuai transisi status.
                // - disetujui: catat 'keluar' untuk barang habis_pakai
                // - dikembalikan: catat perubahan kondisi untuk aset yang rusak
                // Untuk aset yang utuh, reservasi virtual dilepas otomatis lewat status.
                if ($data === 'disetujui') {
                    $this->prosesMutasiAlatDisetujui($proses);
                } elseif ($data === 'dikembalikan') {
                    $payload = request('pengembalian');
                    $this->prosesMutasiAlatDikembalikan(
                        $proses,
                        is_array($payload) ? $payload : []
                    );
                }
            });

            $pesan = 'Pengajuan peminjaman alat Anda telah ' . $data . '.';
            if ($data === 'ditolak' && $alasanPenolakan) {
                $pesan .= ' Alasan: ' . $alasanPenolakan;
            }

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Alat',
                    'pesan' => $pesan,
                    'tipe' => 'pinjam_alat_status',
                    'tautan' => '/pinjam-alat',
                    'meta' => [
                        'pinjam_alat_id' => $proses->id,
                        'status' => $data,
                        'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function filterTopik($id,$plid)
    {
        $data2=data_katalog::with(['katalog','inventaris','jumlah_pinjam'])->where('katalog_id',$id)
        ->get();
        $pus=[];
        $cek=DB::table('jumlah_pinjams as jml')->leftJoin('data_katalogs as kat','jml.data_katalog_id','=','kat.id')
                ->select('jml.*','kat.katalog_id')
                ->where('kat.katalog_id', $id)
                ->where('jml.pinjam_lab_id', $plid)
                ->first();
        if(empty($cek->id)){
            foreach($data2 as $row){
                $pus[] = [
                    'data_katalog_id' => $row->id,
                    'pinjam_lab_id' => $plid,
                    'minta' => 0,
                    'diberi' => 0,
                ];
            }
            jumlah_pinjam::insert($pus);
        }
        $data=DB::table('data_katalogs as dakat')
                ->leftJoin('inventaris as inv','dakat.inventaris_id','=','inv.id')
                ->leftJoin('jumlah_pinjams as jp','dakat.id','=','jp.data_katalog_id')
                ->select('dakat.*','inv.nabar','inv.jml','inv.noreg','jp.minta','jp.diberi','jp.id as jpid')
                ->where('dakat.katalog_id',$id)
                ->where('jp.pinjam_lab_id',$plid)
                ->get();
        return response()->json($data);
    }
    public function filterTopikAlat($id,$paid)
    {
        $data2=data_katalog::with(['katalog','inventaris','jumlah_pinjam_alat'])->where('katalog_id',$id)->get();
        $pus=[];
        $cek=DB::table('jumlah_pinjam_alats as jml')->leftJoin('data_katalogs as kat','jml.data_katalog_id','=','kat.id')
                ->select('jml.*','kat.katalog_id')
                ->where('kat.katalog_id', $id)
                ->where('jml.pinjam_alat_id', $paid)
                ->first();
        if(empty($cek->id)){
            foreach($data2 as $row){
                $pus[] = [
                    'data_katalog_id' => $row->id,
                    'pinjam_alat_id' => $paid,
                    'minta' => 0,
                    'diberi' => 0,
                ];
            }
            jumlah_pinjam_alat::insert($pus);
        }
        $data=DB::table('data_katalogs as dakat')
        ->leftJoin('inventaris as inv','dakat.inventaris_id','=','inv.id')
        ->leftJoin('jumlah_pinjam_alats as jp','dakat.id','=','jp.data_katalog_id')
        ->select(
            'dakat.*',
            'inv.id as inventaris_id',
            'inv.nabar',
            'inv.jml as jml_total',
            'inv.jenis_barang',
            'inv.noreg',
            'jp.minta',
            DB::raw('COALESCE(jp.diberi, 0) as diberi'),
            'jp.diberi as diberi_asli',
            'jp.rusak',
            'jp.hilang',
            'jp.id as jpid'
        )
        ->where('dakat.katalog_id',$id)
        ->where('jp.pinjam_alat_id',$paid)
        ->get();

        // Hitung stok efektif berdasarkan rentang waktu pengajuan ini
        $pinjamAlat = pinjam_alat::find($paid);
        if ($pinjamAlat && $pinjamAlat->tgl_pakai && $pinjamAlat->jam_pakai
            && $pinjamAlat->tgl_kembali && $pinjamAlat->jam_kembali) {
            foreach ($data as $row) {
                if (empty($row->inventaris_id)) {
                    $row->jml = (int) ($row->jml_total ?? 0);
                    continue;
                }
                $row->jml = $this->stokEfektifInventaris(
                    (int) $row->inventaris_id,
                    $pinjamAlat->tgl_pakai,
                    $pinjamAlat->jam_pakai,
                    $pinjamAlat->tgl_kembali,
                    $pinjamAlat->jam_kembali,
                    $paid
                );
            }
        } else {
            // Fallback bila rentang waktu belum lengkap: pakai stok total
            foreach ($data as $row) {
                $row->jml = (int) ($row->jml_total ?? 0);
            }
        }

        return response()->json($data);
    }
    public function jumlahPinjamPost(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam::find($request->id);
            $update->update([
                'minta'=>$request->minta
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamAlatPost(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam_alat::find($request->id);
            if (!$update) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $pinjamAlat = pinjam_alat::find($update->pinjam_alat_id);
            if (!$pinjamAlat) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $user = auth()->user();
            if ((int) $user->role_id !== 3) {
                return response()->json([
                    'message' => 'Hanya guru yang dapat mengubah jumlah diajukan.'
                ], 403);
            }

            if ($pinjamAlat->status !== 'diajukan') {
                return response()->json([
                    'message' => 'Pengajuan sudah diproses, jumlah diajukan tidak dapat diubah.'
                ], 422);
            }

            $minta = (int) $request->minta;
            if ($minta < 0) {
                return response()->json([
                    'message' => 'Jumlah diajukan tidak boleh kurang dari 0.'
                ], 422);
            }

            // Stok efektif: dihitung pada rentang waktu pengajuan ini
            // (memperhitungkan reservasi peminjaman lain yang masih 'disetujui').
            $inventarisId = (int) DB::table('data_katalogs')
                ->where('id', $update->data_katalog_id)
                ->value('inventaris_id');

            $stokTersedia = $inventarisId
                ? $this->stokEfektifInventaris(
                    $inventarisId,
                    $pinjamAlat->tgl_pakai,
                    $pinjamAlat->jam_pakai,
                    $pinjamAlat->tgl_kembali,
                    $pinjamAlat->jam_kembali,
                    $pinjamAlat->id
                )
                : 0;

            if ($minta > $stokTersedia) {
                return response()->json([
                    'message' => "Jumlah diajukan ({$minta}) melebihi stok yang tersedia pada rentang waktu tersebut ({$stokTersedia})."
                ], 422);
            }

            $update->update([
                'minta'=>$minta
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamPost2(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam::find($request->id);
            $update->update([
                'diberi'=>$request->diberi
            ]);
        }
        return response()->json($update);
    }
    public function jumlahPinjamAlatPost2(Request $request)
    {
        if($request->id){
            $update=jumlah_pinjam_alat::find($request->id);
            if (!$update) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $pinjamAlat = pinjam_alat::find($update->pinjam_alat_id);
            if (!$pinjamAlat) {
                return response()->json([
                    'message' => 'Data peminjaman alat tidak ditemukan.'
                ], 404);
            }

            $user = auth()->user();
            if (!in_array((int) $user->role_id, [1, 2], true)) {
                return response()->json([
                    'message' => 'Hanya admin atau laboran yang dapat mengubah jumlah diberikan.'
                ], 403);
            }

            if ($pinjamAlat->status !== 'diajukan') {
                return response()->json([
                    'message' => 'Pengajuan sudah diproses, jumlah diberikan tidak dapat diubah.'
                ], 422);
            }

            $diberi = (int) $request->diberi;
            $minta = (int) $update->minta;

            if ($diberi < 0) {
                return response()->json([
                    'message' => 'Jumlah diberikan tidak boleh kurang dari 0.'
                ], 422);
            }

            if ($diberi > $minta) {
                return response()->json([
                    'message' => 'Jumlah diberikan tidak boleh melebihi jumlah diajukan.'
                ], 422);
            }

            // Validasi stok efektif agar laboran tidak memberi lebih dari yang tersedia
            // pada rentang waktu pengajuan ini.
            if ($pinjamAlat) {
                $inventarisId = (int) DB::table('data_katalogs')
                    ->where('id', $update->data_katalog_id)
                    ->value('inventaris_id');
                if ($inventarisId) {
                    $stokTersedia = $this->stokEfektifInventaris(
                        $inventarisId,
                        $pinjamAlat->tgl_pakai,
                        $pinjamAlat->jam_pakai,
                        $pinjamAlat->tgl_kembali,
                        $pinjamAlat->jam_kembali,
                        $pinjamAlat->id
                    );
                    if ($diberi > $stokTersedia) {
                        return response()->json([
                            'message' => "Jumlah diberikan ({$diberi}) melebihi stok yang tersedia pada rentang waktu tersebut ({$stokTersedia})."
                        ], 422);
                    }
                }
            }

            $update->update([
                'diberi'=>$diberi
            ]);
        }
        return response()->json($update);
    }
    public function lkpds(Request $request)
    {
        $request->validate([
            'lkpd'=>'required|mimes:,pdf,doc,docx'
        ]);
        if($request->id){
            $upload=pinjam_lab::find($request->id);
            $lkpd=$request->file('lkpd')->store('lkpd','public');
            $upload->update([
                'lkpd'=>$lkpd
            ]);
        }
        return response()->json($upload);
    }
    public function lkpdalat(Request $request)
    {
        $request->validate([
            'lkpd'=>'required|mimes:,pdf,doc,docx'
        ]);
        if($request->id){
            $upload=pinjam_alat::find($request->id);
            $lkpd=$request->file('lkpd')->store('lkpd','public');
            $upload->update([
                'lkpd'=>$lkpd
            ]);
        }
        return response()->json($upload);
    }
    public function pinjamAlat()
    {
        $user = Auth()->User();
        if ($user->role_id == 2 || $user->role_id == 1) {
             $data = pinjam_alat::with(['katalog','kelas','user.bioguru','modulLkpd.uploader','jumlahPinjamAlats'])->latest()->get();
        } else {
             $data = pinjam_alat::with(['katalog','kelas','user.bioguru','modulLkpd.uploader','jumlahPinjamAlats'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        $data->each(function ($row) { $this->appendRingkasanKondisi($row); });
        return response()->json($data);
    }
    public function pinjamAlatPost(Request $request)
    {
        $request->validate([
            'kelas_id'=>'required',
            'katalog_id'=>'required',
            'tgl_pakai'=>'required',
            'tgl_kembali'=>'required',
            'jam_kembali'=>'required',
            'jam_pakai'=>'required',
            'lokasi'=>'required',
            'keperluan'=>'required',
            'modul_lkpd_ids'=>'nullable|array',
            'modul_lkpd_ids.*'=>'exists:modul_lkpd,id',
        ]);

        if (!$this->guruCanUseKelas($request->kelas_id)) {
            return $this->rejectUnauthorizedGuruKelas();
        }

        // Validasi rentang tanggal & jam pakai-kembali logis.
        $mulai = $request->tgl_pakai . ' ' . $request->jam_pakai;
        $selesai = $request->tgl_kembali . ' ' . $request->jam_kembali;
        if (strtotime($selesai) <= strtotime($mulai)) {
            $sameDay = $request->tgl_pakai === $request->tgl_kembali;
            return response()->json([
                'message' => $sameDay
                    ? 'Jam kembali harus lebih besar dari jam pakai.'
                    : 'Tanggal/jam kembali harus setelah tanggal/jam pakai.',
            ], 422);
        }

        $data=pinjam_alat::updateOrCreate(['id'=>$request->id],[
            'kelas_id'=>$request->kelas_id,
            'katalog_id'=>$request->katalog_id,
            'tgl_pakai'=>$request->tgl_pakai,
            'user_id'=>Auth()->User()->id,
            'jam_pakai'=>$request->jam_pakai,
            'jam_kembali'=>$request->jam_kembali,
            'tgl_kembali'=>$request->tgl_kembali,
            'jam'=>$request->jam,
            'lokasi'=>$request->lokasi,
            'keperluan'=>$request->keperluan,
            'status'=>'diajukan',
            'alasan_penolakan' => null,
        ]);
        $data->modulLkpd()->sync($request->modul_lkpd_ids ?? []);
        return response()->json($data->load('modulLkpd.uploader'));
    }
    public function pinjamAlatEdit($id)
    {
        $data=pinjam_alat::with('modulLkpd.uploader')->where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamAlatHapus($id)
    {
        if($id){
            $hapus=pinjam_alat::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function pinjamLain()
    {
        $user = Auth()->User();
        if ($user->role_id == 2 || $user->role_id == 1) {
            $data = pinjam_lain::with('user')->latest()->get();
        } else {
            $data = pinjam_lain::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return response()->json($data);
    }
    public function pinjamLainPost(Request $request)
    {
        $request->validate([
            'tgl'=>'required',
            'mulai'=>'required',
            'selesai'=>'required',
            'kegiatan'=>'required',
        ]);

        if ($error = $this->cekBentrokJadwal(
            pinjam_lain::class,
            'tgl',
            'mulai',
            'selesai',
            $request->tgl,
            $request->mulai,
            $request->selesai,
            $request->id
        )) {
            return $error;
        }

        $data=pinjam_lain::updateOrCreate(['id'=>$request->id],[
            'tgl'=>$request->tgl,
            'mulai'=>$request->mulai,
            'selesai'=>$request->selesai,
            'kegiatan'=>$request->kegiatan,
            'status'=>'diajukan',
            'alasan_penolakan' => null,
            'user_id'=>Auth()->User()->id
        ]);
        return response()->json($data);
    }
    public function pinjamLainEdit($id)
    {
        $data=pinjam_lain::where('id', $id)->first();
        return response()->json($data);
    }
    public function pinjamLainHapus($id)
    {
        if($id){
            $hapus=pinjam_lain::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function peminjamanLain()
    {
        $data=pinjam_lain::with('user')->whereIn('status',['diajukan','disetujui','ditolak'])->latest()->get();
        return response()->json($data);
    }
    public function pinjamLainClient()
    {
        $data=pinjam_lain::where('status','disetujui')->latest()->get();
        return response()->json($data);
    }
    public function peminjamanLainProses($id, $data)
    {
        $user = auth()->user();
        if (!$user || !in_array((int) $user->role_id, [1, 2], true)) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah status peminjaman kegiatan lain.',
            ], 403);
        }

        if (!in_array($data, ['diajukan', 'disetujui', 'ditolak'], true)) {
            return response()->json([
                'message' => 'Status tidak valid.',
            ], 422);
        }

        $alasanPenolakan = $this->normalizeAlasanPenolakan(request('alasan_penolakan'));
        if ($error = $this->validateAlasanPenolakan($data, $alasanPenolakan)) {
            return $error;
        }

        if($id){
            $proses=pinjam_lain::find($id);
            if (!$proses) {
                return response()->json([
                    'message' => 'Data peminjaman kegiatan lain tidak ditemukan.',
                ], 404);
            }

            if ($error = $this->validateTransisiStatusPeminjaman('lain', $proses->status, $data)) {
                return $error;
            }

            $proses->update([
                'status'=> $data,
                'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
            ]);

            $pesan = 'Pengajuan kegiatan lain Anda telah ' . $data . '.';
            if ($data === 'ditolak' && $alasanPenolakan) {
                $pesan .= ' Alasan: ' . $alasanPenolakan;
            }

            if ($proses && $proses->user_id) {
                notifikasi_user::create([
                    'user_id' => $proses->user_id,
                    'judul' => 'Update Status Peminjaman Kegiatan Lain',
                    'pesan' => $pesan,
                    'tipe' => 'pinjam_lain_status',
                    'tautan' => '/pinjam-lain',
                    'meta' => [
                        'pinjam_lain_id' => $proses->id,
                        'status' => $data,
                        'alasan_penolakan' => $data === 'ditolak' ? $alasanPenolakan : null,
                    ],
                    'dibaca' => false,
                ]);
            }
        }
        return response()->json($proses);
    }
    public function pinjamLabCopy($id)
    {
        $cek=pinjam_lab::where('id', $id)->first();
        $jml=jumlah_pinjam::where('pinjam_lab_id',$id)->get();
        $datas=[];
        $data=pinjam_lab::create([
            'kelas_id'=>$cek->kelas_id,
            'katalog_id'=>$cek->katalog_id,
            'tgl'=>$cek->tgl,
            'peminjam'=>$cek->peminjam,
            'pekan'=>$cek->pekan,
            'jam'=>$cek->jam,
            'status'=>'diajukan',
            'alasan_penolakan' => null,
            'jam_selesai'=>$cek->jam_selesai,
            'user_id'=>Auth()->User()->id,

        ]);
        foreach($jml as $row){
            $datas[]=[
                'data_katalog_id' => $row->data_katalog_id,
                'pinjam_lab_id' => $data->id,
                'minta' => $row->minta,
                'diberi' => 0,
            ];
        }
        jumlah_pinjam::insert($datas);
        $data->modulLkpd()->sync($cek->modulLkpd()->pluck('modul_lkpd.id')->toArray());
        return response()->json($data->load('modulLkpd.uploader'));
    }
    public function pinjamLainCopy($id)
    {
        $cek = pinjam_lain::where('id', $id)->first();

        if (!$cek) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $data = pinjam_lain::create([
            'user_id' => Auth()->User()->id,
            'tgl' => $cek->tgl,
            'mulai' => $cek->mulai,
            'selesai' => $cek->selesai,
            'kegiatan' => $cek->kegiatan,
            'status' => 'diajukan',
            'alasan_penolakan' => null,
        ]);

        return response()->json($data);
    }
    public function pinjamAlatCopy($id)
    {
        $cek=pinjam_alat::where('id', $id)->first();
        $jml=jumlah_pinjam_alat::where('pinjam_alat_id', $id)->get();
        $datas=[];
        $data=pinjam_alat::create([
            'kelas_id'=>$cek->kelas_id,
            'katalog_id'=>$cek->katalog_id,
            'user_id'=>Auth()->User()->id,
            'tgl_pakai'=>$cek->tgl_pakai,
            'tgl_kembali'=>$cek->tgl_kembali,
            'jam_pakai'=>$cek->jam_pakai,
            'jam_kembali'=>$cek->jam_kembali,
            'jam'=>$cek->jam,
            'lokasi'=>$cek->lokasi,
            'keperluan'=>$cek->keperluan,
            'status'=>'diajukan',
            'alasan_penolakan' => null,
        ]);
        foreach($jml as $row){
            $datas[]=[
                'data_katalog_id'=>$row->data_katalog_id,
                'pinjam_alat_id'=>$data->id,
                'minta' => $row->minta,
                'diberi' => 0,
            ];
        }
        jumlah_pinjam_alat::insert($datas);
        $data->modulLkpd()->sync($cek->modulLkpd()->pluck('modul_lkpd.id')->toArray());
        return response()->json($data->load('modulLkpd.uploader'));
    }

}
