<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductEquipment; // Pastikan model sesuai
use App\Models\History;  // Pastikan model sesuai

class RiwayatController extends Controller
{
    // public function index($sn, $tagg)
    // {
    //     $histories = History::with(['productEquipment.productEquipmentType'])
    //         ->whereHas('productEquipment', function($q) use ($sn, $tagg) {
    //             $q->where('sn', $sn)->where('tagg', $tagg);
    //         })
    //         ->orderBy('waktu_mulai', 'desc')
    //         ->get();

    //     return view('riwayat', compact('histories'));
    // }
    public function index($sn, $tagg)
    {
        $histories = \App\Models\History::whereHas('productEquipment', function($q) use ($sn, $tagg) {
            $q->where('sn', $sn)->where('tagg', $tagg);
        })
        ->where('is_canceled', false)
        ->orderByDesc('waktu_mulai')
        ->get();

        return view('riwayat', compact('histories'));
    }

    public function restore($historyId)
    {
        $history = \App\Models\History::with('productEquipment')->findOrFail($historyId);

        // Validasi: hanya bisa restore jika status_akhir PROGRESS dan belum dibatalkan
        if ($history->status_akhir !== 'PROGRESS' || $history->is_canceled) {
            return redirect()->back()->with('error', 'History tidak dapat dibatalkan.');
        }

        // Set is_canceled = true
        $history->is_canceled = true;
        $history->save();

        // Update status_qc pada product_equipments menjadi "proses_qc"
        $equipment = $history->productEquipment;
        $equipment->status_qc = 'proses_qc';
        $equipment->save();

        return redirect()->back()->with('success', 'Progress berhasil dibatalkan dan alat dikembalikan ke proses QC.');
    }
   
}