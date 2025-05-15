<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function summary(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        // Query jumlah alat produksi dengan status_qc = 'proses_qc'
        $prosesQcCount = ProductEquipment::where('status_qc', 'proses_qc')->count();

        // Query total alat produksi
        $totalAlat = ProductEquipment::count();

        // Query jumlah status OK, NOT OK, PROGRESS (khusus PROGRESS: is_canceled = 0)
        $histories = History::query();

        if ($tahun) {
            $histories->whereYear('waktu_mulai', $tahun);
        }
        if ($bulan) {
            $histories->whereMonth('waktu_mulai', $bulan);
        }

        $okCount = (clone $histories)->where('status_akhir', 'OK')->count();
        $notOkCount = (clone $histories)->where('status_akhir', 'NOT OK')->count();
        $progressCount = (clone $histories)
            ->where('status_akhir', 'PROGRESS')
            ->where('is_canceled', 0)
            ->count();

        return response()->json([
            'proses_qc' => $prosesQcCount,
            'ok' => $okCount,
            'not_ok' => $notOkCount,
            'progress' => $progressCount,
            'total' => $totalAlat,
        ]);
    }
}