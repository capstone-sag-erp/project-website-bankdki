@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-semibold mb-4">Upload File Baru</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="title" class="block font-medium">Judul File</label>
            <input type="text" name="title" id="title" class="border rounded w-full px-3 py-2 mt-1" required>
        </div>

        <div>
            <label for="category_id" class="block font-medium">Kategori</label>
            <select name="category_id" id="category_id" class="border rounded w-full px-3 py-2 mt-1" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="file" class="block font-medium">Pilih File</label>
            <input type="file" name="file" id="file" class="border rounded w-full px-3 py-2 mt-1" required>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Upload
            </button>
        </div>
    </form>
</div>
@endsection
