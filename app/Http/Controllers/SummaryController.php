<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Response;

class SummaryController extends Controller
{
    public function summary(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $totalAlat = ProductEquipment::count();
        $prosesQcCount = ProductEquipment::where('status_qc', 'proses_qc')->count();
        $histories = History::query();
        if($tahun)
        {
            $histories->whereYear('waktu_mulai', $tahun);
        }
        if($bulan)
        {
            $histories->whereMonth('waktu_selesai', $bulan);
        }
        // ->when($tahun, fn($q) => $q->whereYear('waktu_selesai', $tahun))
        // ->when($bulan, fn($q) => $q->whereMonth('waktu_selesai', $bulan));

        $okCount = (clone $histories)->where('status_akhir', 'OK')->count();
        $notOkCount = (clone $histories)->where('status_akhir', 'NOT OK')->count();
        $progressCount = (clone $histories)->where('status_akhir', 'PROGRESS')->where('is_canceled', 0)->count();
        return response()->json([
            'total' => $totalAlat,
            'proses_qc'=> $prosesQcCount,
            'ok'=> $okCount,
            'not_ok'=> $notOkCount,
            'progress'=> $progressCount,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        // Data summary alat produksi
        $productEquipments = ProductEquipment::with(['productEquipmentType', 'location']);
        $totalAlat = $productEquipments->get()->map(function($item) {
            return [
                'nama_alpro' => $item->productEquipmentType->name ?? '-',
                'sn' => $item->sn,
                'tagg' => $item->tagg,
                'merk' => $item->merk,
                'location' => $item->location->nama ?? '-',
            ];
        });
        // Data histories (OK, NOT OK, PROGRESS, is_canceled = 0)
        $histories = History::with(['productEquipment.location', 'productEquipment.productEquipmentType', 'user'])
            ->where('is_canceled', 0);
        if ($tahun) {
            $histories->whereYear('waktu_mulai', $tahun);
        }
        if ($bulan) {
            $histories->whereMonth('waktu_mulai', $bulan);
        }
        $historiesData = $histories->get()->map(function($item) {
            return [
                'history_id' => $item->id,
                'Nama Alpro' => $item->productEquipment->productEquipmentType->name?? '-',
                'Engineer Test'=> $item->user->name?? '-',
                'Waktu Mulai' => $item->waktu_mulai,
                'Waktu Selesai' => $item->waktu_selesai,
                'Status Akhir' => $item->status_akhir,
                'SN' => $item->productEquipment->sn ?? '-',
                'TAGG' => $item->productEquipment->tagg ?? '-',
                'Merk' => $item->productEquipment->merk ?? '-',
                'Location' => $item->productEquipment->location->nama ?? '-',
            ];
        });
        // Export dengan 2 sheet
        return Excel::download(new class($totalAlat, $historiesData) implements WithMultipleSheets {
            private $summaryData;
            private $historiesData;
            public function __construct($summaryData, $historiesData) {
                $this->summaryData = $summaryData;
                $this->historiesData = $historiesData;
            }
            public function sheets(): array {
                return [
                    new class($this->summaryData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithHeadings {
                        private $data;
                        public function __construct($data) { $this->data = $data; }
                        public function array(): array { return $this->data->toArray(); }
                        public function headings(): array { return ['Nama Alpro', 'SN', 'TAGG', 'Merk', 'Location']; }
                        public function title(): string { return 'Total Alat Produksi'; }
                    },
                    new class($this->historiesData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithHeadings {
                        private $data;
                        public function __construct($data) { $this->data = $data; }
                        public function array(): array { return $this->data->toArray(); }
                        public function headings(): array { return ['history_id','Nama Alpro', 'Engineer Test','Waktu Mulai', 'Waktu Selesai', 'Status Akhir', 'SN', 'TAGG', 'Merk', 'Location']; }
                        public function title(): string { return 'Histories'; }
                    }
                ];
            }
        }, 'summary_export.xlsx');
    }
            // Route: GET /api/dashboard/summary/detail
        public function summaryDetail(Request $request)
        {
            $tahun = $request->input('tahun');
            $bulan = $request->input('bulan');
            $status = $request->input('status');

            // Query join histories dan product_equipment
            $query = History::with('productEquipment')
                ->where('status_akhir', $status)
                ->where('is_canceled', 0);

            if ($tahun) $query->whereYear('waktu_selesai', $tahun);
            if ($bulan) $query->whereMonth('waktu_selesai', $bulan);

            // Group by alat, count history per alat
            $data = $query
                ->selectRaw('product_equipment_id, COUNT(*) as total')
                ->groupBy('product_equipment_id')
                ->get()
                ->map(function($item) {
                    $alat = $item->productEquipment;
                    return [
                        'nama_alat' => $alat->productEquipmentType->name ?? '-',
                        'sn' => $alat->sn ?? '-',
                        'tagg' => $alat->tagg ?? '-',
                        'total' => $item->total
                    ];
                });
                if(strtoupper($status) === "NOT OK")
                {
                    $data = $data->sortByDesc('total')->values();
                }

            return response()->json($data);
        }

}
