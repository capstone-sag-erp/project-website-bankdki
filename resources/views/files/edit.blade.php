@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-semibold mb-4">Edit File</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('files.update', $file) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block font-medium">Judul File</label>
            <input type="text" name="title" id="title" value="{{ old('title', $file->title) }}"
                   class="border rounded w-full px-3 py-2 mt-1" required>
        </div>

        <div>
            <label for="category_id" class="block font-medium">Kategori</label>
            <select name="category_id" id="category_id" class="border rounded w-full px-3 py-2 mt-1" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $file->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Simpan Perubahan
            </button>
            <a href="{{ route('files.index') }}" class="text-gray-700 underline ml-4">Batal</a>
        </div>
    </form>
</div>
@endsection
