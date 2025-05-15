<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMS - Surat Hasil Pengujian Alat Produksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

  </head>
  <body class="bg-white text-black p-10 font-serif">
    <div class="max-w-3xl mx-auto mb-4 flex justify-end">
      <button
        onclick="downloadPDF()"
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow"
      >
        Unduh PDF
      </button>
    </div>
    <div id="pdf-content" class="max-w-3xl mx-auto border p-8">
      <div class="max-w-3xl mx-auto border p-8">
        <div class="text-center mb-6">
          <h1 class="text-lg font-bold">PT XYZ</h1>
          <h2 class="font-semibold">PENYEDIA LAYANAN SATELIT</h2>
          <p>Jl. Raya xXXx Bogor</p>
          <p>Telp. XxxXXx</p>
        </div>
  
        <hr class="border-black my-4" />
  
        <div class="text-center mb-6">
          <h2 class="text-lg font-bold underline">KETERANGAN HASIL PENGUJIAN</h2>
          <p>Nomor: -</p>
        </div>
  
        <div class="mb-4">
          <p><strong>Nama Alat Produksi</strong> {{ $equipment->productEquipmentType->name ?? '-' }}</p>
          <p class="ml-6">- <strong>SN</strong> : {{ $equipment->sn ?? '-' }}</p>
          <p class="ml-6">- <strong>TAGG</strong> : {{ $equipment->tagg ?? '-' }}</p>
          <p class="ml-6">- <strong>Merk</strong> : {{ $equipment->merk ?? '-' }}</p>
          <p class="ml-6">- <strong>Waktu Mulai Tes</strong> : {{ $history->waktu_mulai ? \Carbon\Carbon::parse($history->waktu_mulai)->format('d M Y H:i') : '-' }}</p>
          <p class="ml-6">- <strong>Waktu Selesai Tes</strong> : {{ $history->waktu_selesai ? \Carbon\Carbon::parse($history->waktu_selesai)->format('d M Y H:i') : '-' }}</p>
          <p class="ml-6">- <strong>Teknisi Penguji</strong> : {{ $history->user->name ?? '-' }}</p>
        </div>
        
        <div class="mb-4">
          <p><strong>Elemen Pengujian</strong> :</p>
          <table class="w-full border border-black mt-2">
            <thead>
              <tr class="bg-gray-200">
                <th class="border border-black p-2">Elemen</th>
                <th class="border border-black p-2">Status</th>
                <th class="border border-black p-2">Hasil</th>
                <th class="border border-black p-2">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($elementTests as $et)
              <tr>
                <td class="border border-black p-2">{{ $et->nama_element }}</td>
                <td class="border border-black p-2">{{ $et->status }}</td>
                <td class="border border-black p-2">{{ $et->hasil_test }}</td>
                <td class="border border-black p-2">{{ $et->keterangan }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
  
        <div class="mb-4">
          <table class="w-full border border-black mt-4">
            <thead>
              <tr class="bg-gray-200">
                <th class="border border-black p-2">Status Akhir</th>
                <th class="border border-black p-2">Keterangan Akhir</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border border-black p-2">{{ $history->status_akhir }}</td>
                <td class="border border-black p-2">{{ $history->keterangan_akhir }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  
      <div class="max-w-3xl mx-auto border p-8 mt-10">
        <h2 class="text-lg font-bold underline mb-4">Lampiran</h2>
        <p class="mb-4">
          Berikut merupakan dokumentasi berupa foto alat dan bukti pengujian:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          @foreach($history->images as $img)
          <div>
            <img
              src="{{ asset('storage/' . $img->path) }}"
              alt="Foto Bukti"
              class="w-full border rounded shadow"
            />
            <p class="mt-2 text-center text-sm">
              Bukti Pengujian
            </p>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    
    <script>
      function downloadPDF() {
        const element = document.getElementById('pdf-content');
        html2pdf()
          .from(element)
          .set({
            margin: 0.5,
            filename: 'Surat_Hasil_Pengujian.pdf',
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
          })
          .save();
      }
    </script>
  </body>
</html>
