<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\ElementTest;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function show($history_id)
    {
        // $history = History::with(['productEquipment', 'user', 'images'])->findOrFail($history_id);

        // Batasi akses hanya teknisi yang melakukan tes
        // if (auth()->user()->id !== $history->user_id) {
        //     abort(403, 'Unauthorized');
        // }

        // $equipment = $history->productEquipment;
        // $elementTests = ElementTest::where('history_id', $history_id)->get();

        // return view('surat', compact('history', 'equipment', 'elementTests'));
        $history = History::with(['productEquipment', 'user', 'images'])->findOrFail($history_id);

        // Batasi akses hanya teknisi yang melakukan tes
        // if (auth()->user()->id !== $history->user_id) {
        //     abort(403, 'Unauthorized');
        // }

        $equipment = $history->productEquipment;
        $elementTests = ElementTest::where('history_id', $history_id)->get();

        return view('surat', compact('history', 'equipment', 'elementTests'));
    }
}