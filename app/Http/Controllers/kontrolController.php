<?php

namespace App\Http\Controllers;

use App\Jobs\kirimPesanFonnte;
use App\Jobs\prosesNotifikasiPasienKontrol;
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
            ->where('emrpasiend_t.emrdfk', '=', '43000003') // tgl kontrol
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
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000006')
            ->select(
                'emrpasien_t.noemr',
                DB::raw("split_part(emrpasiend_t.value, '~', 2) as namadokter")
            )
            ->get();

        // Query data ruangan
        $queryRuangan = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000002') // ruangan
            ->select(
                'emrpasien_t.noemr',
                DB::raw("split_part(emrpasiend_t.value, '~', 2) as namaruangan")
            )
            ->get();

        // Buat mapping dokter & ruangan berdasarkan noemr
        $dokterMap = collect($queryDokter)->keyBy('noemr');
        $ruanganMap = collect($queryRuangan)->keyBy('noemr');

        // Gabungkan ke data kontrol
        $kontrolItems = $query->items();
        $datas = collect($kontrolItems)->map(function ($item) use ($dokterMap, $ruanganMap) {
            $item->namadokter = $dokterMap[$item->noemr]->namadokter ?? '-';
            $item->namaruangan = $ruanganMap[$item->noemr]->namaruangan ?? '-';
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

        dispatch(new prosesNotifikasiPasienKontrol($tglAwal, $tglAkhir)); // Test kirim pesan

        return response()->json([
            'message' => 'Notifikasi berhasil diproses.',
            'status' => 'success',
            'code' => 200,
        ]);
    }

    public function singleNotification(Request $request)
    {
        //       
    }

    public function batalKontrol(Request $request)
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

        return view('admin.batalKontrol', compact('datas'));
    }
}
