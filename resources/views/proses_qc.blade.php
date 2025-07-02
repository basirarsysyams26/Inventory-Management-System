<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>IMS - Proses QC</title>
		<script src="https://cdn.tailwindcss.com"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
		<link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
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
						<button onclick="toggleDropdown()" class="flex justify-between items-center w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
							<span>Workshop</span>
							<i id="arrowIcon" class="fas fa-chevron-down transition-transform duration-200"></i>
						</button>
						<div id="workshopMenu" class="ml-4 hidden transition-all">
							<a href="{{ route('summary')  }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Summary</a>
							<a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 bg-gray-700">Proses QC</a>
							<a href="{{ route('hasil-test.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Hasil Test</a>
						</div>
					</div>

					<!-- Repair (tetap di luar dropdown) -->
					<div class="mt-4">
						<a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Repair</a>
					</div>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="flex-1 p-6">
				<div class="flex justify-between items-center mb-5">
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
				{{--
				<h2 class="text-xl font-semibold mb-4">Workshop Tes</h2>
				--}}

				<div class="flex items-center mb-1">
					<button class="bg-white text-gray-700 px-4 py-2 rounded mr-2">
						<a href="{{ route('summary') }}">SUMMARY</a>
					</button>
					<button class="bg-blue-600 text-white px-4 py-2 rounded mr-2">
						PROSES QC
					</button>
					<button>
						<a href="{{ route('hasil-test.index') }}" class="bg-white text-gray-700 px-4 py-2 rounded mr-2">HASIL TEST</a>
					</button>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full bg-white mt-3">
						<thead>
							<tr class="w-full bg-blue-600 text-white">
								<th class="py-2 px-4 text-center">NOMOR</th>
								<th class="py-2 px-4 text-center">NAMA ALPRO</th>
								<th class="py-2 px-4 text-center">SN</th>
								<th class="py-2 px-4 text-center">TAGG</th>
								<th class="py-2 px-4 text-center">LOKASI</th>
								@if(Auth::user() && Auth::user()->role !== 'Manager')
								<th class="py-2 px-4 text-center">AKSI</th>
								@endif
							</tr>
							<tr class="bg-gray-100">
								<form method="GET" action="{{ route('proses-qc.index') }}" class="contents">
									<td class="py-2 px-4 text-center"></td>
									<td class="py-2 px-4 text-center">
										<input type="text" name="nama_alpro" value="{{ request('nama_alpro') }}" class="w-full border rounded px-2 py-1" placeholder="Search" />
									</td>
									<td class="py-2 px-4 text-center">
										<input type="text" name="sn" value="{{ request('sn') }}" class="w-full border rounded px-2 py-1" placeholder="Search" />
									</td>
									<td class="py-2 px-4 text-center">
										<input type="text" name="tagg" value="{{ request('tagg') }}" class="w-full border rounded px-2 py-1" placeholder="Search" />
									</td>
									<td class="py-2 px-4 text-center">
										<input type="text" name="lokasi" value="{{ request('lokasi') }}" class="w-full border rounded px-2 py-1" placeholder="Search" />
									</td>
									@if(Auth::user() && Auth::user()->role !== 'Manager')
									<td class="py-2 px-4 text-center">
										<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">Cari</button>
									</td>
									@endif @if(Auth::user() && Auth::user()->role === 'Manager')
									<td colspan="1" class="py-2 px-4 text-center">
										<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">Cari</button>
									</td>
									@endif
								</form>
							</tr>
						</thead>
						<tbody>
							@foreach ($equipments as $index => $equipment)
							<tr class="border-b">
								<td class="py-2 px-4 text-center">{{ $index + 1 }}</td>
								<td class="py-2 px-4 text-center">{{ $equipment->productEquipmentType->name ?? '-' }}</td>
								<td class="py-2 px-4 text-center">{{ $equipment->sn }}</td>
								<td class="py-2 px-4 text-center">{{ $equipment->tagg }}</td>
								<td class="py-2 px-4 text-center">{{ $equipment->location->nama ?? '-' }}</td>
								@if(Auth::user() && Auth::user()->role !== 'Manager')
								<td class="py-2 px-4 text-center">
									{{--
									<a href="{{ url('form/'. $equipment->id) }}" class="workshopBtn bg-green-500 text-white px-4 py-2 rounded">
										+ Workshop Test
									</a>
									--}}
									<form action="{{ route('proses-qc.workshop-test', $equipment->id) }}" method="POST" style="display:inline">
										@csrf
										<button type="submit" class="workshopBtn bg-green-500 text-white px-4 py-2 rounded">
											+ Workshop Test
										</button>
									</form>
								</td>
								@endif
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
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
	</body>
</html>
