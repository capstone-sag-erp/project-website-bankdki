@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Daftar File Anda</h2>
            <a href="{{ route('files.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Upload Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        @if($files->isEmpty())
            <p>Belum ada file yang diunggah.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diupload Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($files as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $file->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                    <a href="{{ asset('storage/'.$file->file_path) }}" class="text-blue-500 hover:underline" target="_blank">Lihat</a>
                                    <a href="{{ route('files.edit', $file->id) }}" class="text-yellow-500 hover:underline">Edit</a>
                                    <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
