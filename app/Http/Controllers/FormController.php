<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\ElementTest;
use Illuminate\Http\Request;
use App\Models\ProductEquipment;
use App\Models\MasterElementTest;

class FormController extends Controller
{
    public function show($id)
    {
        $equipment = ProductEquipment::with('productEquipmentType')->findOrFail($id);
        $elementTests = MasterElementTest::where('product_equipment_type_id', $equipment->product_equipment_type_id)->get();
        return view('form', compact('equipment', 'elementTests'));
    }
    // public function edit($history_id)
    // {
    //     $history = History::with(['productEquipment', 'elementTests'])->findOrFail($history_id);
    //     $equipment = $history->productEquipment;
    //     $elementTests = $history->elementTests;
    //     return view('form', compact('equipment', 'elementTests', 'history'));
    // }
    public function edit($history_id)
    {
        $history = History::with(['productEquipment', 'elementTests'])->findOrFail($history_id);
        $equipment = $history->productEquipment;
        $productEquipmentTypeId = $equipment->product_equipment_type_id;

        // Ambil master element tests untuk tipe alat terkait
        $masterElements = MasterElementTest::where('product_equipment_type_id', $productEquipmentTypeId)->get()->keyBy('nama_element');

        // Ambil hasil element tests dari history
        $elementTestsDb = $history->elementTests->keyBy('nama_element');

        // Gabungkan data master dan hasil pengujian
        $elementTests = [];
        foreach ($masterElements as $nama_element => $master) {
            $element = [
                'nama_element' => $master->nama_element,
                'keterangan_ok' => $master->keterangan_ok,
                'keterangan_not_ok' => $master->keterangan_not_ok,
                // Data hasil pengujian jika ada
                'hasil_test' => $elementTestsDb[$nama_element]->hasil_test ?? '',
                'status' => $elementTestsDb[$nama_element]->status ?? 'PROGRESS',
                'keterangan' => $elementTestsDb[$nama_element]->keterangan ?? '',
            ];
            $elementTests[] = (object) $element;
        }

        return view('form', compact('equipment', 'elementTests', 'history'));
    }
    
    public function update(Request $request, $history_id)
    {
        // Validasi dasar
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

        \DB::beginTransaction();
        try {
            // Update data history
            $history = History::findOrFail($history_id);
            $history->status_akhir = $request->status_akhir;
            $history->keterangan_akhir = $request->keterangan_akhir;
            // waktu_selesai hanya diisi jika status akhir OK/NOT OK
            if (in_array($request->status_akhir, ['OK', 'NOT OK']) && !$history->waktu_selesai) {
                $history->waktu_selesai = now();
            }
            $history->save();

            // Ambil semua element_tests lama berdasarkan history_id
            $elementTestsDb = ElementTest::where('history_id', $history_id)->get()->keyBy('nama_element');

            foreach ($request->element_tests as $et) {
                // Jika element test sudah ada, update
                if ($elementTestsDb->has($et['nama_element'])) {
                    $elementTest = $elementTestsDb->get($et['nama_element']);
                    $elementTest->hasil_test = $et['hasil_test'] ?? null;
                    $elementTest->status = $et['status'];
                    $elementTest->keterangan = $et['keterangan'] ?? null;
                    $elementTest->save();
                } else {
                    // Jika element test baru (misal master element test bertambah), insert baru
                    \App\Models\ElementTest::create([
                        'history_id' => $history_id,
                        'nama_element' => $et['nama_element'],
                        'hasil_test' => $et['hasil_test'] ?? null,
                        'status' => $et['status'],
                        'keterangan' => $et['keterangan'] ?? null,
                    ]);
                }
            }

            // (Opsional) Simpan images jika ad
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $path = $img->store('bukti', 'public');
                    \App\Models\Image::create([
                        'history_id' => $history_id,
                        'path' => $path,
                    ]);
                }
            }

            \DB::commit();
            return redirect()->route('hasil-test.index')->with('success', 'Data berhasil diupdate!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal mengupdate data: ' . $e->getMessage()]);
        }
    }
}