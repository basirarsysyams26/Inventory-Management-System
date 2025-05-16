<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\History;
use App\Models\ElementTest;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use App\Models\MasterElementTest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HasilTestController extends Controller
{
   
    public function index(Request $request)
    {
        // Ambil parameter pencarian
        $search_nama_alpro = $request->input('search_nama_alpro');
        $search_sn = $request->input('search_sn');
        $search_tagg = $request->input('search_tagg');
        $search_waktu_mulai = $request->input('search_waktu_mulai');
        $search_waktu_selesai = $request->input('search_waktu_selesai');
        // Ambil hanya history terbaru untuk setiap alat
        $sub = \App\Models\History::selectRaw('MAX(id) as id')
            ->where('is_canceled', 0)
            ->groupBy('product_equipment_id');
        $query = \App\Models\History::with(['productEquipment.productEquipmentType'])
            ->whereIn('id', $sub);
        // Filter berdasarkan nama alpro
        if ($search_nama_alpro) {
            $query->whereHas('productEquipment.productEquipmentType', function($q) use ($search_nama_alpro) {
                $q->where('name', 'like', '%' . $search_nama_alpro . '%');
            });
        }
        // Filter berdasarkan SN
        if ($search_sn) {
            $query->whereHas('productEquipment', function($q) use ($search_sn) {
                $q->where('sn', 'like', '%' . $search_sn . '%');
            });
        }
        // Filter berdasarkan TAGG
        if ($search_tagg) {
            $query->whereHas('productEquipment', function($q) use ($search_tagg) {
                $q->where('tagg', 'like', '%' . $search_tagg . '%');
            });
        }
        // Filter berdasarkan waktu mulai
        if ($search_waktu_mulai) {
            $query->whereDate('waktu_mulai', '=', $search_waktu_mulai);
        }

        // Filter berdasarkan waktu selesai
        if ($search_waktu_selesai) {
            $query->whereDate('waktu_selesai', '=', $search_waktu_selesai);
        }
        $hasilTests = $query->orderBy('updated_at', 'desc')->get();

        return view('hasil_test', compact('hasilTests'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_equipment_id' => 'required|exists:product_equipments,id',
            'element_tests' => 'required|array|min:1',
            'element_tests.*.nama_element' => 'required|string',
            'element_tests.*.hasil_test' => 'nullable|string',
            'element_tests.*.status' => 'required|in:OK,NOT OK,PROGRESS',
            'element_tests.*.keterangan' => 'nullable|string',
            'status_akhir' => 'required|in:OK,NOT OK,PROGRESS',
            'keterangan_akhir' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Validasi wajib upload foto jika status akhir OK/NOT OK
        if (in_array($request->status_akhir, ['OK', 'NOT OK'])) {
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        DB::beginTransaction();
        try {
            // Selalu buat history baru (insert, bukan update)
            $history = new History();
            $history->product_equipment_id = $request->product_equipment_id;
            $history->user_id = Auth::id();
            $history->waktu_mulai = now();
            $history->status_akhir = $request->status_akhir;
            $history->keterangan_akhir = $request->keterangan_akhir;
            // waktu_selesai hanya diisi jika status akhir OK/NOT OK
            if (in_array($request->status_akhir, ['OK', 'NOT OK'])) {
                $history->waktu_selesai = now();
            }
            $history->save();

            // Simpan element_tests
            $productEquipment = ProductEquipment::find($request->product_equipment_id);
            foreach ($request->element_tests as $et) {
                $keterangan = $et['keterangan'] ?? null;
                if (!$keterangan && in_array($et['status'], ['OK', 'NOT OK'])) {
                    $master = MasterElementTest::where('product_equipment_type_id', $productEquipment->product_equipment_type_id)
                        ->where('nama_element', $et['nama_element'])
                        ->first();
                    if ($master) {
                        if ($et['status'] === 'OK') {
                            $keterangan = $master->keterangan_ok;
                        } elseif ($et['status'] === 'NOT OK') {
                            $keterangan = $master->keterangan_not_ok;
                        }
                    }
                }
                ElementTest::create([
                    'history_id' => $history->id,
                    'nama_element' => $et['nama_element'],
                    'hasil_test' => $et['hasil_test'] ?? null,
                    'status' => $et['status'],
                    'keterangan' => $keterangan,
                ]);
            }

            // Simpan images jika ada
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = $img->store('bukti', 'public');
                    Image::create([
                        'history_id' => $history->id,
                        'path' => $path,
                    ]);
                }
            }
            
            // Update status QC alat
            $productEquipment->status_qc = 'hasil_test';
            $productEquipment->save();

            DB::commit();
            return redirect()->route('hasil-test.index')->with('success', 'Hasil test berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
    public function restore($id)
    {
        // Cari alat berdasarkan ID
        $equipment = ProductEquipment::findOrFail($id);

        // Update status alat ke "Proses QC" (atau sesuai kebutuhan)
        $equipment->status_qc = 'proses_qc';
        $equipment->save();

        // Tambahkan flash message
        return redirect()->route('hasil-test.index')->with('success', 'Alat berhasil direstore ke Proses QC.');
    }   
    
}