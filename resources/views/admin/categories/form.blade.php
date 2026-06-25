@extends('admin.layout')

@section('title', $category->exists ? 'Edit Category' : 'Create Category')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-{{ $category->exists ? 'edit' : 'plus-circle' }} mr-2"></i>
            {{ $category->exists ? 'Edit Category' : 'Create New Category' }}
        </h2>

        <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="name">Category Name</label>
                    <input class="form-control w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                           id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required placeholder="Enter category name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="description">Description</label>
                    <textarea class="form-control w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                              id="description" name="description" rows="4" placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-{{ $category->exists ? 'save' : 'plus' }}"></i>
                    {{ $category->exists ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
