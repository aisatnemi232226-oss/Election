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
                        <td class="px-6 py-4 text-base">{{ $article->title }}</td>
                        <td class="px-6 py-4 text-base">{{ $article->author }}</td>
                        <td class="px-6 py-4 text-base">{{ \Carbon\Carbon::parse($article->created_at)->format('F j, Y') }}</td>
                        <td class="px-6 py-4 text-base">
                            @php
                                $election = \App\Models\Election::select('election.*')
                                    ->join('election_articles', 'election.id', '=', 'election_articles.election_id')
                                    ->where('election_articles.articles_id', $article->id)
                                    ->first();
                            @endphp
                            {{ $election ? $election->name : 'No Election' }}
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
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-base">
                            No articles found. Please create your first article.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- View Modal -->
<div id="view-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-[800px] mx-auto mt-16 max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Article Details</h2>
            <button class="close-modal text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        </div>
        <div class="p-6">
            <h3 id="view-title" class="text-2xl font-bold text-gray-800 mb-2"></h3>
            <div class="text-sm text-gray-600 mb-4">
                Author: <span id="view-author" class="font-medium"></span><br>
                Date: <span id="view-date" class="font-medium"></span><br>
                Election: <span id="view-election" class="font-medium"></span><br>
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

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-[800px] mx-auto mt-16 max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Edit Article</h2>
            <button class="close-modal text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="edit-form" method="POST" action="{{ route('admin.election-articles.update', $article->id) }}" class="p-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id" name="id">
            
            <div class="space-y-6">
                <div>
                    <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="edit-title" name="title" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="edit-author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <input type="text" id="edit-author" name="author" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                    </div>
                    <div>
                        <label for="edit-election" class="block text-sm font-medium text-gray-700 mb-1">Election</label>
                        <select id="edit-election" name="election_id" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                            @foreach($elections as $election)
                                <option value="{{ $election->id }}">{{ $election->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                    <div id="edit-tags-container" class="flex flex-wrap gap-2 mb-2"></div>
                    <input type="text" id="edit-tag-input" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Add a tag and press Enter">
                    <input type="hidden" id="edit-tags" name="tags">
                </div>
                
                <div class="flex items-center space-x-2 mb-3">
                    <input type="checkbox" id="edit-show-status" name="show_status" value="1" class="rounded">
                    <label for="edit-show-status" class="text-sm text-gray-700">Visible</label>
                </div>

                <div>
                    <label for="edit-content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <div id="edit-content" class="border border-gray-300 rounded-lg"></div>
                    <input type="hidden" id="edit-content-input" name="content">
                </div>
                
                <!-- Hidden input for storing image URLs -->
                <input type="hidden" id="edit-image-urls" name="image_urls">
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
    <div class="bg-white rounded-lg shadow-lg w-[800px] mx-auto mt-16 max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">New Article</h2>
            <button class="close-modal text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form id="new-article-form" method="POST" action="{{ route('admin.election-articles.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="new-title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="new-title" name="title" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="new-author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <input type="text" id="new-author" name="author" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                    </div>
                    <div>
                        <label for="new-election" class="block text-sm font-medium text-gray-700 mb-1">Election</label>
                        <select id="new-election" name="election_id" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                            @foreach($elections as $election)
                                <option value="{{ $election->id }}">{{ $election->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                    <div id="new-tags-container" class="flex flex-wrap gap-2 mb-2"></div>
                    <input type="text" id="new-tag-input" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Add a tag and press Enter">
                    <input type="hidden" id="new-tags" name="tags">
                </div>
                
                <div class="flex items-center space-x-2 mb-3">
                    <input type="checkbox" id="new-show-status" name="show_status" value="1" class="rounded">
                    <label for="new-show-status" class="text-sm text-gray-700">Visible</label>
                </div>

                <div>
                    <label for="new-content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <div id="new-content" class="border border-gray-300 rounded-lg"></div>
                    <input type="hidden" id="new-content-input" name="content">
                </div>
                
                <!-- Hidden input for storing image URLs -->
                <input type="hidden" id="new-image-urls" name="image_urls">
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
            <button class="close-modal text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label for="image-upload" class="block text-sm font-medium text-gray-700 mb-1">Select Image</label>
                    <input type="file" id="image-upload" class="w-full border border-gray-300 rounded-lg px-4 py-2" accept="image/*">
                </div>
                <div id="image-preview-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preview</label>
                    <img id="image-preview" class="max-w-full h-auto max-h-[200px] border rounded-lg" src="" alt="Preview">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" id="insert-image" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Insert Image
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script>
$(document).ready(function() {
    // Global variable to track current editor
    let currentEditor = null;
    
    // Initialize Quill editors with custom image handler
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        ['link', 'image'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['clean']
    ];

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

    
    const quillStyles = `
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            padding: 8px;
            border-color: #E5E7EB;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #E5E7EB;
            font-size: 0.875rem;
            min-height: 200px;
        }
        .ql-editor {
            padding: 1rem;
        }
        .ql-editor img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
        .upload-progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 4px;
            margin-top: 10px;
            overflow: hidden;
            height: 8px;
        }
        .upload-progress-bar {
            height: 100%;
            background-color: #3b82f6;
            width: 0%;
            transition: width 0.3s ease;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>`;
    $('head').append(quillStyles);
    
    // Custom image handler function
    function imageHandler() {
        console.log('Image handler triggered');
        currentEditor = this.quill; 
        console.log('Current editor set to:', currentEditor.container.id);
        showModal('image-upload-modal');
    }

    // Initialize editors with custom handlers
    const editQuill = new Quill('#edit-content', {
        theme: 'snow',
        modules: { 
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    image: imageHandler
                }
            }
        },
        placeholder: 'Write your article content here...'
    });
    
    const newQuill = new Quill('#new-content', {
        theme: 'snow',
        modules: { 
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    image: imageHandler
                }
            }
        },
        placeholder: 'Write your article content here...'
    });

// Featured image preview handlers
$('#new-featured-image, #edit-featured-image').change(function() {
    const file = this.files[0];
    const previewId = $(this).attr('id').includes('new') ? 'new-featured-image-preview' : 'edit-featured-image-preview';
    const previewImgId = $(this).attr('id').includes('new') ? 'new-featured-image-preview-img' : 'edit-featured-image-preview-img';
    
    if (file) {
        // Validate file size
        if (file.size > 3 * 1024 * 1024) {
            alert('Featured image is too large. Please select an image smaller than 3MB.');
            this.value = '';
            $(`#${previewId}`).addClass('hidden');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $(`#${previewImgId}`).attr('src', e.target.result);
            $(`#${previewId}`).removeClass('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        $(`#${previewId}`).addClass('hidden');
    }
});

    // Image preview handler with optimized preview generation
    $('#image-upload').change(function() {
        const file = this.files[0];
        if (file) {
            // Validate file size before attempting upload
            if (file.size > 3 * 1024 * 1024) { 
                alert('File is too large. Please select an image smaller than 3MB.');
                this.value = '';
                $('#image-preview-container').addClass('hidden');
                return;
            }
            
            // Optimize preview generation
            const reader = new FileReader();
            reader.onload = function(e) {
                // Pre-load the image to get dimensions
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // Limit preview size while maintaining aspect ratio
                    const maxWidth = 300;
                    const maxHeight = 200;
                    let width = img.width;
                    let height = img.height;
                    
                    if (width > maxWidth) {
                        height = (maxWidth / width) * height;
                        width = maxWidth;
                    }
                    
                    if (height > maxHeight) {
                        width = (maxHeight / height) * width;
                        height = maxHeight;
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Use optimized preview for display
                    $('#image-preview').attr('src', canvas.toDataURL('image/jpeg', 0.85));
                    $('#image-preview-container').removeClass('hidden');
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Add progress bar to modal
    $('#image-upload-modal .p-6').append(`
        <div id="upload-progress-container" class="upload-progress-container hidden">
            <div id="upload-progress-bar" class="upload-progress-bar"></div>
        </div>
    `);
    
    // Optimized insert image handler with progress tracking
    $('#insert-image').click(function() {
        const file = $('#image-upload')[0].files[0];
        if (!file) {
            alert('Please select an image first');
            return;
    }

      // Compress image before upload if it's a jpeg/jpg/png
    function processAndUploadImage(imageFile) {
        // Show loading state
        const originalButtonText = $('#insert-image').text();
        $('#insert-image').html('<span class="spinner"></span>Uploading...').prop('disabled', true);
        $('#upload-progress-container').removeClass('hidden');
        $('#upload-progress-bar').css('width', '0%');

        // Create form data for upload
        const formData = new FormData();
        formData.append('image', imageFile);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Output file details for debugging
        console.log('Uploading file:', imageFile.name, imageFile.type, imageFile.size);

       // Upload with progress tracking
        $.ajax({
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = evt.loaded / evt.total * 100;
                        $('#upload-progress-bar').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            url: "{{ route('admin.election-articles.quill.image.upload') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Upload success response:', response);
                
                if (response && response.url) {
                    // Make sure we have a valid currentEditor reference
                    if (!currentEditor) {
                        console.error('No current editor reference');
                        alert('Error: Editor reference is missing');
                        $('#upload-progress-container').addClass('hidden');
                        $('#insert-image').html(originalButtonText).prop('disabled', false);
                        return;
                    }
                    
                    // Insert the image at the current cursor position
                    const range = currentEditor.getSelection();
                    if (!range) {
                        console.warn('No selection range, defaulting to index 0');
                        currentEditor.insertEmbed(0, 'image', response.url);
                    } else {
                        console.log('Inserting image at index:', range.index);
                        currentEditor.insertEmbed(range.index, 'image', response.url);
                    }
                    
                    // Store image URL
                    const urlsInput = currentEditor.container.id.includes('edit') ? '#edit-image-urls' : '#new-image-urls';
                    let urls = $(urlsInput).val() ? JSON.parse($(urlsInput).val()) : [];
                    urls.push(response.url);
                    $(urlsInput).val(JSON.stringify(urls));
                    
                    // Reset and close modal
                    $('#image-upload').val('');
                    $('#image-preview-container').addClass('hidden');
                    $('#upload-progress-container').addClass('hidden');
                    $('#insert-image').html(originalButtonText).prop('disabled', false);
                    hideModal('image-upload-modal');
                } else {
                    console.error("Invalid upload response:", response);
                    alert('Upload failed: Invalid server response');
                    $('#upload-progress-container').addClass('hidden');
                    $('#insert-image').html(originalButtonText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                // Add better error handling to debug issues
                console.error("Upload error:", status, error);
                console.error("Server response:", xhr.responseText);
                
                let errorMessage = 'Image upload failed.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                alert(errorMessage);
                
                $('#upload-progress-container').addClass('hidden');
                $('#insert-image').html(originalButtonText).prop('disabled', false);
            }
        });
    }

        // Check if we can optimize the image before upload
        if (['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            // Compress image if it's larger than 500KB
            if (file.size > 500 * 1024) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Create canvas for compression
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        // Calculate new dimensions (maintain aspect ratio)
                        const maxWidth = 1200; // Max width for article images
                        const maxHeight = 800; // Max height for article images
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > maxWidth) {
                            height = (maxWidth / width) * height;
                            width = maxWidth;
                        }
                        
                        if (height > maxHeight) {
                            width = (maxHeight / height) * width;
                            height = maxHeight;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Convert to compressed blob
                        canvas.toBlob(function(blob) {
                            // Create new file from blob
                            const compressedFile = new File([blob], file.name, {
                                type: file.type,
                                lastModified: new Date().getTime()
                            });
                            
                            // Upload the compressed file
                            processAndUploadImage(compressedFile);
                        }, file.type, 0.85); // Compression quality 0.85
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // File is small enough, upload directly
                processAndUploadImage(file);
            }
        } else {
            // Not an image type we can optimize, upload directly
            processAndUploadImage(file);
        }
    });

    // Modal Management
    function showModal(modalId) {
        const modal = $(`#${modalId}`);
        modal.css('opacity', 0)
            .removeClass('hidden')
            .addClass('flex')
            .animate({ opacity: 1 }, 200);
    }

    function hideModal(modalId) {
        const modal = $(`#${modalId}`);
        modal.animate({ opacity: 0 }, 200, function() {
            $(this).removeClass('flex').addClass('hidden');
        });
    }

    function closeAllModals() {
        $('.fixed').each(function() {
            if ($(this).hasClass('flex')) {
                $(this).animate({ opacity: 0 }, 200, function() {
                    $(this).removeClass('flex').addClass('hidden');
                });
            }
        });
    }

    // Close modal handlers
    $(document).on('click', '.close-modal', closeAllModals);
    $(document).keydown(function(e) {
        if (e.key === "Escape") closeAllModals();
    });
    // New Article
    $('#new-article-button').click(function(e) {
        e.preventDefault();
        $('#new-article-form')[0].reset();
        $('#new-date').val(today);
        newQuill.root.innerHTML = '';
        $('#new-tags-container').empty();
        $('#new-image-urls').val('[]');
        showModal('new-article-modal');
    });

    // View Article
    $(document).on('click', '.view-article', function(e) {
        e.preventDefault();
        const articleId = $(this).data('id');
        
        showModal('view-modal');
        
        // Show loading state
        $('#view-title').html('<div class="animate-pulse h-8 bg-gray-200 rounded w-3/4"></div>');
        $('#view-author, #view-date, #view-election').html('<div class="animate-pulse h-5 bg-gray-200 rounded w-24"></div>');
        $('#view-tags').html('<div class="animate-pulse h-6 bg-gray-200 rounded w-full"></div>');
        $('#view-content').html('<div class="animate-pulse h-32 bg-gray-200 rounded w-full"></div>');
        
        $.get(`/admin/election-articles/${articleId}`, function(article) {
            $('#view-title').text(article.title);
            $('#view-author').text(article.author);
            
            const date = new Date(article.created_at);
            const formattedDate = new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(date);
            $('#view-date').text(formattedDate);
            
            // Safely handle the election relationship
            $('#view-election').text(article.election ? article.election.name : 'No Election');

            // Featured image to view modal if it exists
        if (article.featured_image) {
            if ($('#view-featured-image').length === 0) {
                // Add featured image container if it doesn't exist
                $('#view-content').before(`
                    <div id="view-featured-image" class="mb-6">
                        <img src="${article.featured_image}" class="w-full h-auto rounded-lg" alt="Featured Image">
                    </div>
                `);
            } else {
                $('#view-featured-image img').attr('src', article.featured_image);
                $('#view-featured-image').show();
            }
        } else if ($('#view-featured-image').length > 0) {
            $('#view-featured-image').hide();
        }
            
            // Handle tags
            let tags = [];

            if (Array.isArray(article.tags)) {
                tags = article.tags;
            } else if (typeof article.tags === 'string') {
                try {
                    tags = JSON.parse(article.tags);
                } catch (e) {
                    console.error('Failed to parse tags:', e);
                    tags = [];
                }
            }

            const tagHtml = tags.length > 0
                ? tags.map(tag => `
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium mr-2 mb-2 px-2.5 py-0.5 rounded">
                        ${tag}
                    </span>
                `).join('')
                : '<span class="text-gray-500">No tags</span>';

            $('#view-tags').html(tagHtml);


            $('#view-content').html(article.content || '');
        }).fail(function(error) {
            console.error("Error fetching article:", error);
            $('#view-content').html(`
                <div class="text-center py-8">
                    <p class="text-red-600">Failed to load article details. Please try again.</p>
                </div>
            `);
        });
    });

    // Edit Article
    $(document).on('click', '.edit-article', function(e) {
        e.preventDefault();
        const articleId = $(this).data('id');   
        const form = $('#edit-form');
        form.attr('action', `/admin/election-articles/${articleId}`);
        
        showModal('edit-modal');
        
        $.get(`/admin/election-articles/${articleId}/edit`, function(article) {
            $('#edit-id').val(article.id);
            $('#edit-title').val(article.title);
            $('#edit-author').val(article.author);
            $('#edit-election').val(article.election_id);
            
            // Set content
            editQuill.root.innerHTML = article.content || '';
            
            // Handle tags
            const tagContainer = $('#edit-tags-container');
            tagContainer.empty();
            
            let tags = article.tags || [];
            if (typeof tags === 'string') {
                try {
                    tags = JSON.parse(tags);
                } catch (e) {
                    console.error("Error parsing tags:", e);
                    tags = [];
                }
            }
            
            if (Array.isArray(tags)) {
                tags.forEach(tag => {
                    tagContainer.append(createTagElement(tag));
                });
            }
            
       // Handle featured image preview
       if (article.featured_image) {
            $('#edit-featured-image-preview-img').attr('src', article.featured_image);
            $('#edit-featured-image-preview').removeClass('hidden');
        } else {
            $('#edit-featured-image-preview').addClass('hidden');
        }

            // Handle image URLs
            $('#edit-image-urls').val(article.image_urls || '[]');
            
            updateTagsInput($('#edit-form'));
        }).fail(function(error) {
            console.error("Error fetching article for edit:", error);
            hideModal('edit-modal');
            alert("Failed to load article for editing. Please try again.");
        });
    });

    // Delete Article
    $(document).on('click', '.delete-article', function(e) {
        e.preventDefault();
        const articleId = $(this).data('id');
        $('#delete-form').attr('action', `/admin/election-articles/${articleId}`);
        showModal('delete-modal');
    });

    // Tag Management
    function createTagElement(tag) {
        const tagEl = $(`
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                ${tag}
                <button type="button" class="ml-2 text-blue-600 hover:text-blue-800 focus:outline-none">&times;</button>
            </span>
        `);
        
        tagEl.find('button').on('click', function() {
            $(this).parent().fadeOut(200, function() {
                $(this).remove();
                updateTagsInput($(this).closest('form'));
            });
        });
        
        return tagEl;
    }

    function updateTagsInput(form) {
        const tags = [];
        form.find('.bg-blue-100').each(function() {
            tags.push($(this).text().trim());
        });
        form.find('input[name="tags"]').val(JSON.stringify(tags));
    }

    // Tag input handling
    $('#edit-tag-input, #new-tag-input').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const tag = $(this).val().trim();
            if (tag) {
                const tagContainer = $(this).closest('form').find('[id$=tags-container]');
                const tagElement = createTagElement(tag);
                tagElement.hide();
                tagContainer.append(tagElement);
                tagElement.fadeIn(200);
                updateTagsInput($(this).closest('form'));
                $(this).val('');
            }
        }
    });

    // Form submissions
    $('#edit-form, #new-article-form').on('submit', function(e) {
        e.preventDefault();
        const isEdit = $(this).attr('id') === 'edit-form';
        const quill = isEdit ? editQuill : newQuill;
        
        if (quill.getText().trim().length === 0) {
            alert('Article content cannot be empty.');
            return;
        }
        
        $(isEdit ? '#edit-content-input' : '#new-content-input').val(quill.root.innerHTML);
        updateTagsInput($(this));
        this.submit();
    });
});
</script>
@endpush