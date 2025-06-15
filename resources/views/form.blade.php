<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMS - Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

  </head>
  <body class="bg-gray-100">
    <div class="flex">
      <!-- Main Content -->
      <div class="flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold">FORM WORKSHOP TEST</h1>
          <div class="flex items-center">
            <span class="mr-4">{{ Auth::user()->name }}</span>
            <i class="fas fa-user-circle text-2xl"></i>
          </div>
        </div>
        <button
        id="btnKembali"
        onclick="window.location.href='{{ route('proses-qc.index') }}'"
        class="bg-gray-300 text-gray-700 px-4 py-2 rounded mb-6"
      >
        Kembali
      </button>

        {{-- <h2 class="text-xl font-bold mb-4">Workshop Tes</h2> --}}
        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-lg font-bold mb-4">DATA PERANGKAT</h3>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-gray-700">Perangkat Alpro</label>
              <input
                type="text"
                class="w-full border border-gray-300 p-2 rounded"
                value="{{ $equipment->productEquipmentType->name?? '-' }}" readonly
              />
            </div>
            <div>
              <label class="block text-gray-700">SN</label>
              <input
                type="text"
                class="w-full border border-gray-300 p-2 rounded"
                value="{{ $equipment->sn ?? '-'  }}" readonly
              />
            </div>
            <div>
              <label class="block text-gray-700">TAGG</label>
              <input
                type="text"
                class="w-full border border-gray-300 p-2 rounded"
                value="{{ $equipment->tagg?? '-'  }}" readonly
              />
            </div>
            <div>
              <label class="block text-gray-700">Merk</label>
              <input
                type="text"
                class="w-full border border-gray-300 p-2 rounded"
                value="{{ $equipment->merk?? '-'  }}" readonly
              />
            </div>

            <div>
              <label class="block text-gray-700">Engineer Tes</label>
              <input
                type="text"
                class="w-full border border-gray-300 p-2 rounded"
                value="{{ auth()->user()->name ?? '-' }}" readonly
              />
            </div>
          </div>
          <div class="bg-white p-6 rounded shadow">
            {{-- Pesan error validasi --}}
            @if ($errors->any())
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
          
            <form method="POST"
            action="{{ isset($history) ? route('hasil-test.update', $history->id) : route('hasil-test.store') }}"
            enctype="multipart/form-data"
            id="formPengujian">
          @csrf
          @if(isset($history))
              @method('PUT')
          @endif
          <input type="hidden" name="product_equipment_id" value="{{ $equipment->id }}">
          <h3 class="text-lg font-bold mb-4">ELEMENT TEST</h3>
          <div class="overflow-x-auto">
              <table class="min-w-full bg-white">
                  <thead>
                      <tr class="w-full bg-gray-800 text-white">
                          <th class="py-2 px-4">NOMOR</th>
                          <th class="py-2 px-4">ELEMENT TEST</th>
                          <th class="py-2 px-4">STATUS</th>
                          <th class="py-2 px-4">HASIL TES</th>
                          <th class="py-2 px-4">KETERANGAN</th>
                      </tr>
                  </thead>
                  <tbody>
                      @php
                            $elementTests = old('element_tests', $elementTests ?? []);
                      @endphp
                      @php
                      $elementTests = old('element_tests', $elementTests ?? []);
                  @endphp
                  @foreach ($elementTests as $index => $element)
                  <tr class="border-b">
                      <td class="py-2 px-4">{{ $index + 1 }}</td>
                      <td class="py-2 px-4">
                        <input type="hidden" name="element_tests[{{ $index }}][nama_element]" value="{{ old('element_tests.' . $index . '.nama_element', $element->nama_element ?? '') }}">
                        {{ old('element_tests.' . $index . '.nama_element', $element->nama_element ?? '') }}
                      </td>
                      <td class="py-2 px-4">
                        <select name="element_tests[{{ $index }}][status]" class="border border-gray-300 p-2 rounded w-full status-select" data-ok="{{ $element->keterangan_ok ?? ($element->keterangan ?? '') }}" data-notok="{{ $element->keterangan_not_ok ?? ($element->keterangan ?? '') }}">
                          <option value="OK" @if(old('element_tests.' . $index . '.status', $element->status ?? 'PROGRESS') == 'OK') selected @endif>OK</option>
                          <option value="NOT OK" @if(old('element_tests.' . $index . '.status', $element->status ?? 'PROGRESS') == 'NOT OK') selected @endif>NOT OK</option>
                          <option value="PROGRESS" @if(old('element_tests.' . $index . '.status', $element->status ?? 'PROGRESS') == 'PROGRESS') selected @endif>PROGRESS</option>
                      </select>
                      </td>
                      <td class="py-2 px-4">
                        <input type="text" name="element_tests[{{ $index }}][hasil_test]" class="border border-gray-300 p-2 rounded w-full" placeholder="Masukkan Hasil Tes" value="{{ old('element_tests.' . $index . '.hasil_test', $element->hasil_test ?? '') }}" />
                      </td>
                      <td class="py-2 px-4">
                        <input type="text" name="element_tests[{{ $index }}][keterangan]" class="border border-gray-300 p-2 rounded w-full keterangan-input" value="{{ old('element_tests.' . $index . '.keterangan', $element->keterangan ?? '') }}" readonly />
                      </td>
                  </tr>
                  @endforeach
                  </tbody>
              </table>
          </div>
          <h3 class="text-lg font-bold mt-6 mb-4">HASIL PENGETESAN KESELURUHAN</h3>
          <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                  <label class="block text-gray-700">STATUS AKHIR</label>
                  <input type="text" id="statusAkhir" name="status_akhir" class="w-full border border-gray-300 p-2 rounded" value="{{ $history->status_akhir ?? 'PROGRESS' }}" readonly />
              </div>
              <div>
                <label class="block text-gray-700">KETERANGAN AKHIR</label>
                <textarea name="keterangan_akhir" class="w-full border border-gray-300 p-2 rounded">{{ old('keterangan_akhir', $history->keterangan_akhir ?? '') }}</textarea>
            </div>
          </div>
          <div class="mb-4" id="fotoBuktiWrapper">
              <label class="block text-gray-700">Foto Bukti Hasil Pengetesan</label>
              <input type="file" name="images[]" id="fotoBukti" class="w-full border border-gray-300 p-2 rounded" multiple>
              <small class="text-red-500 hidden" id="fotoError">Foto bukti wajib diupload jika status akhir OK/NOT OK.</small>
          </div>
          <div class="flex justify-end">
              <button type="submit" id="btnSimpan" class="bg-blue-500 text-white px-4 py-2 rounded">
                  {{ isset($history) ? 'Update' : 'Submit' }}
              </button>
          </div>
      </form>
    <script>   
    
      // Handle tool selection
      document.querySelectorAll(".toolOption").forEach((button) => {
        button.addEventListener("click", function () {
          const toolName = this.textContent.trim();
          alert(`Anda memilih: ${toolName}`);
          document.getElementById("toolModal").classList.add("hidden");
        });
      });
      function toggleDropdown() {
        const menu = document.getElementById("workshopMenu");
        const icon = document.getElementById("arrowIcon");

        menu.classList.toggle("hidden");
        icon.classList.toggle("rotate-180"); // rotasi panah saat dropdown terbuka
      }
      function updateStatusAkhir() {
        let statusList = Array.from(document.querySelectorAll('.status-select')).map(sel => sel.value);
        let statusAkhir = "OK";
        if (statusList.includes("PROGRESS")) {
            statusAkhir = "PROGRESS";
        } else if (statusList.includes("NOT OK")) {
            statusAkhir = "NOT OK";
        }
        document.getElementById("statusAkhir").value = statusAkhir;
    }

      // Jalankan saat halaman dimuat dan setiap kali status berubah
      document.querySelectorAll('.status-select').forEach(function(select) {
          select.addEventListener('change', function() {
              // ... existing keterangan logic ...
              const keteranganInput = this.closest('tr').querySelector('.keterangan-input');
              if(this.value === 'OK') {
                  keteranganInput.value = this.getAttribute('data-ok');
              } else if(this.value === 'NOT OK') {
                  keteranganInput.value = this.getAttribute('data-notok');
              } else {
                  keteranganInput.value = '';
              }
              updateStatusAkhir();
          });
      });
      // Set status akhir saat halaman pertama kali dimuat
      updateStatusAkhir();

      // Validasi Javascript sebelum submit
      document.getElementById('formPengujian').addEventListener('submit', function(e) {
      let statusAkhir = document.getElementById('statusAkhir').value.toLowerCase();
      let fotoInput = document.getElementById('fotoBukti');
      let fotoError = document.getElementById('fotoError');
      let files = fotoInput.files;

      // Jika status akhir OK/NOT OK, foto wajib diupload
      if (statusAkhir === 'OK' || statusAkhir === 'NOT OK') {
          if (!files.length) {
              e.preventDefault();
              fotoError.classList.remove('hidden');
              fotoInput.classList.add('border-red-500');
              fotoInput.scrollIntoView({behavior: "smooth"});
              return false;
          } else {
              fotoError.classList.add('hidden');
              fotoInput.classList.remove('border-red-500');
          }
      } else {
          fotoError.classList.add('hidden');
          fotoInput.classList.remove('border-red-500');
      }
  });

    document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.status-select').forEach(function(select) {
    // Trigger awal agar keterangan terisi sesuai status saat reload
    select.dispatchEvent(new Event('change'));
    select.addEventListener('change', function() {
      const row = select.closest('tr');
      const keteranganInput = row.querySelector('.keterangan-input');
      if (select.value === 'OK') {
        keteranganInput.value = select.getAttribute('data-ok') || '';
      } else if (select.value === 'NOT OK') {
        keteranganInput.value = select.getAttribute('data-notok') || '';
      } else {
        keteranganInput.value = '';
      }
    });
  });
});
        
    </script>
  </body>
</html>
