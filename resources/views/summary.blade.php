<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proses QC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
  </head>
  <body class="bg-gray-100">
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
                href="#"
                class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700  bg-gray-700"
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
 

        <div class="flex items-center mb-4">
          <button class="bg-blue-600 text-white px-4 py-2 rounded mr-2">
            SUMMARY
          </button>
          <button class="bg-white text-gray-700 px-4 py-2 rounded mr-2">
            <a href="{{ route('proses-qc.index') }}">PROSES QC</a>
          </button>
          <button
           
            class="bg-white text-gray-700 px-4 py-2 rounded mr-2"
          >
            <a href="{{ route('hasil-test.index') }}">HASIL TEST</a>
          </button>
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
        </div>

        <!-- Chart Container -->
        <div class="bg-white rounded shadow p-6">
          <canvas id="summaryChart" height="100"></canvas>
        </div>
      </div>
    </div>
     <!-- Chart.js CDN -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script>
       // Isi dropdown tahun (otomatis dari 2022 sampai tahun sekarang)
       const tahunSelect = document.getElementById('tahunSelect');
       const tahunSekarang = new Date().getFullYear();
       for(let t = tahunSekarang; t >= 2022; t--) {
         let opt = document.createElement('option');
         opt.value = t;
         opt.textContent = t;
         tahunSelect.appendChild(opt);
       }
       tahunSelect.value = tahunSekarang;
 
       // Chart.js instance
       let summaryChart = null;
 
       // Fungsi ambil data dan render chart
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
               '#3b82f6', // Total - biru
               '#f59e42', // Proses QC - oranye
               '#22c55e', // OK - hijau
               '#ef4444', // NOT OK - merah
               '#facc15'  // PROGRESS - kuning
             ];
 
             if(summaryChart) summaryChart.destroy();
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
                   title: { display: true, text: `Summary Workshop ${tahun}${bulan ? ' - ' + document.getElementById('bulanSelect').options[document.getElementById('bulanSelect').selectedIndex].text : ''}` }
                 },
                 scales: {
                   y: { beginAtZero: true }
                 }
               }
             });
           });
       }
 
       // Event filter
       document.getElementById('filterBtn').addEventListener('click', loadSummaryChart);
 
       // Export chart ke PNG
       document.getElementById('exportBtn').addEventListener('click', function() {
         if(summaryChart) {
           const link = document.createElement('a');
           link.href = summaryChart.toBase64Image();
           link.download = 'summary_chart.png';
           link.click();
         }
       });
 
       // Load chart pertama kali
       window.onload = loadSummaryChart;
     </script>
    <script>
      // Event listener for workshop buttons
      document.querySelectorAll(".workshopBtn").forEach((button) => {
        button.addEventListener("click", function () {
          window.location.href = "form.html";
        });
      });

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
