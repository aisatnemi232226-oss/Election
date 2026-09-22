@extends('layouts.admin')

@section('title', 'Election Articles')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-left">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Image</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Author</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Date</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Election</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Visible</th>
                    <th class="px-6 py-3 text-left text-base font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50 article-row" data-id="{{ $article->id }}">
                        <td class="px-6 py-4 text-base">{{ $article->articles->title }}</td>
                        <td class="px-6 py-4 text-base">
                            @php
                                $imgUrl = $article->articles->featured_image_url ?? null;
                            @endphp

                            @if(!empty($imgUrl))
                                <img src="{{ $imgUrl }}" alt="Thumbnail" class="w-16 h-16 object-cover rounded border" onerror="this.onerror=null;this.src='https://via.placeholder.com/64x64?text=No+Image'">
                            @else
                                <span class="text-gray-400 text-sm">No image</span>
                            @endif
                        </td>
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
                                            // remember id for AJAX submit handler
                                            form.dataset.articleId = id;
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 text-base">
                            No articles found. Please create your first article.
                        </td>
                    </tr>
                                    // Submit delete via AJAX to avoid navigating to JSON response page
                                    const deleteForm = document.getElementById('delete-form');
                                    if (deleteForm) {
                                        deleteForm.addEventListener('submit', async function (e) {
                                            e.preventDefault();
                                            const url = this.action;
                                            const articleId = this.dataset.articleId;
                                            try {
                                                const res = await fetchJson(url, { method: 'DELETE' });
                                                // close modal
                                                const modal = document.getElementById('delete-modal');
                                                modal.classList.add('hidden'); modal.classList.remove('flex');

                                                // remove table row for the deleted article
                                                if (articleId) {
                                                    const rowBtn = document.querySelector(`.delete-article[data-id="${articleId}"]`);
                                                    const tr = rowBtn ? rowBtn.closest('tr') : null;
                                                    if (tr) tr.remove();
                                                }

                                                // show success modal then refresh the table (reload page)
                                                const successModal = document.getElementById('delete-success-modal');
                                                if (successModal) {
                                                    successModal.classList.remove('hidden'); successModal.classList.add('flex');
                                                }

                                                // remove the row immediately for snappy UI
                                                if (articleId) {
                                                    const rowBtn = document.querySelector(`.delete-article[data-id="${articleId}"]`);
                                                    const tr = rowBtn ? rowBtn.closest('tr') : null;
                                                    if (tr) tr.remove();
                                                }

                                                // after short delay, hide modal and reload to refresh table state
                                                setTimeout(() => {
                                                    if (successModal) { successModal.classList.add('hidden'); successModal.classList.remove('flex'); }
                                                    // reload table/page to ensure server-side pagination/sorting is consistent
                                                    location.reload();
                                                }, 900);
                                            } catch (err) {
                                                console.error(err);
                                                alert('Unable to delete article. See console for details.');
                                            }
                                        });
                                    }
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
                
                <div class="mt-6 space-y-4">
                    <div id="view-featured-image-wrapper" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Featured Image</h4>
                        <img id="view-featured-image" src="" alt="Featured" class="max-w-full h-auto rounded-lg border" />
                    </div>

                    <div>
                        <div id="view-content" class="prose max-w-none"></div>
                    </div>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags (Candidates)</label>
                        <select id="edit-tags" name="tags[]" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" multiple>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                            @endforeach
                        </select>
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
                        <textarea id="edit-content" name="content" rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-quill="1"></textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image (optional) -->
                    <div>
                        <label for="edit-featured-image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image (optional)</label>
                        <input type="file" id="edit-featured-image" name="featured_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <p class="text-xs text-gray-500 mt-1">Upload a JPG, PNG, or GIF image up to 3MB.</p>
                        @error('featured_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <div id="edit-featured-preview-wrapper" class="mt-3 hidden">
                            <p class="text-xs font-medium text-gray-600 mb-1">Current / New Preview:</p>
                            <img id="edit-featured-preview" src="" alt="Featured preview" class="max-w-full h-32 object-cover rounded border">
                        </div>
                    </div>
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

    <!-- Delete Success Modal -->
    <div id="delete-success-modal" class="fixed inset-0 bg-gray-800 bg-opacity-60 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-[360px] mx-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Delete Successful</h2>
            </div>
            <div class="p-6 text-center">
                <p class="text-gray-700 mb-4">The article was deleted successfully.</p>
                <button id="delete-success-close" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">OK</button>
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
                        <select id="new-tags" name="tags[]" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" multiple>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                            @endforeach
                        </select>
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
                        <textarea id="new-content" name="content" rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" data-quill="1">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image (optional) -->
                    <div>
                        <label for="new-featured-image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image (optional)</label>
                        <input type="file" id="new-featured-image" name="featured_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <p class="text-xs text-gray-500 mt-1">Upload a JPG, PNG, or GIF image up to 3MB.</p>
                        @error('featured_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <div id="new-featured-preview-wrapper" class="mt-3 hidden">
                            <p class="text-xs font-medium text-gray-600 mb-1">Image Preview:</p>
                            <img id="new-featured-preview" src="" alt="Featured preview" class="max-w-full h-32 object-cover rounded border">
                        </div>
                    </div>
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
    @vite(['resources/js/admin/election-articles.js'])

    <script>
        // Base URL for AJAX endpoints (handles apps served from subfolders)
        window.baseUrl = "{{ url('') }}";
        // Fallback: ensure New Article button always opens the modal
        document.addEventListener('DOMContentLoaded', function () {
            var newBtn = document.getElementById('new-article-button');
            var newModal = document.getElementById('new-article-modal');
            if (newBtn && newModal) {
                newBtn.addEventListener('click', function () {
                    newModal.classList.remove('hidden');
                    newModal.classList.add('flex');
                });
            }

            // Fallback: all close-modal buttons hide their parent modal
            var closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var modal = this.closest('.fixed.inset-0');
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            });
        });
    </script>
    <script>
        // Attach handlers for View / Edit / Delete buttons (works without npm build)
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            async function fetchJson(url, opts = {}) {
                opts.headers = Object.assign({ 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, opts.headers || {});
                const res = await fetch(url, opts);
                if (!res.ok) throw new Error('Network error ' + res.status);
                return res.json();
            }

            function setImageWithFallback(imgEl, url) {
                const placeholder = 'https://via.placeholder.com/800x450?text=No+Image';
                if (!imgEl) return;
                imgEl.onerror = null; // clear previous
                if (!url) { imgEl.src = placeholder; return; }
                // try the provided url first
                imgEl.src = url;
                // on error, try replacement strategies
                imgEl.onerror = function() {
                    try {
                        const u = url;
                        // if contains featured-images, try article-images
                        if (u.indexOf('featured-images') !== -1) {
                            const alt = u.replace('featured-images', 'article-images');
                            imgEl.onerror = function() {
                                // final fallback: try basename in article-images
                                const base = encodeURIComponent(u.split('/').pop());
                                imgEl.onerror = null;
                                imgEl.src = '/storage/article-images/' + base;
                            };
                            imgEl.src = alt;
                            return;
                        }
                        // if url contains /storage/, try article-images with basename
                        if (u.indexOf('/storage/') !== -1) {
                            const base = encodeURIComponent(u.split('/').pop());
                            imgEl.onerror = function() { imgEl.onerror = null; imgEl.src = '/storage/article-images/' + base; };
                            imgEl.src = '/storage/article-images/' + base;
                            return;
                        }
                        // last resort: set empty
                        imgEl.onerror = null; imgEl.src = placeholder;
                    } catch (e) { imgEl.onerror = null; imgEl.src = ''; }
                };
            }

            // Prevent action buttons from triggering row click
            document.querySelectorAll('.view-article, .edit-article, .delete-article').forEach(btn => {
                btn.addEventListener('click', function(e) { e.stopPropagation(); });
            });

            // View
            document.querySelectorAll('.view-article').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    try {
                        const url = (window.baseUrl || '') + `/admin/election-articles/${id}`;
                        const data = await fetchJson(url);
                        document.getElementById('view-title').textContent = data.title || '';
                        document.getElementById('view-author').textContent = data.author || '';
                        document.getElementById('view-date').textContent = data.created_at || '';
                        document.getElementById('view-election').textContent = data.election ? data.election.name : 'No Election';
                        document.getElementById('view-show-status').textContent = data.show_status ? 'Yes' : 'No';
                        document.getElementById('view-tags').textContent = data.tags || '';
                        const imgWrapper = document.getElementById('view-featured-image-wrapper');
                        const img = document.getElementById('view-featured-image');
                        const imgUrl = data.featured_image_url || data.featured_image || null;
                        if (imgUrl) {
                            setImageWithFallback(img, imgUrl);
                            imgWrapper.classList.remove('hidden');
                        } else {
                            setImageWithFallback(img, null);
                            imgWrapper.classList.remove('hidden');
                        }
                        document.getElementById('view-content').innerHTML = data.content || '';
                        const modal = document.getElementById('view-modal');
                        modal.classList.remove('hidden'); modal.classList.add('flex');
                    } catch (e) {
                        console.error(e);
                        alert('Unable to load article details.');
                    }
                });

                // Click on row opens view modal
                document.querySelectorAll('tr.article-row').forEach(row => {
                    row.addEventListener('click', async () => {
                        const id = row.getAttribute('data-id');
                        if (!id) return;
                        try {
                            const url = (window.baseUrl || '') + `/admin/election-articles/${id}`;
                            const data = await fetchJson(url);
                            document.getElementById('view-title').textContent = data.title || '';
                            document.getElementById('view-author').textContent = data.author || '';
                            document.getElementById('view-date').textContent = data.created_at || '';
                            document.getElementById('view-election').textContent = data.election ? data.election.name : 'No Election';
                            document.getElementById('view-show-status').textContent = data.show_status ? 'Yes' : 'No';
                            document.getElementById('view-tags').textContent = data.tags || '';
                            const imgWrapper = document.getElementById('view-featured-image-wrapper');
                            const img = document.getElementById('view-featured-image');
                            const imgUrl = data.featured_image_url || data.featured_image || null;
                            if (imgUrl) { setImageWithFallback(img, imgUrl); imgWrapper.classList.remove('hidden'); } else { setImageWithFallback(img, null); imgWrapper.classList.remove('hidden'); }
                            document.getElementById('view-content').innerHTML = data.content || '';
                            const modal = document.getElementById('view-modal');
                            modal.classList.remove('hidden'); modal.classList.add('flex');
                        } catch (e) {
                            console.error(e);
                            alert('Unable to load article details.');
                        }
                    });
                });
            });

            // Edit
            document.querySelectorAll('.edit-article').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    try {
                        const url = (window.baseUrl || '') + `/admin/election-articles/${id}/edit`;
                        const data = await fetchJson(url);
                        const form = document.getElementById('edit-form');
                        form.action = (window.baseUrl || '') + `/admin/election-articles/${id}`;
                        document.getElementById('edit-id').value = data.id || '';
                        document.getElementById('edit-title').value = data.title || '';
                        document.getElementById('edit-author').value = data.author || '';
                        document.getElementById('edit-election').value = data.election_name || '';
                        // tags: if provided as array, select options
                        if (Array.isArray(data.tags)) {
                            const select = document.getElementById('edit-tags');
                            Array.from(select.options).forEach(opt => { opt.selected = data.tags.includes(opt.value) || data.tags.includes(Number(opt.value)); });
                        }
                        document.getElementById('edit-show-status').checked = !!data.show_status;
                        const previewWrapper = document.getElementById('edit-featured-preview-wrapper');
                        const previewImg = document.getElementById('edit-featured-preview');
                        const imgUrl = data.featured_image_url || data.featured_image || null;
                        if (imgUrl) { setImageWithFallback(previewImg, imgUrl); previewWrapper.classList.remove('hidden'); } else { previewImg.src = ''; previewWrapper.classList.add('hidden'); }
                        document.getElementById('edit-content').value = data.content || '';
                        const modal = document.getElementById('edit-modal');
                        modal.classList.remove('hidden'); modal.classList.add('flex');
                    } catch (e) {
                        console.error(e);
                        alert('Unable to load article for editing.');
                    }
                });
            });

            // Delete
            document.querySelectorAll('.delete-article').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const form = document.getElementById('delete-form');
                    form.action = (window.baseUrl || '') + `/admin/election-articles/${id}`;
                    // store id so submit handler knows which row to remove
                    form.dataset.articleId = id;
                    const modal = document.getElementById('delete-modal');
                    modal.classList.remove('hidden'); modal.classList.add('flex');
                });
            });

            // Submit delete via AJAX to avoid navigating to JSON response page
            const deleteForm = document.getElementById('delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const url = this.action;
                    const articleId = this.dataset.articleId;
                    try {
                        const res = await fetchJson(url, { method: 'DELETE' });
                        // close modal
                        const modal = document.getElementById('delete-modal');
                        modal.classList.add('hidden'); modal.classList.remove('flex');

                        // show success modal then refresh the table (reload page)
                        const successModal = document.getElementById('delete-success-modal');
                        if (successModal) {
                            successModal.classList.remove('hidden'); successModal.classList.add('flex');
                        }

                        // remove the row immediately for snappy UI
                        if (articleId) {
                            const rowBtn = document.querySelector(`.delete-article[data-id="${articleId}"]`);
                            const tr = rowBtn ? rowBtn.closest('tr') : null;
                            if (tr) tr.remove();
                        }

                        // after short delay, hide modal and reload to refresh table state
                        setTimeout(() => {
                            if (successModal) { successModal.classList.add('hidden'); successModal.classList.remove('flex'); }
                            location.reload();
                        }, 900);
                    } catch (err) {
                        console.error(err);
                        alert('Unable to delete article. See console for details.');
                    }
                });
            }

            // Close button on success modal: hide and reload immediately
            const successClose = document.getElementById('delete-success-close');
            if (successClose) {
                successClose.addEventListener('click', function () {
                    const sm = document.getElementById('delete-success-modal');
                    if (sm) { sm.classList.add('hidden'); sm.classList.remove('flex'); }
                    location.reload();
                });
            }
        });
    </script>
@endpush