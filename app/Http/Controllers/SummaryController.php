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
    // public function exportExcel(Request $request)
    // {
    //     $tahun = $request->input('tahun');
    //     $bulan = $request->input('bulan');

    //     $query = ProductEquipment::with(['productEquipmentType', 'location']);
    //     if ($tahun) {
    //         $query->whereYear('created_at', $tahun);
    //     }
    //     if ($bulan) {
    //         $query->whereMonth('created_at', $bulan);
    //     }
    //     $equipments = $query->get();

    //     $data = $equipments->map(function($item) {
    //         return [
    //             'nama_alpro' => $item->productEquipmentType->name ?? '-',
    //             'sn' => $item->sn,
    //             'tagg' => $item->tagg,
    //             'merk' => $item->merk,
    //             'location' => $item->location->nama ?? '-',
    //         ];
    //     });

    //     // Membuat file Excel secara dinamis
    //     $export = new \ArrayObject([$data->toArray()]);
    //     return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
    //         private $data;
    //         public function __construct($data) { $this->data = $data; }
    //         public function array(): array { return $this->data->toArray(); }
    //         public function headings(): array {
    //             return ['nama_alpro', 'sn', 'tagg', 'merk', 'location'];
    //         }
    //     }, 'summary_export.xlsx');
    // }
    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        // Data summary alat produksi
        $query = ProductEquipment::with(['productEquipmentType', 'location']);
        if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }
        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }
        $equipments = $query->get();
        $data = $equipments->map(function($item) {
            return [
                'nama_alpro' => $item->productEquipmentType->name ?? '-',
                'sn' => $item->sn,
                'tagg' => $item->tagg,
                'merk' => $item->merk,
                'location' => $item->location->nama ?? '-',
            ];
        });

        // Data histories (OK, NOT OK, PROGRESS, is_canceled = 0)
        $histories = History::with(['productEquipment.location'])
            ->where('is_canceled', 0)
            ->whereIn('status_akhir', ['OK', 'NOT OK', 'PROGRESS']);
        if ($tahun) {
            $histories->whereYear('waktu_mulai', $tahun);
        }
        if ($bulan) {
            $histories->whereMonth('waktu_mulai', $bulan);
        }
        $historiesData = $histories->get()->map(function($item) {
            return [
                'history_id' => $item->id,
                'nama_alpro' => $item->productEquipment->productEquipmentType->name?? '-',
                'engineer_test'=> $item->user->name?? '-',
                'waktu_mulai' => $item->waktu_mulai,
                'waktu_selesai' => $item->waktu_selesai,
                'status_akhir' => $item->status_akhir,
                'sn' => $item->productEquipment->sn ?? '-',
                'tagg' => $item->productEquipment->tagg ?? '-',
                'merk' => $item->productEquipment->merk ?? '-',
                'location' => $item->productEquipment->location->nama ?? '-',
            ];
        });

        // Export dengan 2 sheet
        return Excel::download(new class($data, $historiesData) implements WithMultipleSheets {
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
                        public function headings(): array { return ['nama_alpro', 'sn', 'tagg', 'merk', 'location']; }
                        public function title(): string { return 'Summary'; }
                    },
                    new class($this->historiesData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithHeadings {
                        private $data;
                        public function __construct($data) { $this->data = $data; }
                        public function array(): array { return $this->data->toArray(); }
                        public function headings(): array { return ['history_id','nama_alpro', 'engineer_test','waktu_mulai', 'waktu_selesai', 'status_akhir', 'sn', 'tagg', 'merk', 'location']; }
                        public function title(): string { return 'Histories'; }
                    }
                ];
            }
        }, 'summary_export.xlsx');
    }
}