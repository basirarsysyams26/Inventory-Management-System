
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMS - Hasil Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

  </head>
  <body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
      <!-- Sidebar -->
      <div class="w-64 bg-gray-900 text-white min-h-screen">
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
                href="{{ route('summary')  }}"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
                >Summary</a
              >
              <a
                href="{{ route('proses-qc.index') }}"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"
                >Proses QC</a
              >
              <a
                href="#"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 bg-gray-700"
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
        <div class="flex justify-between items-center mb-2">
          <h1 class="text-2xl font-bold">ALPRO MANAGEMENT SYSTEM</h1>
          {{-- <div class="flex items-center">
            <span class="mr-4">USER TEKNISI 1</span>
            <i class="fas fa-cog"></i>
          </div> --}}
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

        <div class="mt-6">
          <div class="flex mt-4">
            <button class="bg-white text-gray-700 px-4 py-2 rounded mr-2">
              <a href="{{ route('summary') }}">SUMMARY</a>
            </button>
            <button
            >
            <a
            href="{{ route('proses-qc.index') }}"
            class="bg-white text-gray-700 px-4 py-2 rounded mr-2"
            >PROSES QC</a
          >
            </button>
            <button
              onclick="window.location.href='hasil-test.html'"
              class="bg-blue-600 text-white px-4 py-2 rounded mr-2"
            >
              HASIL TEST
            </button>
          </div>
          @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 mt-2 px-2 py-3 rounded  mb-4" role="alert">
              <strong class="font-bold">Sukses!</strong>
              <span class="block sm:inline">{{ session('success') }}</span>
            </div>
          @endif
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white mt-3">
              <thead>
                <tr class="w-full bg-blue-600 text-white">
                  <th class="py-2 px-4 text-center">NOMOR</th>
                  <th class="py-2 px-4 text-center">NAMA ALPRO</th>
                  <th class="py-2 px-4 text-center">SN</th>
                  <th class="py-2 px-4 text-center">TAGG</th>
                  <th class="py-2 px-4 text-center">WAKTU MULAI</th>
                  <th class="py-2 px-4 text-center">WAKTU SELESAI</th>
                  <th class="py-2 px-4 text-center">STATUS</th>
                  <th class="py-2 px-4 text-center">KETERANGAN</th>
                  <th class="py-2 px-4 text-center">AKSI</th>
                </tr>
                <form action="{{ route('hasil-test.index') }}" method="GET">
                  <tr class=" bg-gray-100">
                    <td class="py-2 px-4 text-center"></td>
                    <td class="py-2 px-4 text-center">
                      <input
                        type="text"
                        name="search_nama_alpro"
                        value="{{ request('search_nama_alpro') }}"
                        class="w-full bg-white text-black px-2 py-1 rounded"
                        placeholder="Search"
                      />
                    </td>
                    <td class="py-2 px-4 text-center">
                      <input
                        type="text"
                        name="search_sn"
                        value="{{ request('search_sn') }}"
                        class="w-full bg-white text-black px-2 py-1 rounded"
                        placeholder="Search"
                      />
                    </td>
                    <td class="py-2 px-4 text-center">
                      <input
                        type="text"
                        name="search_tagg"
                        value="{{ request('search_tagg') }}"
                        class="w-full bg-white text-black px-2 py-1 rounded"
                        placeholder="Search"
                      />
                    </td>
                    <td class="py-2 px-4 text-center">
                      <input
                        type="date"
                        name="search_waktu_mulai"
                        value="{{ request('search_waktu_mulai') }}"
                        class="w-full bg-white text-black px-2 py-1 rounded"
                        placeholder="Search"
                      />
                    </td>
                    <td class="py-2 px-4 text-center">
                      <input
                        type="date"
                        name="search_waktu_selesai"
                        value="{{ request('search_waktu_selesai') }}"
                        class="w-full bg-white text-black px-2 py-1 rounded"
                        placeholder="Search"
                      />
                    </td>
                    <td class="py-2 px-4"></td>
                    <td class="py-2 px-4"></td>
                    <td class="py-2 px-4 text-center">
                      <div class="flex space-x-1 justify-center">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">
                          <i class="fas fa-search"></i> 
                        </button>
                        <a href="{{ route('hasil-test.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded">
                          <i class="fas fa-sync-alt"></i> 
                        </a>
                      </div>
                    </td>
                  </tr>
                </form>
              </thead>  
              <tbody>
                  @foreach($hasilTests as $index => $history)
                  <tr class="border-b">
                      <td class="py-2 px-4 text-center">{{ $index + 1 }}</td>
                      <td class="py-2 px-4 text-center">{{ $history->productEquipment->productEquipmentType->name ?? '-' }}</td>
                      <td class="py-2 px-4 text-center">{{ $history->productEquipment->sn ?? '-' }}</td>
                      <td class="py-2 px-4 text-center">{{ $history->productEquipment->tagg ?? '-' }}</td>
                      {{-- <td class="py-2 px-4 text-center">
                          {{ $history->waktu_mulai ? \Carbon\Carbon::parse($history->waktu_mulai)->format('d M Y H:i') : '-' }}
                      </td>
                      <td class="py-2 px-4 text-center">
                          {{ $history->waktu_selesai ? \Carbon\Carbon::parse($history->waktu_selesai)->format('d M Y H:i') : '-' }}
                      </td> --}}
                      <td class="py-2 px-4 text-center">
                        {{ \Carbon\Carbon::parse($history->waktu_mulai)->timezone(config('app.timezone'))->format('d-m-Y H:i:s') }}
                    </td>
                    <td class="py-2 px-4 text-center">
                        {{ $history->waktu_selesai ? \Carbon\Carbon::parse($history->waktu_selesai)->timezone(config('app.timezone'))->format('d-m-Y H:i:s') : '-' }}
                    </td>
                      <td class="py-2 px-4 text-center">
                         @if ($history->status_akhir === 'OK')
                           <span class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap"
                           >
                              {{ $history->status_akhir  }}
                           </span>
                         @elseif ($history->status_akhir === 'NOT OK')
                           <span class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap"
                           >
                              {{ $history->status_akhir  }}
                           </span>
                         @else
                           <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded whitespace-nowrap"
                           >
                              {{ $history->status_akhir  }}
                           </span>
                         @endif
                      </td>
                      <td class="py-2 px-4 text-center">{{ $history->keterangan_akhir ?? '' }}</td>
                      <td class="py-2 px-4 text-center flex justify-center gap-2">
                        <a href="{{ route('riwayat.index', ['sn' => $history->productEquipment->sn, 'tagg' => $history->productEquipment->tagg]) }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition duration-200">Riwayat</a>
                        @auth
                          @if (
                            Auth::user()->role === 'Teknisi' &&
                            ($history->status_akhir === 'OK' || $history->status_akhir === 'NOT OK')
                          )
                            <form action="{{ route('hasil-test.restore', $history->productEquipment->id) }}" method="POST" style="display:inline-block;">
                              @csrf
                              <button type="submit" onclick="return confirm('Apakah Anda yakin ingin me-restore alat ini ke Proses QC?')" class="inline-flex items-center bg-yellow-500 hover:bg-yellow-700 text-white font-semibold px-4 py-2 rounded shadow transition duration-200 ">
                                <i class="fas fa-undo mr-1"></i> Restore
                              </button>
                            </form>
                          @endif
                        @endauth
                      </td>
                  </tr>
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
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
    </script>
  </body>
</html>
