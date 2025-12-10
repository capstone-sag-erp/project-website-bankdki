<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Transaksi') }}
            </h2>
            @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('staff'))
            <a href="{{ route('erp.transaksi.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Transaksi
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <form method="GET" action="{{ route('erp.transaksi.index') }}" class="grid grid-cols-5 gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nasabah..." class="rounded-md border-gray-300">
                        <select name="product_type_id" class="rounded-md border-gray-300">
                            <option value="">Semua Produk</option>
                            @foreach($productTypes as $pt)
                            <option value="{{ $pt->id }}" {{ request('product_type_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300">
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex-1">
                                Filter
                            </button>
                            <a href="{{ route('erp.transaksi.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nasabah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('staff'))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $trx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->nasabah->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $trx->productType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $trx->status == 'completed' ? 'bg-green-100 text-green-800' : ($trx->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                                @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('staff'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('erp.transaksi.edit', $trx->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                    @if(auth()->user()->hasRole('manager'))
                                    <form action="{{ route('erp.transaksi.destroy', $trx->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ (auth()->user()->hasRole('manager') || auth()->user()->hasRole('staff')) ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-500">Tidak ada data transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
