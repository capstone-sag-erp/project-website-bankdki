<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Nasabah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('erp.nasabah.update', $nasabah->id_nasabah) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                            <input type="text" name="nama" value="{{ old('nama', $nasabah->nama) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No KTP</label>
                            <input type="text" name="no_ktp" value="{{ old('no_ktp', $nasabah->no_ktp) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('no_ktp')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                            <textarea name="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('alamat', $nasabah->alamat) }}</textarea>
                            @error('alamat')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $nasabah->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No Telepon</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon', $nasabah->no_telepon) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('no_telepon')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Daftar</label>
                            <input type="date" name="tanggal_daftar" value="{{ old('tanggal_daftar', $nasabah->tanggal_daftar->format('Y-m-d')) }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('tanggal_daftar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                            <a href="{{ route('erp.nasabah.show', $nasabah->id_nasabah) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
