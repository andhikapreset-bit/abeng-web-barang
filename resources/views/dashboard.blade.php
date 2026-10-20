<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-slate-800 text-white p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-xl font-bold mb-6 text-blue-400">Logistika</h2>
            <ul class="space-y-2">
                <li><a href="#" class="block py-2 px-4 rounded bg-slate-700 text-white">Dashboard</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-slate-700 text-gray-300">Gudang</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-slate-700 text-gray-300">Data Barang</a></li>
                <li><a href="#" class="block py-2 px-4 rounded hover:bg-slate-700 text-gray-300">Transaksi</a></li>
            </ul>
        </div>
        <div class="text-sm text-gray-400">
            <p>TALL Stack App</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Dashboard Operasional</h1>
            <span class="text-sm font-medium text-gray-600">Admin Gudang</span>
        </header>

        <main class="p-6">
            <!-- Cards Ringkasan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Gudang</p>
                    <h3 class="text-2xl font-bold text-gray-800">3</h3>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Barang</p>
                    <h3 class="text-2xl font-bold text-gray-800">120</h3>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800">12</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Selamat Datang di Sistem Manajemen Logistik</h3>
                <p class="text-gray-600">Gunakan menu di sebelah kiri untuk mengelola gudang, stok barang, serta memantau transaksi barang masuk dan keluar.</p>
            </div>
        </main>
    </div>
</div>