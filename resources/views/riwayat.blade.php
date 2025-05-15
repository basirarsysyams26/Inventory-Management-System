<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMS - Riwayat Hasil Workshop Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

  </head>
  <body class="bg-gray-100 font-sans">
    <div class="flex">
      <!-- Sidebar -->
      <div class="w-64 bg-gray-900 text-white h-screen">
        <div class="p-4 text-lg font-bold text-center">
          INVENTORY MANAGEMENT SYSTEM
        </div>
        <nav class="mt-10">
          <!-- Workshop Dropdown -->
          <div class="mt-4">
            <button
              onclick="toggleDropdown()"
              class="flex justify-between items-center w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
            >
              <span>Workshop</span>
              <i
                id="arrowIcon"
                class="fas fa-chevron-down transition-transform duration-200"
              ></i>
            </button>
            <div id="workshopMenu" class="ml-4 hidden transition-all">
              <a
                href="{{ route('summary') }}"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
                >Summary</a
              >
              <a
                href="{{ route('proses-qc.index') }}"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
                >Proses QC</a
              >
              <a
                href="{{ route('hasil-test.index') }}"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
                >Hasil Test</a
              >
            </div>
          </div>

          <!-- Repair (tetap di luar dropdown) -->
          <div class="mt-4">
            <a
              href="#"
              class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
              >Repair</a
            >
          </div>
        </nav>
      </div>
      <!-- Main Content -->
      <div class="flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold">ALPRO MANAGEMENT SYSTEM</h1>
          <div class="flex items-center">
            @auth
              <div class="relative">
                <button id="userDropdownBtn" class="mr-4 flex items-center focus:outline-none">
                  <span class="mr-2">{{ Auth::user()->name }}</span>
                  <i class="fas fa-cog"></i>
                </button>
                <div id="userDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 hidden">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                      Logout
                    </button>
                  </form>
                </div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function () {
                  const btn = document.getElementById('userDropdownBtn');
                  const menu = document.getElementById('userDropdownMenu');
                  btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    menu.classList.toggle('hidden');
                  });
                  document.addEventListener('click', function () {
                    menu.classList.add('hidden');
                  });
                });
              </script>
            @endauth
        </div>
        </div>
      
        
          <div class="bg-white p-6 rounded shadow mt-10 mb-5">
            <h2 class="text-xl font-semibold mb-4">Riwayat Hasil Workshop Test</h2>
            @php
              $equipment = $histories->first() ? $histories->first()->productEquipment : null;
            @endphp
            <div class="mb-4">
              <p>Nama Perangkat : {{ $equipment ? ($equipment->productEquipmentType->name ?? '-') : '-' }}</p>
              <p>SN : {{ $equipment->sn ?? '-' }}</p>
              <p>TAGG : {{ $equipment->tagg ?? '-' }}</p>
            </div>
           
          @php
            $history = $histories->first();
          @endphp
          {{-- @if($history && $history->status_akhir === 'PROGRESS' && !$history->is_canceled)
              <form action="{{ route('riwayat.restore', $history->id) }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded" onclick="return confirm('Yakin ingin membatalkan progress alat ini?')">
                      Batalkan Progress
                  </button>
              </form>
          @endif --}}
          @if($history && $history->status_akhir === 'PROGRESS' && !$history->is_canceled && Auth::user() && Auth::user()->role !== 'Manager')
            <form action="{{ route('riwayat.restore', $history->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded" onclick="return confirm('Yakin ingin membatalkan progress alat ini?')">
                    Batalkan Progress
                </button>
            </form>
        @endif
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
              <thead>
                <tr>
                  <th class="py-2 px-4 bg-blue-600 text-white">NOMOR</th>
                  <th class="py-2 px-4 bg-blue-600 text-white">
                    HASIL KESELURUHAN
                  </th>
                  <th class="py-2 px-4 bg-blue-600 text-white">WAKTU MULAI</th>
                  <th class="py-2 px-4 bg-blue-600 text-white">
                    WAKTU SELESAI
                  </th>
                  <th class="py-2 px-4 bg-blue-600 text-white">STATUS</th>
                  <th class="py-2 px-4 bg-blue-600 text-white">ENGINEER TES</th>
                  <th class="py-2 px-4 bg-blue-600 text-white">AKSI</th>
                </tr>
              </thead>
              <tbody>
                @foreach($histories as $index => $riwayat)
                <tr class="border-b">
                  <td class="py-2 px-4 text-center">{{ $index + 1 }}</td>
                  <td class="py-2 px-4 text-left">{{ $riwayat->keterangan_akhir ?? '-' }}</td>
                  <td class="py-2 px-4 text-center">
                    {{ \Carbon\Carbon::parse($riwayat->waktu_mulai)->format('d M Y H:i') }}
                  </td>
                  <td class="py-2 px-4 text-center">
                    {{ $riwayat->waktu_selesai ? \Carbon\Carbon::parse($riwayat->waktu_selesai)->format('d M Y H:i') : '-' }}
                  </td>
                  <td class="py-2 px-4 text-center">
                    @if($riwayat->status_akhir === 'PROGRESS')
                      <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">PROGRESS</span>
                    @elseif($riwayat->status_akhir === 'NOT OK')
                      <span class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap">NOT OK</span>
                    @else
                      <span class="bg-green-100 text-green-800 px-2 py-1 rounded">OK</span>
                    @endif
                  </td>
                  <td class="py-2 px-4 text-center whitespace-nowrap">{{ $riwayat->user->name ?? '-' }}</td>
                  <td class="py-2 px-4 text-center">
                    @if($riwayat->status_akhir === 'PROGRESS' && Auth::user() && Auth::user()->role !== 'Manager')
                        <a href="{{ route('hasil-test.edit', $riwayat->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    @elseif($riwayat->status_akhir !== 'PROGRESS')
                        <a href="{{ route('surat.show', ['history_id' => $riwayat->id]) }}" class="btnDetail bg-orange-500 text-white px-4 py-2 rounded">Detail</a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-4 flex justify-between items-center">
            <div class="flex items-center">
              <button
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2"
                disabled
              >
                Previous
              </button>
              <span>1</span>
              <button
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded ml-2"
                disabled
              >
                Next
              </button>
            </div>
          </div>
        </div>
        {{-- <button
        >
        <a
        href="{{ route('hasil-test.index') }}"
        class="bg-gray-300 text-gray-700 px-4 py-2 rounded mb-6"
        >Back</a
      >
        </button> --}}
      </div>
    </div>
    <script>
      document
        .getElementById("btnKembali")
        .addEventListener("click", function () {
          window.location.href = "hasil-test.html";
        });

      document.querySelectorAll(".btnDetail").forEach((btn) => {
        btn.addEventListener("click", function () {
          window.location.href = "surat.html";
        });
      });

      function batalkanProgress() {
        const konfirmasi = confirm(
          "Apakah Anda yakin ingin membatalkan progress?"
        );
        if (konfirmasi) {
          alert("Progress berhasil dibatalkan!");
          // Tambahkan logika backend atau redirect di sini
        }
      }

      // TODO: Implementasi fungsi edit progress
      function editProgress() {
        // Implementasi fungsi edit progress
        alert("Fitur edit progress akan segera tersedia");
      }

      // TODO: Implementasi fungsi navigasi halaman
      function previousPage() {
        // Implementasi fungsi previous page
        alert("Fitur previous page akan segera tersedia");
      }

      function nextPage() {
        // Implementasi fungsi next page
        alert("Fitur next page akan segera tersedia");
      }

      // Deteksi apakah ada status "Progress"
      document.addEventListener("DOMContentLoaded", function () {
        const statusElements = document.querySelectorAll("td span");
        let adaProgress = false;

        statusElements.forEach((el) => {
          if (el.textContent.trim() === "PROGRESS") {
            adaProgress = true;
          }
        });

        if (adaProgress) {
          document
            .getElementById("btnBatalkanWrapper")
            .classList.remove("hidden");
        }
      });

      function toggleDropdown() {
        const menu = document.getElementById("workshopMenu");
        const icon = document.getElementById("arrowIcon");

        menu.classList.toggle("hidden");
        icon.classList.toggle("rotate-180"); // rotasi panah saat dropdown terbuka
      }
    </script>
  </body>
</html>
