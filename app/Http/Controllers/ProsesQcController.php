<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use App\Models\ProductEquipmentType;
use App\Models\History;
use App\Models\ElementTest;
use App\Models\MasterElementTest;
use Illuminate\Support\Facades\Auth;

class ProsesQcController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductEquipment::with(['productEquipmentType', 'location'])
            ->where('status_qc', 'proses_qc');
    
        if ($request->filled('sn')) {
            $query->where('sn', 'like', '%' . $request->sn . '%');
        }
    
        if ($request->filled('tagg')) {
            $query->where('tagg', 'like', '%' . $request->tagg . '%');
        }
    
        if ($request->filled('nama_alpro')) {
            $query->whereHas('productEquipmentType', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama_alpro . '%');
            });
        }
    
        if ($request->filled('lokasi')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->lokasi . '%');
            });
        }
    
        $equipments = $query->get();
    
        return view('proses_qc', compact('equipments'));
    }
    public function startWorkshopTest($equipmentId)
    {
        $equipment = ProductEquipment::findOrFail($equipmentId);
        // 1. Buat history baru
        $history = History::create([
            'product_equipment_id' => $equipment->id,
            'user_id' => Auth::id(),
            'waktu_mulai' => now(),
            'status_akhir' => 'PROGRESS',
            'is_canceled' => false,
            'waktu_selesai' => null,
        ]);
        // 2. Buat element_tests default dari master_element_tests
        $masterElements = MasterElementTest::where('product_equipment_type_id', $equipment->product_equipment_type_id)->get();
        foreach ($masterElements as $element) {
            ElementTest::create([
                'history_id' => $history->id,
                'nama_element' => $element->nama_element,
                'hasil_test' => null,
                'status' => 'PROGRESS',
                'keterangan' => $element->keterangan_ok ?? '',
            ]);
        }
        // 3. Update status_qc alat
        $equipment->status_qc = 'hasil_test';
        $equipment->save();
        // 4. Redirect ke form edit dengan ID history baru
        return redirect()->route('hasil-test.edit', $history->id);
    }
    
}