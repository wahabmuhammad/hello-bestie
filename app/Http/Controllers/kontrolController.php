<?php

namespace App\Http\Controllers;

use App\Jobs\kirimPesanFonnte;
use App\Jobs\notifikasiBatalPraktik;
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
        $idRuangan = $request->input('idruangan');
        $idDokter = $request->input('iddokter');
        // dd($idDokter);

        $query = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->leftJoin('emrpasiend_t as ed', function ($join) {
                $join->on('ed.emrpasienfk', '=', 'emrpasiend_t.emrpasienfk')
                    ->where('ed.emrdfk', '=', 43000006); // dokter
            })
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003') // tgl kontrol
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'ilike', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'ilike', '%' . $search . '%');
                });
            })
            ->when($idRuangan, function ($query) use ($idRuangan) {
                $query->whereExists(function ($sub) use ($idRuangan) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as er')
                        ->whereColumn('er.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('er.emrdfk', 43000002) // ruangan
                        ->where(DB::raw("split_part(er.value, '~', 1)"), '=', $idRuangan);
                });
            })
            ->when($idDokter, function ($query) use ($idDokter) {
                $query->whereExists(function ($sub) use ($idDokter) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as ed2')
                        ->whereColumn('ed2.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('ed2.emrdfk', 43000006) // dokter
                        ->where(DB::raw("split_part(ed2.value, '~', 1)"), '=', $idDokter);
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
                'pasien_m.tgllahir',
                DB::raw("COALESCE(split_part(ed.value, '~', 2), '-') as namadokter")
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->paginate(10);


        if ($request->ajax()) {
            return response()->json([
                'datas' => $query->items(),
                'pagination' => (string) $query->links()
            ]);
        }

        return view('admin.daftarkontrol');
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
        $idRuangan = $request->input('idruangan');
        $idDokter = $request->input('iddokter');
        // dd($idDokter);

        $query = DB::table('emrpasiend_t')
            ->leftJoin('emrpasien_t', 'emrpasien_t.noemr', '=', 'emrpasiend_t.emrpasienfk')
            ->leftJoin('pasien_m', 'pasien_m.nocm', '=', 'emrpasien_t.nocm')
            ->leftJoin('pegawai_m', 'pegawai_m.id', '=', 'emrpasien_t.pegawaifk')
            ->leftJoin('emrpasiend_t as ed', function ($join) {
                $join->on('ed.emrpasienfk', '=', 'emrpasiend_t.emrpasienfk')
                    ->where('ed.emrdfk', '=', 43000006); // dokter
            })
            ->where('emrpasiend_t.emrfk', '=', '210047')
            ->where('emrpasiend_t.emrdfk', '=', '43000003') // tgl kontrol
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pasien_m.namapasien', 'ilike', '%' . $search . '%')
                        ->orWhere('pasien_m.nocm', 'ilike', '%' . $search . '%');
                });
            })
            ->when($idRuangan, function ($query) use ($idRuangan) {
                $query->whereExists(function ($sub) use ($idRuangan) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as er')
                        ->whereColumn('er.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('er.emrdfk', 43000002) // ruangan
                        ->where(DB::raw("split_part(er.value, '~', 1)"), '=', $idRuangan);
                });
            })
            ->when($idDokter, function ($query) use ($idDokter) {
                $query->whereExists(function ($sub) use ($idDokter) {
                    $sub->select(DB::raw(1))
                        ->from('emrpasiend_t as ed2')
                        ->whereColumn('ed2.emrpasienfk', 'emrpasiend_t.emrpasienfk')
                        ->where('ed2.emrdfk', 43000006) // dokter
                        ->where(DB::raw("split_part(ed2.value, '~', 1)"), '=', $idDokter);
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
                'pasien_m.tgllahir',
                DB::raw("COALESCE(split_part(ed.value, '~', 2), '-') as namadokter")
            )
            ->orderBy('emrpasiend_t.value', 'asc')
            ->paginate(10);


        if ($request->ajax()) {
            return response()->json([
                'datas' => $query->items(),
                'pagination' => (string) $query->links()
            ]);
        }

        return view('admin.batalKontrol');
    }

    public  function getRuangan(Request $request)
    {
        $search = $request->input('query');
        $query = DB::table('ruangan_m')
            ->where('ruangan_m.statusenabled', '=', 'true')
            ->whereIn('ruangan_m.objectdepartemenfk', [18, 16]) // Ruangan
            ->select(
                'ruangan_m.id',
                'ruangan_m.namaruangan'
            )
            ->when($search, function ($query, $search) {
                $query->where('namaruangan', 'ilike', '%' . $search . '%');
            })
            ->get();
        // dd($query);

        return response()->json([
            'datas' => $query,
            'status' => 'success',
            'code' => 200,
        ]);
    }

    public function getDokter(Request $request)
    {
        $search = $request->input('query');
        $query = DB::table('pegawai_m')
            ->where('pegawai_m.statusenabled', '=', 'true')
            ->where('pegawai_m.objectjenispegawaifk', '=', 1) // Ruangan
            ->select(
                'pegawai_m.id',
                'pegawai_m.namalengkap as namadokter'
            )
            ->when($search, function ($query, $search) {
                $query->where('namalengkap', 'ilike', '%' . $search . '%');
            })
            ->get();
        // dd($query);

        return response()->json([
            'datas' => $query,
            'status' => 'success',
            'code' => 200,
        ]);
    }

    public function notifikasiBatalPraktik(Request $request)
    {
        dd($request->all());
        $tglAwal = $request->input('tglAwal');
        $tglAkhir = $request->input('tglAkhir');
        $idRuangan = $request->input('idruangan');
        $idDokter = $request->input('iddokter');
        $search = $request->input('search');

        dispatch(new notifikasiBatalPraktik($tglAwal, $tglAkhir, $idRuangan, $idDokter,$search)); // Test kirim pesan

        return response()->json([
            'message' => 'Notifikasi batal praktik berhasil diproses.',
            'status' => 'success',
            'code' => 200,
        ]);
    }
}
