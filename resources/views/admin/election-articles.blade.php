@extends('layouts.admin')

@section('title', 'Manage Election Articles')

@section('content')
<section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Election Articles</h2>
    <p class="text-gray-600 text-lg">Here you can manage all the articles related to the elections, including debates and candidate articles.</p>
</section>

<section class="my-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-3xl font-bold text-gray-800">Articles List</h3>
        <button id="new-article-button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            New Article
        </button>
    </div>




    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif


    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Title</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Author</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Election</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Visible</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                    </tr>
                </thead>
 <!-- First, remove any debugging statements like {{ $articles }} from the top of your file -->

<!-- Keep your table structure but update the body section -->
<tbody>
    @forelse($articles as $article)
        <tr>
            <td class="py-2 px-4 border-b border-gray-300">{{ $article->title }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ $article->author }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ $article->created_at->format('Y-m-d') }}</td>
            <td class="py-2 px-4 border-b border-gray-300">{{ $article->election->name ?? 'N/A' }}</td>
            <td class="py-2 px-4 border-b border-gray-300">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $article->visible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $article->visible ? 'Yes' : 'No' }}
                </span>
            </td>
            <td class="py-2 px-4 border-b border-gray-300">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 view-button mr-2"
                        data-id="{{ $article->id }}"
                        data-title="{{ $article->title }}"
                        data-author="{{ $article->author }}"
                        data-date="{{ $article->created_at->format('Y-m-d') }}"
                        data-election="{{ $article->election->name ?? 'N/A' }}"
                        data-tags="{{ implode(', ', $article->tags ?? []) }}"
                        data-content="{{ $article->content }}">
                    View
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-button mr-2"
                        data-id="{{ $article->id }}">
                    Edit
                </button>
                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button"
                        data-id="{{ $article->id }}">
                    Delete
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="py-4 px-4 text-center border-b border-gray-300">
                No articles found. Please create your first article.
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

</section>

