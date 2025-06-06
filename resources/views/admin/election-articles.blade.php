@extends('layouts.admin')

@section('title', 'Election Articles')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-2">Manage Election Articles</h1>
    <p class="text-gray-600 text-center mb-8">Here you can manage all the articles related to the elections, including debates and candidate articles.</p>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Articles List</h2>
        <button 
            id="new-article-button" 
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200"
        >
            New Article
        </button>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="mb-4"></div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Title</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Author</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Date</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Election</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Visible</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-base">{{ $article->articles->title }}</td>
                        <td class="px-6 py-4 text-base">{{ $article->articles->author }}</td>
                        <td class="px-6 py-4 text-base">{{ \Carbon\Carbon::parse($article->articles->created_at)->format('F j, Y') }}</td>
                        <td class="px-6 py-4 text-base">
                            {{ $article->election ? $article->election->name : 'No Election' }}
                        </td>
                        <td class="px-6 py-4 text-base">
                            {{ $article->show_status ? 'Yes' : 'No' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button 
                                    class="view-article bg-blue-600 text-white px-4 py-1.5 rounded text-base font-medium hover:bg-blue-700 transition-colors"
                                    data-id="{{ $article->id }}"
                                >
                                    View
                                </button>
                                <button 
                                    class="edit-article bg-blue-600 text-white px-4 py-1.5 rounded text-base font-medium hover:bg-blue-700 transition-colors"
                                    data-id="{{ $article->id }}"
                                >
                                    Edit
                                </button>
                                <button 
                                    class="delete-article bg-red-600 text-white px-4 py-1.5 rounded text-base font-medium hover:bg-red-700 transition-colors"
                                    data-id="{{ $article->id }}"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-base">
                            No articles found. Please create your first article.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- View Modal -->
    <div id="view-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[800px] mx-auto mt-16 max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Article Details</h2>
                <button class="close-modal text-gray-500 hover:text-gray-700 text-xl">×</button>
            </div>
            <div class="p-6">
                <h3 id="view-title" class="text-2xl font-bold text-gray-800 mb-2"></h3>
                <div class="text-sm text-gray-600 mb-4">
                    Author: <span id="view-author" class="font-medium"></span><br>
                    Date: <span id="view-date" class="font-medium"></span><br>
                    Election: <span id="view-election" class="font-medium"></span><br>
                    Visible: <span id="view-show-status" class="font-medium"></span><br>
                    Tags: <span id="view-tags" class="font-medium"></span>
                </div>
                
                <div class="mt-6">
                    <div id="view-content" class="prose max-w-none"></div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200">
                <button class="close-modal bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Article Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[900px] mx-auto mt-8 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Edit Article</h2>
                <button class="close-modal text-gray-500 hover:text-gray-700">×</button>
            </div>
            <form id="edit-form" method="POST" action="" class="p-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id" name="id">

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="edit-title" name="title" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Author and Election -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="edit-author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                            <input type="text" id="edit-author" name="author" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            @error('author')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="edit-election" class="block text-sm font-medium text-gray-700 mb-2">Election</label>
                            <input type="text" id="edit-election" name="election_name" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            @error('election_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div id="edit-tags-container" class="flex flex-wrap gap-2 mb-3"></div>
                        <select id="edit-tag-input" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" multiple>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="edit-tags" name="tags">
                        @error('tags')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Show Status -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="edit-show-status" name="show_status" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="edit-show-status" class="text-sm font-medium text-gray-700">Make article visible</label>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="edit-content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                        <div id="edit-content" class="border border-gray-300 rounded-lg min-h-[300px]"></div>
                        <input type="hidden" id="edit-content-input" name="content">
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden input for storing image URLs -->
                    <input type="hidden" id="edit-image-urls" name="featured_image">
                </div>

                <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="close-modal px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[400px] mx-auto mt-16">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Delete Article</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete this article? This action cannot be undone.
                </p>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3">
                        <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Article Modal -->
    <div id="new-article-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[900px] mx-auto mt-8 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">New Article</h2>
                <button class="close-modal text-gray-500 hover:text-gray-700">×</button>
            </div>
            <form id="new-article-form" method="POST" action="{{ route('admin.election-articles.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="new-title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="new-title" name="title" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Author and Election -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="new-author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                            <input type="text" id="new-author" name="author" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('author') }}" required>
                            @error('author')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="new-election" class="block text-sm font-medium text-gray-700 mb-2">Election</label>
                            <input type="text" id="new-election" name="election_name" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('election_name') }}" required>
                            @error('election_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags (Candidates)</label>
                        <div id="new-tags-container" class="flex flex-wrap gap-2 mb-3"></div>
                        <select id="new-tag-input" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" multiple>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="new-tags" name="tags" value="{{ old('tags') }}">
                        @error('tags')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Show Status -->
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="new-show-status" name="show_status" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('show_status') ? 'checked' : '' }}>
                        <label for="new-show-status" class="text-sm font-medium text-gray-700">Make article visible</label>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="new-content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                        <div id="new-content" class="border border-gray-300 rounded-lg min-h-[300px]"></div>
                        <input type="hidden" id="new-content-input" name="content" value="{{ old('content') }}">
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden input for storing image URLs -->
                    <input type="hidden" id="new-image-urls" name="featured_image">
                </div>

                <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="close-modal px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Create Article
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div id="image-upload-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[500px] mx-auto mt-16">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Upload Image</h2>
                <button class="close-modal text-gray-500 hover:text-gray-700">×</button>
            </div>
            <div class="p-6">
                <form id="image-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="image-upload" class="block text-sm font-medium text-gray-700 mb-1">Select Image</label>
                            <input type="file" id="image-upload" name="image" class="w-full border border-gray-300 rounded-lg px-4 py-2" accept="image/*" required>
                        </div>
                        <div id="image-preview-container" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preview</label>
                            <img id="image-preview" class="max-w-full h-auto max-h-[200px] border rounded-lg" src="" alt="Preview">
                        </div>
                        <div id="upload-progress" class="hidden mt-4">
                            <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div id="upload-progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="upload-status" class="text-sm text-gray-600 mt-1">Ready to upload</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="upload-image-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Upload & Insert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    @vite(['resources/js/admin/election-articles.js'])
@endpush