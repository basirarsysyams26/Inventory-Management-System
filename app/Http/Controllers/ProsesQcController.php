<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use App\Models\ProductEquipmentType;

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
    
}