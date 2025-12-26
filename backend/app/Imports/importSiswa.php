<?php

namespace App\Imports;

use App\Models\data_siswa;
use App\Models\User;
use App\Models\foto_profile;
use App\Models\kelas_siswa;
use Maatwebsite\Excel\Concerns\ToModel;

class importSiswa implements ToModel
{
    public static $duplicates = [];
    public static $imported = 0;
    public static $importedNis = [];

    public static function resetCounters()
    {
        self::$duplicates = [];
        self::$imported = 0;
        self::$importedNis = [];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip jika row kosong atau header
        $nis = trim($row[0] ?? '');
        $nama = trim($row[1] ?? '');
        $kelas_id = $row[2] ?? null;
        $email = trim($row[3] ?? '');

        // Skip jika NIS kosong atau jika ini adalah header row
        if (empty($nis) || strtolower($nis) === 'nis' || !is_numeric($nis)) {
            return null;
        }

        // Cek apakah NIS sudah ada di database
        $existingSiswa = data_siswa::where('nis', $nis)->first();
        if ($existingSiswa) {
            self::$duplicates[] = [
                'nis' => $nis,
                'nama' => $nama,
                'alasan' => 'NIS sudah terdaftar di database'
            ];
            return null;
        }

        // Cek apakah NIS sudah ada di batch import ini (duplikat dalam file excel)
        if (in_array($nis, self::$importedNis)) {
            self::$duplicates[] = [
                'nis' => $nis,
                'nama' => $nama,
                'alasan' => 'NIS duplikat dalam file Excel'
            ];
            return null;
        }
        self::$importedNis[] = $nis;

        // Simpan data siswa
        $dataSiswa = data_siswa::create([
            'nis' => $nis,
            'nama' => $nama,
            'kelas_id' => $kelas_id,
            'email' => $email,
        ]);

        // Auto create user account dengan password = NIS
        $existingUser = User::where('email', $email)->orWhere('username', $nis)->first();
        if (!$existingUser && !empty($email)) {
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'username' => $nis, // NIS sebagai username
                'password' => bcrypt($nis), // NIS sebagai password default
                'role_id' => 4 // Role siswa
            ]);

            // Create foto profile default
            foto_profile::create([
                'user_id' => $user->id,
                'foto' => 'foto/user.jpg'
            ]);

            // Create kelas_siswa relation
            kelas_siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $kelas_id
            ]);
        }

        self::$imported++;
        return null; // Return null karena sudah di-save manual
    }
}


