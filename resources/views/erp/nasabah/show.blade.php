<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Nasabah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Info Nasabah -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold">Informasi Nasabah</h3>
                        @if(auth()->user()->hasRole('manager'))
                        <a href="{{ route('erp.nasabah.edit', $nasabah->id_nasabah) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama</p>
                            <p class="font-semibold">{{ $nasabah->nama }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">No KTP</p>
                            <p class="font-semibold">{{ $nasabah->no_ktp }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold">{{ $nasabah->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">No Telepon</p>
                            <p class="font-semibold">{{ $nasabah->no_telepon ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Alamat</p>
                            <p class="font-semibold">{{ $nasabah->alamat ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Daftar</p>
                            <p class="font-semibold">{{ $nasabah->tanggal_daftar->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Transaksi</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($nasabah->transactions as $trx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->productType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $trx->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('erp.nasabah.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
