<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IMS - Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white h-screen">
            <div class="p-4 text-lg font-bold text-center">
                INVENTORY MANAGEMENT SYSTEM
            </div>
            <nav class="mt-10">
                <!-- Workshop Dropdown -->
                <div class="mt-4">
                    <button
                        onclick="toggleDropdown()"
                        class="flex justify-between items-center w-full py-2.5 px-4 rounded transition hover:bg-gray-700"
                    >
                        <span>Workshop</span>
                        <i id="arrowIcon" class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div id="workshopMenu" class="ml-4 hidden">
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-gray-700 bg-gray-700">Summary</a>
                        <a href="{{ route('proses-qc.index') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700">Proses QC</a>
                        <a href="{{ route('hasil-test.index') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700">Hasil Test</a>
                    </div>
                </div>
                <!-- Repair (di luar dropdown) -->
                <div class="mt-4">
                    <a href="#" class="block py-2.5 px-4 rounded hover:bg-gray-700">Repair</a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">ALPRO MANAGEMENT SYSTEM</h1>
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
                    @endauth
                </div>
            </div>

            <div class="flex items-center mb-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded mr-2">SUMMARY</button>
                <a href="{{ route('proses-qc.index') }}" class="bg-white text-gray-700 px-4 py-2 rounded mr-2">PROSES QC</a>
                <a href="{{ route('hasil-test.index') }}" class="bg-white text-gray-700 px-4 py-2 rounded mr-2">HASIL TEST</a>
            </div>

            <div class="flex items-center mb-6">
                <label class="mr-2">Tahun:</label>
                <select id="tahunSelect" class="border rounded px-2 py-1 mr-4"></select>
                <label class="mr-2">Bulan:</label>
                <select id="bulanSelect" class="border rounded px-2 py-1 mr-4">
                    <option value="">Semua</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
                <button id="filterBtn" class="bg-blue-500 text-white px-3 py-1 rounded">Filter</button>
                <button id="exportBtn" class="bg-green-500 text-white px-3 py-1 rounded ml-4"><i class="fas fa-download"></i> Export PNG</button>
                <button id="exportExcelBtn" class="bg-green-500 text-white px-3 py-1 rounded ml-4"><i class="fas fa-file-excel"></i> Export Excel</button>
            </div>
            <div class="mb-4">
              <div class=" text-red-800 rounded px-4 py-3 flex items-center gap-2">*Silahkan klik grafik untuk melihat informasi detail</div>
            </div>

            <!-- Chart Container -->
            <div class="bg-white rounded shadow p-6">
                <canvas id="summaryChart" height="100"></canvas>
                <!-- Modal Detail Summary -->
                <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                  <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
                    <div class="flex justify-between items-center px-4 py-2 border-b">
                      <h2 class="text-xl font-bold" id="modalTitle">Detail Data</h2>
                      <button id="closeModalBtn" class="text-gray-700 hover:text-red-500 text-2xl">&times;</button>
                    </div>
                    <div class="overflow-x-auto max-h-[400px]">
                      <table class="min-w-full text-sm" id="modalTable">
                        <thead>
                          <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">Nama Alat</th>
                            <th class="px-4 py-2 border">SN</th>
                            <th class="px-4 py-2 border">TAGG</th>
                            <th class="px-4 py-2 border">Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Data here -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts Section -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
                // Modal logic
      const detailModal = document.getElementById('detailModal');
      const closeModalBtn = document.getElementById('closeModalBtn');
      closeModalBtn.addEventListener('click', () => detailModal.classList.add('hidden'));
      detailModal.addEventListener('click', (e) => {
        if (e.target === detailModal) detailModal.classList.add('hidden');
      });

      // Show detail in modal
      function showDetailModal(title, data) {
        document.getElementById('modalTitle').innerText = title;
        const tbody = document.querySelector('#modalTable tbody');
        tbody.innerHTML = '';
        if (data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Tidak ada data</td></tr>';
        } else {
          data.forEach(item => {
            tbody.innerHTML += `
              <tr>
                <td class="border px-4 py-2">${item.nama_alat}</td>
                <td class="border px-4 py-2">${item.sn}</td>
                <td class="border px-4 py-2">${item.tagg}</td>
                <td class="border px-4 py-2 text-center">${item.total}</td>
              </tr>`;
          });
        }
        detailModal.classList.remove('hidden');
      }

      // Chart.js: Handle click event
      function getStatusByIndex(idx) {
        const arr = ['Total', 'Proses QC', 'OK', 'NOT OK', 'PROGRESS'];
        const statusMap = {
          'Total': null, // Tidak ada detail
          'Proses QC': 'proses_qc',
          'OK': 'OK',
          'NOT OK': 'NOT OK',
          'PROGRESS': 'PROGRESS'
        };
        return statusMap[arr[idx]];
      }

      document.getElementById('summaryChart').onclick = function(evt) {
        const points = summaryChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, false);
        if (points.length) {
          const idx = points[0].index;
          const tahun = document.getElementById('tahunSelect').value;
          const bulan = document.getElementById('bulanSelect').value;
          const label = summaryChart.data.labels[idx];
          const status = getStatusByIndex(idx);

          if (!status) {
            showDetailModal(label, []); // "Total" tidak ada detail
            return;
          }

          // Fetch detail data
          fetch(`/api/dashboard/summary/detail?tahun=${tahun}&bulan=${bulan}&status=${encodeURIComponent(status)}`)
            .then(res => res.json())
            .then(data => {
              showDetailModal(`Detail "${label}" Tahun ${tahun}${bulan ? ' - ' + document.getElementById('bulanSelect').options[document.getElementById('bulanSelect').selectedIndex].text : ''}`, data);
            })
            .catch(() => {
              showDetailModal('Error', []);
            });
        }
      };

        // Dropdown tahun otomatis
        const tahunSelect = document.getElementById('tahunSelect');
        const tahunSekarang = new Date().getFullYear();
        for (let t = tahunSekarang; t >= 2022; t--) {
            let opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            tahunSelect.appendChild(opt);
        }
        tahunSelect.value = tahunSekarang;

        let summaryChart = null;

        // Ambil data & render Chart
        function loadSummaryChart() {
            const tahun = tahunSelect.value;
            const bulan = document.getElementById('bulanSelect').value;
            fetch(`/api/dashboard/summary?tahun=${tahun}&bulan=${bulan}`)
                .then(res => res.json())
                .then(data => {
                    const labels = ['Total', 'Proses QC', 'OK', 'NOT OK', 'PROGRESS'];
                    const values = [
                        data.total || 0,
                        data.proses_qc || 0,
                        data.ok || 0,
                        data.not_ok || 0,
                        data.progress || 0
                    ];
                    const colors = [
                        '#3b82f6', // Biru
                        '#f59e42', // Oranye
                        '#22c55e', // Hijau
                        '#ef4444', // Merah
                        '#facc15'  // Kuning
                    ];
                    if (summaryChart) summaryChart.destroy();
                    const ctx = document.getElementById('summaryChart').getContext('2d');
                    summaryChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah',
                                data: values,
                                backgroundColor: colors
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                title: { 
                                    display: true,
                                    text: `Summary Workshop ${tahun}${bulan ? ' - ' + document.getElementById('bulanSelect').options[document.getElementById('bulanSelect').selectedIndex].text : ''}`
                                }
                            },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                });
        }

        // Event: Filter data
        document.getElementById('filterBtn').addEventListener('click', loadSummaryChart);

        // Export Chart to PNG
        document.getElementById('exportBtn').addEventListener('click', () => {
            if (summaryChart) {
                const link = document.createElement('a');
                link.href = summaryChart.toBase64Image();
                link.download = 'summary_chart.png';
                link.click();
            }
        });

        // Export to Excel
        document.getElementById('exportExcelBtn').addEventListener('click', () => {
            const tahun = tahunSelect.value;
            const bulan = document.getElementById('bulanSelect').value;
            const url = `/summary/export?tahun=${tahun}&bulan=${bulan}`;
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal export Excel');
                return response.blob();
            })
            .then(blob => {
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'summary_export.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            .catch(err => alert(err.message));
        });

        // Dropdown sidebar (Workshop)
        function toggleDropdown() {
            const menu = document.getElementById('workshopMenu');
            const icon = document.getElementById('arrowIcon');
            menu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // User Dropdown Auth Menu
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('userDropdownBtn');
            const menu = document.getElementById('userDropdownMenu');
            if (btn && menu) {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    menu.classList.toggle('hidden');
                });
                document.addEventListener('click', () => menu.classList.add('hidden'));
            }
            // Load chart pertama kali saat page load
            loadSummaryChart();
        });
    </script>
</body>
</html>