<!-- View Modal -->
<div id="view-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-2xl font-bold mb-4">Article Details</h2>
        <div class="mb-4">
            <h3 class="text-xl font-bold">Title: <span id="article-title"></span></h3>
            <p class="text-gray-600">Author: <span id="article-author"></span></p>
            <p class="text-gray-600">Date: <span id="article-date"></span></p>
            <p class="text-gray-600">Election: <span id="article-election"></span></p>
            <p class="text-gray-600">Tags: <span id="article-tags"></span></p>
        </div>
        <div class="mb-4 prose max-w-none" id="article-content"></div>
        <div class="flex justify-end">
            <button id="close-view" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Close</button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-2xl font-bold mb-4">Edit Article</h2>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id" name="id">
            
            <div class="mb-4">
                <label for="edit-title" class="block text-gray-700">Title:</label>
                <input type="text" id="edit-title" name="title" class="border border-gray-300 p-2 w-full" required />
            </div>
            
            <div class="mb-4">
                <label for="edit-author" class="block text-gray-700">Author:</label>
                <input type="text" id="edit-author" name="author" class="border border-gray-300 p-2 w-full" required />
            </div>
            
            <div class="mb-4">
                <label for="edit-election" class="block text-gray-700">Election:</label>
                <select id="edit-election" name="election_id" class="border border-gray-300 p-2 w-full" required>
                    @foreach($elections as $election)
                        <option value="{{ $election->id }}">{{ $election->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Tags:</label>
                <div id="edit-tag-container" class="flex flex-wrap mb-2"></div>
                <input type="text" id="edit-tag" class="border border-gray-300 p-2 w-full" placeholder="Add a tag and press Enter" />
                <input type="hidden" name="tags" id="edit-tags-input">
            </div>
            
            <div class="mb-4">
                <label for="edit-visible" class="inline-flex items-center">
                    <input type="checkbox" id="edit-visible" name="visible" value="1" class="mr-2">
                    <span class="text-gray-700">Visible</span>
                </label>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Content:</label>
                <div id="edit-content" class="border border-gray-300" style="height: 200px;"></div>
                <input type="hidden" name="content" id="edit-content-input">
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-edit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- New Article Modal -->
<div id="new-article-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-2xl font-bold mb-4">New Article</h2>
        <form id="new-form" method="POST" action="{{ route('admin.election-articles.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="new-title" class="block text-gray-700">Title:</label>
                <input type="text" id="new-title" name="title" class="border border-gray-300 p-2 w-full" required />
            </div>
            
            <div class="mb-4">
                <label for="new-author" class="block text-gray-700">Author:</label>
                <input type="text" id="new-author" name="author" class="border border-gray-300 p-2 w-full" required />
            </div>
            
            <div class="mb-4">
                <label for="new-election" class="block text-gray-700">Election:</label>
                <select id="new-election" name="election_id" class="border border-gray-300 p-2 w-full" required>
                    <option value="">Select Election</option>
                    @foreach($elections as $election)
                        <option value="{{ $election->id }}">{{ $election->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Tags:</label>
                <div id="new-tag-container" class="flex flex-wrap mb-2"></div>
                <input type="text" id="new-tag" class="border border-gray-300 p-2 w-full" placeholder="Add a tag and press Enter" />
                <input type="hidden" name="tags" id="new-tags-input">
            </div>
            
            <div class="mb-4">
                <label for="new-visible" class="inline-flex items-center">
                    <input type="checkbox" id="new-visible" name="visible" value="1" checked class="mr-2">
                    <span class="text-gray-700">Visible</span>
                </label>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Content:</label>
                <div id="new-content" class="border border-gray-300" style="height: 200px;"></div>
                <input type="hidden" name="content" id="new-content-input">
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-new" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create Article</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>
        <p class="mb-4">Are you sure you want to delete this article? This action cannot be undone.</p>
        <div class="flex justify-end">
            <button id="cancel-delete" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">Cancel</button>
            <form id="delete-form" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Quill editors
    const quillOptions = {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                ['link', 'image'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    };

    const editQuill = new Quill('#edit-content', quillOptions);
    const newQuill = new Quill('#new-content', quillOptions);

    // Modal display helper functions
    function showModal(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideModal(modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // View Modal
    const viewButtons = document.querySelectorAll('.view-button');
    const viewModal = document.getElementById('view-modal');
    const closeView = document.getElementById('close-view');

    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            const data = button.dataset;
            document.getElementById('article-title').textContent = data.title;
            document.getElementById('article-author').textContent = data.author;
            document.getElementById('article-date').textContent = data.date;
            document.getElementById('article-election').textContent = data.election;
            document.getElementById('article-tags').textContent = data.tags;
            document.getElementById('article-content').innerHTML = data.content;
            showModal(viewModal);
        });
    });

    closeView.addEventListener('click', () => {
        hideModal(viewModal);
    });

    // Edit Modal
    const editButtons = document.querySelectorAll('.edit-button');
    const editModal = document.getElementById('edit-modal');
    const cancelEdit = document.getElementById('cancel-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const articleId = button.dataset.id;
            try {
                const response = await fetch(`/admin/articles/${articleId}/edit`);
                const article = await response.json();
                
                document.getElementById('edit-id').value = article.id;
                document.getElementById('edit-title').value = article.title;
                document.getElementById('edit-author').value = article.author;
                document.getElementById('edit-election').value = article.election_id;
                document.getElementById('edit-visible').checked = article.visible;
                editQuill.root.innerHTML = article.content;
                
                // Handle tags
                const tagContainer = document.getElementById('edit-tag-container');
                tagContainer.innerHTML = '';
                if (article.tags) {
                    article.tags.forEach(tag => {
                        tagContainer.appendChild(createTagElement(tag));
                    });
                }
                
                showModal(editModal);
            } catch (error) {
                console.error('Error fetching article:', error);
            }
        });
    });

    cancelEdit.addEventListener('click', () => {
        hideModal(editModal);
    });

    // New Article Modal
    const newArticleButton = document.getElementById('new-article-button');
    const newArticleModal = document.getElementById('new-article-modal');
    const cancelNew = document.getElementById('cancel-new');

    newArticleButton.addEventListener('click', () => {
        showModal(newArticleModal);
    });

    cancelNew.addEventListener('click', () => {
        hideModal(newArticleModal);
    });

    // Delete Modal
    const deleteButtons = document.querySelectorAll('.delete-button');
    const deleteModal = document.getElementById('delete-modal');
    const cancelDelete = document.getElementById('cancel-delete');
    const deleteForm = document.getElementById('delete-form');

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const articleId = button.dataset.id;
            deleteForm.action = `/admin/articles/${articleId}`;
            showModal(deleteModal);
        });
    });

    cancelDelete.addEventListener('click', () => {
        hideModal(deleteModal);
    });

    // Tag functionality
    function createTagElement(tag) {
        const tagElement = document.createElement('div');
        tagElement.className = 'bg-blue-600 text-white px-2 py-1 rounded-full mr-2 mb-2 flex items-center';
        tagElement.innerHTML = `
            ${tag}
            <button type="button" class="ml-2 text-white hover:text-red-400">&times;</button>
        `;
        
        tagElement.querySelector('button').addEventListener('click', () => {
            tagElement.remove();
        });
        
        return tagElement;
    }

    ['new-tag', 'edit-tag'].forEach(id => {
        const input = document.getElementById(id);
        const container = document.getElementById(`${id}-container`);
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && input.value.trim()) {
                e.preventDefault();
                container.appendChild(createTagElement(input.value.trim()));
                input.value = '';
            }
        });
    });

    // Form submissions
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('edit-content-input').value = editQuill.root.innerHTML;
        
        const tags = [];
        document.querySelectorAll('#edit-tag-container > div').forEach(tag => {
            tags.push(tag.textContent.trim());
        });
        document.getElementById('edit-tags-input').value = JSON.stringify(tags);
        
        this.submit();
    });

    document.getElementById('new-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('new-content-input').value = newQuill.root.innerHTML;
        
        const tags = [];
        document.querySelectorAll('#new-tag-container > div').forEach(tag => {
            tags.push(tag.textContent.trim());
        });
        document.getElementById('new-tags-input').value = JSON.stringify(tags);
        
        this.submit();
    });
</script>
@endpush