<?php

namespace App\Http\Controllers;

use App\Jobs\kirimPesanFonnte;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class kontrolController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        // Query data kontrol
        $query = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'ilike', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'ilike', '%' . $search . '%');
                });
            })
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween(DB::raw("TO_TIMESTAMP(emrpasiend_t.value, 'YYYY-MM-DD HH24:MI')"), [
                    $tglAwal . ' 00:00',
                    $tglAkhir . ' 23:59'
                ]);
            }, function ($query) use ($search) {
                if (empty($search)) {
                    $today = now()->format('Y-m-d');
                    $query->whereBetween(DB::raw("TO_TIMESTAMP(emrpasiend_t.value, 'YYYY-MM-DD HH24:MI')"), [
                        $today . ' 00:00',
                        $today . ' 23:59'
                    ]);
                }
            })
            ->select(
                'pegawai_m.namalengkap as namapegawai',
                'emrpasien_t.namaruangan',
                'emrpasien_t.noemr',
                'emrpasiend_t.value as tglkontrol',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasien_m.nohp',
                'pasien_m.tgllahir'
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->paginate(10);

        // Query data dokter
        $queryDokter = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000006')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'ilike', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'ilike', '%' . $search . '%');
                });
            })
            ->select(
                'emrpasien_t.noemr',
                DB::raw("split_part(emrpasiend_t.value, '~', 2) as namadokter")
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->get();

        // Gabungkan data dokter ke data kontrol
        $kontrolItems = $query->items();
        $dokterMap = collect($queryDokter)->keyBy('noemr');

        $datas = collect($kontrolItems)->map(function ($item) use ($dokterMap) {
            $item->namadokter = $dokterMap[$item->noemr]->namadokter ?? '-';
            return $item;
        });

        if ($request->ajax()) {
            return response()->json([
                'datas' => $datas,
                'pagination' => (string) $query->links()
            ]);
        }

        return view('admin.daftarkontrol', compact('datas'));
    }

    public function getDataPasienKontrol(Request $request)
    {
        $datas = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003')
            ->limit(10)
            ->get();
        // This is a placeholder; implement your logic here
        return response()->json([
            'message' => 'Data Pasien Kontrol retrieved successfully',
            'datas' => $datas,
            'pagination' => (string) $datas->links(),
            'status' => 'success',
            'code' => 200,
            // Add actual data here
        ]);
    }

    public function searchData(Request $request)
    {
        // dd(['tipe_data_value' => DB::getSchemaBuilder()->getColumnType('emrpasiend_t', 'value')]);
        $search = $request->input('search');
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        // Query data kontrol
        $query = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'like', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'like', '%' . $search . '%');
                });
            })
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween(DB::raw("TO_TIMESTAMP(emrpasiend_t.value, 'YYYY-MM-DD HH24:MI')"), [
                    $tglAwal . ' 00:00',
                    $tglAkhir . ' 23:59'
                ]);
            })
            ->select(
                'pegawai_m.namalengkap as namapegawai',
                'emrpasien_t.namaruangan',
                'emrpasien_t.noemr',
                'emrpasiend_t.value as tglkontrol',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasien_m.nohp',
                'pasien_m.tgllahir'
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->paginate(10);

        // Query data dokter
        $queryDokter = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000006')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'like', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'like', '%' . $search . '%');
                });
            })
            ->select(
                'emrpasien_t.noemr',
                'emrpasiend_t.value as namadokter'
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->get(); // Tidak paginate untuk kemudahan penggabungan

        // Gabungkan data
        $kontrolItems = $query->items();
        $dokterMap = collect($queryDokter)->keyBy('noemr');

        $results = collect($kontrolItems)->map(function ($item) use ($dokterMap) {
            $item->namadokter = $dokterMap[$item->noemr]->namadokter ?? '-';
            return $item;
        });
        // dd($results);
        return response()->json([
            'datas' => $results,
            'pagination' => (string) $query->links()
        ]);
    }

    public function kirimNotifikasi(Request $request)
    {
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');

        // Ambil data kontrol dan dokter
        $datas = DB::table('emrpasiend_t as kontrol')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'kontrol.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->leftJoin(
                DB::raw("(SELECT emrpasienfk, split_part(value, '~', 2) as namadokter 
                            FROM emrpasiend_t 
                            WHERE emrdfk = '43000006') as dokter"),
                'dokter.emrpasienfk',
                '=',
                'kontrol.emrpasienfk'
            )
            ->where('kontrol.emrfk', '=', '210047')
            ->where('kontrol.emrdfk', '=', '43000003') // Tgl kontrol
            ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
                $query->whereBetween(DB::raw("TO_TIMESTAMP(kontrol.value, 'YYYY-MM-DD HH24:MI')"), [
                    $tglAwal . ' 00:00',
                    $tglAkhir . ' 23:59'
                ]);
            })
            ->select(
                'pegawai_m.namalengkap as namapegawai',
                'emrpasien_t.namaruangan',
                'emrpasien_t.noemr',
                'kontrol.value as tglkontrol',
                'pasien_m.nocm',
                'pasien_m.namapasien',
                'pasien_m.nohp',
                'pasien_m.tgllahir',
                'dokter.namadokter'
            )
            ->orderBy('kontrol.value', 'asc')
            ->get();
        Carbon::setLocale('id');
        foreach ($datas as $data) {
            if ($data->tglkontrol) {
                $carbon = Carbon::parse($data->tglkontrol);
                $data->harikontrol = $carbon->translatedFormat('l'); // Contoh: Senin
                $data->formatTgl = $carbon->translatedFormat('d F Y'); // Contoh: 31 Juli 2025
            } else {
                $data->harikontrol = '-';
                $data->formatTgl = '-';
            }

            // Format nama dokter: fallback ke nama pegawai jika kosong
            $namadokter = $data->namadokter ?: '-';

            $pesan = "Assalamu'alaikum Wr. Wb. 

Kepada Yth. *" . trim($data->namapasien) . "*

Kami dari RS Sarkies 'Aisyiyah Kudus ingin menginformasikan jadwal kontrol pada:

Hari, tgl   : " . trim($data->harikontrol) . ", " . trim($data->formatTgl) . "
Poliklinik  : " . trim($data->namaruangan) . "
Dokter     : " . trim($namadokter) . "
*Jadwal dokter bisa berubah sewaktu-waktu*

- Jika pembiayaan dengan BPJS, pendaftaran melalui aplikasi Mobile JKN, maksimal dilakukan H-1 dari hari pemeriksaan

Harap membawa persyaratan berikut ini:
1. Surat kontrol
2. Resume medis (bagi pasien rawat inap)

Demikian informasi yang dapat kami sampaikan, atas perhatiannya kami sampaikan terimakasih.  

Salam sehat.

*Perihal jadwal dokter bisa klik dan ikuti link dibawah ini👇🏻.*
https://whatsapp.com/channel/0029Vamy8ZSDeON9NVKWcb1K

Wassalamu’alaikum Wr. Wb.";

            $phone = '0' . ltrim($data->nohp, '0');
            // $phone = '081215837977';

            // Kirim pesan (dapat diaktifkan)
            dispatch(new kirimPesanFonnte($phone, $pesan));
        }
        // dd($phone, $pesan);

        return response()->json([
            'message' => 'Notifikasi berhasil diproses.',
            'status' => 'success',
            'code' => 200,
        ]);
    }

    public function singleNotification(Request $request)
    {
//         $tglAwal = $request->input('tglAwal');
//         $tglAkhir = $request->input('tglAkhir');

//         // Ambil data kontrol dan dokter
//         $data = DB::table('emrpasiend_t as kontrol')
//             ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'kontrol.emrpasienfk')
//             ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
//             ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
//             ->leftJoin(
//                 DB::raw("(SELECT emrpasienfk, split_part(value, '~', 2) as namadokter 
//                             FROM emrpasiend_t 
//                             WHERE emrdfk = '43000006') as dokter"),
//                 'dokter.emrpasienfk',
//                 '=',
//                 'kontrol.emrpasienfk'
//             )
//             ->where('kontrol.emrfk', '=', '210047')
//             ->where('kontrol.emrdfk', '=', '43000003') // Tgl kontrol
//             ->when($tglAwal && $tglAkhir, function ($query) use ($tglAwal, $tglAkhir) {
//                 $query->whereBetween(DB::raw("TO_TIMESTAMP(kontrol.value, 'YYYY-MM-DD HH24:MI')"), [
//                     $tglAwal . ' 00:00',
//                     $tglAkhir . ' 23:59'
//                 ]);
//             })
//             ->select(
//                 'pegawai_m.namalengkap as namapegawai',
//                 'emrpasien_t.namaruangan',
//                 'emrpasien_t.noemr',
//                 'kontrol.value as tglkontrol',
//                 'pasien_m.nocm',
//                 'pasien_m.namapasien',
//                 'pasien_m.nohp',
//                 'pasien_m.tgllahir',
//                 'dokter.namadokter'
//             )
//             ->orderBy('kontrol.value', 'asc')
//             ->limit(1)
//             ->get();
//         Carbon::setLocale('id');
//         $carbon = Carbon::parse($data->tglkontrol);
//         $data->harikontrol = $carbon->translatedFormat('l'); // Contoh: Senin
//         $data->formatTgl = $carbon->translatedFormat('d F Y');

//         $namadokter = $data->namadokter ?: '-';

//             $pesan = "Assalamu'alaikum Wr. Wb. 

// Kepada Yth. *" . trim($data->namapasien) . "*

// Kami dari RS Sarkies 'Aisyiyah Kudus ingin menginformasikan jadwal kontrol pada:

// Hari, tgl   : " . trim($data->harikontrol) . ", " . trim($data->formatTgl) . "
// Poliklinik  : " . trim($data->namaruangan) . "
// Dokter     : " . trim($namadokter) . "
// *Jadwal dokter bisa berubah sewaktu-waktu*

// - Jika pembiayaan dengan BPJS, pendaftaran melalui aplikasi Mobile JKN, maksimal dilakukan H-1 dari hari pemeriksaan

// Harap membawa persyaratan berikut ini:
// 1. Surat kontrol
// 2. Resume medis (bagi pasien rawat inap)

// Demikian informasi yang dapat kami sampaikan, atas perhatiannya kami sampaikan terimakasih.  

// Salam sehat.

// Perihal jadwal dokter bisa klik dan ikuti link dibawah ini👇🏻.
// https://whatsapp.com/channel/0029Vamy8ZSDeON9NVKWcb1K

// Wassalamu’alaikum Wr. Wb.";

//             // $phone = '0' . ltrim($data->nohp, '0');
//             $phone = '081215837977';

//             // Kirim pesan (dapat diaktifkan)
//             dispatch(new kirimPesanFonnte($phone, $pesan));
//         // dd($phone, $pesan);

//         return response()->json([
//             'message' => 'Notifikasi berhasil diproses.',
//             'status' => 'success',
//             'code' => 200,
//         ]);
    }

    public function batalKontrol
}
