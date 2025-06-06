let quillEditors = {
    new: null,
    edit: null,
};

// Utility to get CSRF token
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : null;
}

// Utility to initialize Quill only once per modal
function initQuill(editorId, hiddenInputId, mode) {
    const container = document.querySelector(editorId);
    if (!container) {
        console.error(`Quill container ${editorId} not found`);
        return null;
    }

    if (container.classList.contains('ql-container')) {
        console.log(`Quill editor for ${mode} already exists in DOM`);
        return quillEditors[mode];
    }

    if (quillEditors[mode]) {
        console.log(`Quill editor for ${mode} already initialized in memory`);
        return quillEditors[mode];
    }

    const toolbarOptions = [
        [{ header: [1, 2, false] }],
        ['bold', 'italic', 'underline'],
        ['link', 'image'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['clean']
    ];

    try {
        const quill = new Quill(editorId, {
            modules: { toolbar: toolbarOptions },
            theme: 'snow'
        });

        quill.on('text-change', () => {
            const hiddenInput = document.querySelector(hiddenInputId);
            if (hiddenInput) {
                hiddenInput.value = quill.root.innerHTML;
            }
        });

        quillEditors[mode] = quill;
        console.log(`Quill editor for ${mode} successfully initialized`);
        return quill;
    } catch (error) {
        console.error('Error initializing Quill:', error);
        return null;
    }
}

// Modal toggling
function showModal(id) {
    const modal = document.querySelector(id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function hideModals() {
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    if (quillEditors.new) {
        quillEditors.new.setText('');
        console.log('Cleared new Quill editor content');
    }
    if (quillEditors.edit) {
        quillEditors.edit.setText('');
        console.log('Cleared edit Quill editor content');
    }
}

// Track initialized searchable tags to prevent duplicates
let initializedSearchableTags = new Set();

// Enhanced searchable tag input logic
function initSearchableTags(prefix) {
    const elementKey = `searchable-tags-${prefix}`;
    console.log(`Attempting to initialize searchable tags for ${prefix}`);

    if (initializedSearchableTags.has(elementKey)) {
        console.log(`Searchable tags for ${prefix} already initialized, skipping...`);
        return window[`${prefix}TagManager`] || function() {};
    }

    const container = document.querySelector(`#${prefix}-tags-container`);
    const select = document.querySelector(`#${prefix}-tag-input`);
    const hiddenInput = document.querySelector(`#${prefix}-tags`);
    
    if (!container || !select || !hiddenInput) {
        console.error('Searchable tag elements not found:', { container, select, hiddenInput });
        return function() {};
    }

    const existingWrapper = container.parentNode.querySelector('.search-wrapper');
    if (existingWrapper) {
        existingWrapper.remove();
        console.log(`Removed existing search wrapper for ${prefix}`);
    }

    select.style.display = 'none';
    
    const searchWrapper = document.createElement('div');
    searchWrapper.className = 'relative search-wrapper';
    searchWrapper.innerHTML = `
        <input type="text" 
               id="${prefix}-tag-search" 
               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
               placeholder="Search candidates to tag..." 
               autocomplete="off">
        <div id="${prefix}-tag-dropdown" 
             class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto z-10 hidden">
        </div>
    `;
    
    container.parentNode.insertBefore(searchWrapper, container.nextSibling);
    
    let selectedTags = [];
    const seenCandidateNames = new Set();
    
    const searchInput = document.querySelector(`#${prefix}-tag-search`);
    const dropdown = document.querySelector(`#${prefix}-tag-dropdown`);
    
    searchInput.addEventListener('input', handleSearch);
    searchInput.addEventListener('focus', handleSearch);
    
    function handleSearch() {
        const query = searchInput.value.toLowerCase();
        
        if (query.length === 0) {
            dropdown.classList.add('hidden');
            return;
        }
        
        const filteredCandidates = [];
        const options = select.querySelectorAll('option');
        
        seenCandidateNames.clear();
        console.log(`Processing candidates for ${prefix} search bar, total options: ${options.length}`);
        
        options.forEach(option => {
            const candidateId = option.value;
            const candidateName = option.textContent.trim();
            const candidateNameLower = candidateName.toLowerCase();
            
            if (!candidateId || selectedTags.some(tag => tag.id === candidateId) || seenCandidateNames.has(candidateNameLower)) {
                console.log(`Skipping candidate: ID=${candidateId}, Name=${candidateName} (already seen or selected)`);
                return;
            }
            
            seenCandidateNames.add(candidateNameLower);
            console.log(`Adding candidate: ID=${candidateId}, Name=${candidateName}`);
            
            if (candidateNameLower.includes(query)) {
                filteredCandidates.push({
                    id: candidateId,
                    name: candidateName
                });
            }
        });
        
        console.log(`Filtered candidates for ${prefix}:`, filteredCandidates);
        
        if (filteredCandidates.length > 0) {
            let dropdownHTML = '';
            filteredCandidates.forEach(candidate => {
                dropdownHTML += `
                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" 
                         data-id="${candidate.id}" 
                         data-name="${candidate.name}">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                ${candidate.name.charAt(0).toUpperCase()}
                            </div>
                            <span class="text-gray-800">${candidate.name}</span>
                        </div>
                    </div>
                `;
            });
            
            dropdown.innerHTML = dropdownHTML;
            dropdown.classList.remove('hidden');
        } else {
            dropdown.innerHTML = `
                <div class="px-4 py-3 text-gray-500 text-center">
                    No candidates found matching "${query}"
                </div>
            `;
            dropdown.classList.remove('hidden');
        }
    }
    
    dropdown.addEventListener('click', function(e) {
        const item = e.target.closest('[data-id]');
        if (!item) return;
        
        const candidateId = item.getAttribute('data-id');
        const candidateName = item.getAttribute('data-name');
        
        selectedTags.push({
            id: candidateId,
            name: candidateName
        });
        
        updateTagsDisplay();
        updateHiddenInput();
        
        searchInput.value = '';
        dropdown.classList.add('hidden');
    });
    
    document.addEventListener('click', function(e) {
        if (!searchWrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag') || e.target.closest('.remove-tag')) {
            const button = e.target.classList.contains('remove-tag') ? e.target : e.target.closest('.remove-tag');
            const tagId = button.getAttribute('data-id');
            selectedTags = selectedTags.filter(tag => tag.id !== tagId);
            
            updateTagsDisplay();
            updateHiddenInput();
        }
    });
    
    function updateTagsDisplay() {
        let tagsHTML = '';
        
        selectedTags.forEach(tag => {
            tagsHTML += `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                    <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                        ${tag.name.charAt(0).toUpperCase()}
                    </div>
                    ${tag.name}
                    <button type="button" 
                            class="remove-tag ml-2 text-blue-600 hover:text-blue-800 focus:outline-none" 
                            data-id="${tag.id}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            `;
        });
        
        container.innerHTML = tagsHTML;
    }
    
    function updateHiddenInput() {
        const tagIds = selectedTags.map(tag => tag.id);
        hiddenInput.value = JSON.stringify(tagIds);
    }
    
    initializedSearchableTags.add(elementKey);
    console.log(`Searchable tags for ${prefix} successfully initialized`);
    
    const managerFunction = function setInitialTags(existingTagIds) {
        selectedTags = [];
        
        if (existingTagIds && existingTagIds.length > 0) {
            let tagIds = [];
            
            if (typeof existingTagIds === 'string') {
                try {
                    tagIds = JSON.parse(existingTagIds);
                } catch (e) {
                    tagIds = existingTagIds.split(',').filter(id => id.trim());
                }
            } else if (Array.isArray(existingTagIds)) {
                tagIds = existingTagIds;
            }
            
            tagIds.forEach(tagId => {
                const option = select.querySelector(`option[value="${tagId}"]`);
                if (option && !selectedTags.some(tag => tag.id === tagId)) {
                    selectedTags.push({
                        id: tagId,
                        name: option.textContent
                    });
                }
            });
        }
        
        updateTagsDisplay();
        updateHiddenInput();
    };
    
    window[`${prefix}TagManager`] = managerFunction;
    return managerFunction;
}

// Image upload functionality
let activeUploadEditor = null;
let activeUploadInput = null;

function setupImageUpload() {
    console.log('Setting up image upload handlers');
    
    // Clear any previously attached event listeners to prevent duplicates
    const uploadForm = document.querySelector('#image-upload-form');
    const newUploadForm = uploadForm.cloneNode(true);
    uploadForm.parentNode.replaceChild(newUploadForm, uploadForm);
    
    // Setup the image preview functionality
    const imageUpload = document.querySelector('#image-upload');
    if (imageUpload) {
        imageUpload.value = ''; // Clear any previous selection
        
        imageUpload.addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.querySelector('#image-preview');
            const container = document.querySelector('#image-preview-container');

            if (file && preview && container) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Setup the form submission handler
    newUploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!activeUploadEditor) {
            console.error('No active editor found for image upload');
            return;
        }

        const formData = new FormData(this);
        const progress = document.querySelector('#upload-progress');
        const bar = document.querySelector('#upload-progress-bar');
        const status = document.querySelector('#upload-status');
        const uploadButton = document.querySelector('#upload-image-btn');
        const previewContainer = document.querySelector('#image-preview-container');

        // Show progress and disable the upload button
        if (progress) progress.classList.remove('hidden');
        if (bar) bar.style.width = '0%';
        if (status) status.textContent = 'Uploading...';
        
        if (uploadButton) {
            uploadButton.disabled = true;
            uploadButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
            `;
        }

        try {
            const csrfToken = getCSRFToken();
            const response = await fetch('/admin/election-articles/upload-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.url) {
                // Insert the image into the editor
                const range = activeUploadEditor.getSelection(true);
                activeUploadEditor.insertEmbed(range.index, 'image', data.url);
                
                // Store the image URL in the hidden input
                if (activeUploadInput) {
                    const hiddenInput = document.querySelector(activeUploadInput);
                    if (hiddenInput) {
                        const currentUrls = hiddenInput.value ? hiddenInput.value.split(',') : [];
                        if (!currentUrls.includes(data.url)) {
                            currentUrls.push(data.url);
                            hiddenInput.value = currentUrls.join(',');
                        }
                    }
                }
                
                // Show success message and update UI
                if (status) status.textContent = 'Upload successful!';
                if (bar) bar.style.width = '100%';
                
                // Wait a moment before closing the modal
                setTimeout(() => {
                    hideModal('#image-upload-modal');
                    resetImageUpload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload error:', error);
            if (status) status.textContent = 'Upload failed: ' + error.message;
            if (bar) bar.style.width = '0%';
            
            // Reset upload button
            if (uploadButton) {
                uploadButton.disabled = false;
                uploadButton.innerHTML = 'Upload & Insert';
            }
        }
    });
}

// Hide a specific modal
function hideModal(modalId) {
    const modal = document.querySelector(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Reset image upload form
function resetImageUpload() {
    const uploadForm = document.querySelector('#image-upload-form');
    if (uploadForm) uploadForm.reset();
    
    const imagePreview = document.querySelector('#image-preview-container');
    if (imagePreview) imagePreview.classList.add('hidden');
    
    const progress = document.querySelector('#upload-progress');
    if (progress) progress.classList.add('hidden');
    
    const uploadButton = document.querySelector('#upload-image-btn');
    if (uploadButton) {
        uploadButton.disabled = false;
        uploadButton.innerHTML = 'Upload & Insert';
    }
    
    activeUploadEditor = null;
    activeUploadInput = null;
}

// Track keyboard navigation listeners to prevent duplicates
let keyboardNavigationInitialized = false;

function initKeyboardNavigation() {
    if (keyboardNavigationInitialized) return;
    
    document.addEventListener('keydown', function(e) {
        const activeDropdown = document.querySelector('[id$="-tag-dropdown"]:not(.hidden)');
        if (!activeDropdown) return;
        
        const items = activeDropdown.querySelectorAll('[data-id]');
        let activeItem = activeDropdown.querySelector('[data-id].bg-blue-100');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!activeItem) {
                if (items.length > 0) {
                    items[0].classList.add('bg-blue-100');
                }
            } else {
                activeItem.classList.remove('bg-blue-100');
                const nextItem = activeItem.nextElementSibling;
                if (nextItem && nextItem.hasAttribute('data-id')) {
                    nextItem.classList.add('bg-blue-100');
                } else if (items.length > 0) {
                    items[0].classList.add('bg-blue-100');
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (!activeItem) {
                if (items.length > 0) {
                    items[items.length - 1].classList.add('bg-blue-100');
                }
            } else {
                activeItem.classList.remove('bg-blue-100');
                const prevItem = activeItem.previousElementSibling;
                if (prevItem && prevItem.hasAttribute('data-id')) {
                    prevItem.classList.add('bg-blue-100');
                } else if (items.length > 0) {
                    items[items.length - 1].classList.add('bg-blue-100');
                }
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeItem) {
                activeItem.click();
            }
        } else if (e.key === 'Escape') {
            activeDropdown.classList.add('hidden');
        }
    });

    document.addEventListener('mouseenter', function(e) {
        if (e.target.hasAttribute('data-id')) {
            const siblings = e.target.parentNode.querySelectorAll('[data-id]');
            siblings.forEach(sibling => sibling.classList.remove('bg-blue-100'));
            e.target.classList.add('bg-blue-100');
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        if (e.target.hasAttribute('data-id')) {
            e.target.classList.remove('bg-blue-100');
        }
    }, true);
    
    keyboardNavigationInitialized = true;
}

// Show notification
function showNotification(message, type = 'success') {
    // Remove existing notification if any
    const existingNotification = document.getElementById('notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.id = 'notification';
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 flex items-center transition-all duration-300 transform translate-x-full`;
    
    if (type === 'success') {
        notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
    } else if (type === 'error') {
        notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
    } else if (type === 'warning') {
        notification.classList.add('bg-yellow-100', 'border-l-4', 'border-yellow-500', 'text-yellow-700');
    }
    
    // Add icon based on type
    let icon = '';
    if (type === 'success') {
        icon = `
            <div class="mr-3">
                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        `;
    } else if (type === 'error') {
        icon = `
            <div class="mr-3">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        `;
    } else if (type === 'warning') {
        icon = `
            <div class="mr-3">
                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        `;
    }
    
    notification.innerHTML = `
        ${icon}
        <div>
            <p class="font-medium">${message}</p>
        </div>
        <button class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Add close event
    notification.querySelector('button').addEventListener('click', () => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
    
    // Show notification with animation
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Utility function for making fetch requests with proper error handling
async function fetchWithErrorHandling(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        },
        credentials: 'same-origin',
        ...options
    };

    // Add CSRF token if not present and it's a POST/PUT/DELETE request
    const token = getCSRFToken();
    if (token && ['POST', 'PUT', 'DELETE', 'PATCH'].includes(defaultOptions.method?.toUpperCase())) {
        defaultOptions.headers['X-CSRF-TOKEN'] = token;
    }

    try {
        console.log(`Making ${defaultOptions.method || 'GET'} request to:`, url);
        console.log('Request options:', defaultOptions);

        const response = await fetch(url, defaultOptions);
        
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));

        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            try {
                const errorText = await response.text();
                console.error('Server error response:', errorText);
                if (errorText) {
                    errorMessage += ` - ${errorText}`;
                }
            } catch (textError) {
                console.error('Could not read error response text:', textError);
            }
            throw new Error(errorMessage);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            console.log('Response data:', data);
            return data;
        } else {
            const text = await response.text();
            console.log('Response text:', text);
            return text;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
            throw new Error('Network error: Could not connect to server. Please check your internet connection and try again.');
        }
        throw error;
    }
}

// Document ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize keyboard navigation once
    initKeyboardNavigation();

    // Initialize Quill editors once at page load
    initQuill('#new-content', '#new-content-input', 'new');
    initQuill('#edit-content', '#edit-content-input', 'edit');

    // Modal controls
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', hideModals);
    });

    // Initialize searchable tags
    const newTagManager = initSearchableTags('new');
    const editTagManager = initSearchableTags('edit');

    // New article
    const newArticleButton = document.querySelector('#new-article-button');
    if (newArticleButton) {
        newArticleButton.addEventListener('click', () => {
            showModal('#new-article-modal');
            if (quillEditors.new) {
                quillEditors.new.setText('');
            }
            newTagManager('');
        });
    }

    // New article form submission
    const newArticleForm = document.querySelector('#new-article-form');
    if (newArticleForm) {
        newArticleForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            console.log('New article form submitted');
            
            // Ensure Quill content is captured
            if (quillEditors.new) {
                const contentInput = document.querySelector('#new-content-input');
                if (contentInput) {
                    contentInput.value = quillEditors.new.root.innerHTML;
                }
            }
            
            const form = e.target;
            const formData = new FormData(form);
            
            // Explicitly handle the show_status checkbox
            const showStatusCheckbox = form.querySelector('#new-show-status');
            if (showStatusCheckbox) {
                // Remove any existing show_status value
                formData.delete('show_status');
                // Add the correct value based on checkbox state
                formData.append('show_status', showStatusCheckbox.checked ? '1' : '0');
            }
            
            // Get CSRF token
            const csrfToken = getCSRFToken();
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }
            
            // Add loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.innerHTML : 'Create Article';
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                `;
            }
            
            try {
                console.log('Submitting to URL:', form.action);
                
                // Log form data for debugging
                console.log('Form data being submitted:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    try {
                        const errorText = await response.text();
                        console.error('Server error response:', errorText);
                        if (errorText) {
                            errorMessage += ` - ${errorText}`;
                        }
                    } catch (textError) {
                        console.error('Could not read error response text:', textError);
                    }
                    throw new Error(errorMessage);
                }

                // Check if response is JSON or redirect
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success || data.message) {
                        hideModals();
                        showNotification('Article created successfully', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Creation failed');
                    }
                } else {
                    // If not JSON, assume success and reload
                    hideModals();
                    showNotification('Article created successfully', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
            } catch (error) {
                console.error('Error submitting new article form:', error);
                
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                
                // More specific error messages
                if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                    showNotification('Network error: Could not connect to server. Please check your internet connection and try again.', 'error');
                } else if (error.message.includes('419')) {
                    showNotification('Session expired. Please refresh the page and try again.', 'error');
                } else if (error.message.includes('422')) {
                    showNotification('Validation error. Please check your input and try again.', 'error');
                } else {
                    showNotification(`Error creating article: ${error.message}`, 'error');
                }
            }
        });
    }

    // View article
    document.querySelectorAll('.view-article').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            
            if (!id) {
                console.error('No article ID found on view button');
                showNotification('Error: No article ID found', 'error');
                return;
            }
            
            try {
                console.log(`Fetching article data for view with ID: ${id}`);
                // Use the show endpoint to get the article data
                const article = await fetchWithErrorHandling(`/admin/election-articles/${id}`);
                
                console.log('Article data retrieved for view:', article);
                
                // Populating the view modal with the article data
                const viewTitle = document.querySelector('#view-title');
                const viewAuthor = document.querySelector('#view-author');
                const viewDate = document.querySelector('#view-date');
                const viewElection = document.querySelector('#view-election');
                const viewShowStatus = document.querySelector('#view-show-status');
                const viewTags = document.querySelector('#view-tags');
                const viewContent = document.querySelector('#view-content');
                
                if (viewTitle) viewTitle.textContent = article.title || 'Untitled';
                if (viewAuthor) viewAuthor.textContent = article.author || 'Unknown';
                
                if (viewDate && article.created_at) {
                    // Format the date nicely
                    const date = new Date(article.created_at);
                    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                    viewDate.textContent = date.toLocaleDateString('en-US', options);
                } else if (viewDate) {
                    viewDate.textContent = 'Unknown date';
                }
                
                if (viewElection) {
                    if (article.election && article.election.name) {
                        viewElection.textContent = article.election.name;
                    } else {
                        viewElection.textContent = 'No election assigned';
                    }
                }
                
                if (viewShowStatus) {
                    viewShowStatus.textContent = article.show_status ? 'Yes' : 'No';
                }
                
                if (viewTags) {
                    viewTags.textContent = article.tags || 'No tags';
                }
                
                if (viewContent) {
                    // Safely render HTML content
                    viewContent.innerHTML = article.content || '';
                }
                
                // Show the modal
                showModal('#view-modal');
                
            } catch (error) {
                console.error('Error fetching article for view:', error);
                showNotification(`Error: ${error.message}`, 'error');
            }
        });
    });

    // Edit article
    document.querySelectorAll('.edit-article').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            
            if (!id) {
                console.error('No article ID found on edit button');
                showNotification('Error: No article ID found', 'error');
                return;
            }

            try {
                console.log(`Fetching article with ID: ${id}`);
                const article = await fetchWithErrorHandling(`/admin/election-articles/${id}/edit`);

                console.log('Fetched article data:', article);

                const editForm = document.querySelector('#edit-form');
                const editId = document.querySelector('#edit-id');
                const editTitle = document.querySelector('#edit-title');
                const editAuthor = document.querySelector('#edit-author');
                const editElection = document.querySelector('#edit-election');
                const editShowStatus = document.querySelector('#edit-show-status');
                const editTags = document.querySelector('#edit-tags');
                const editContentInput = document.querySelector('#edit-content-input');
                const editImageUrls = document.querySelector('#edit-image-urls');

                if (editId) editId.value = article.id || '';
                if (editTitle) editTitle.value = article.title || '';
                if (editAuthor) editAuthor.value = article.author || '';
                
                if (editElection) {
                    editElection.value = article.election_name || '';
                }
                
                if (editShowStatus) {
                    console.log('Setting show_status:', article.show_status);
                    editShowStatus.checked = article.show_status === true || article.show_status === 1 || article.show_status === '1';
                }
                
                const tagsForEditing = article.tags_array || article.tags || '';
                
                if (editTags) {
                    if (Array.isArray(tagsForEditing)) {
                        editTags.value = JSON.stringify(tagsForEditing);
                    } else {
                        editTags.value = tagsForEditing;
                    }
                }
                
                editTagManager(tagsForEditing);
                
                if (editContentInput) editContentInput.value = article.content || '';

                // Handle image URLs if available
                if (editImageUrls && article.featured_image) {
                    editImageUrls.value = Array.isArray(article.featured_image) 
                        ? article.featured_image.join(',')
                        : article.featured_image;
                }

                if (editForm) {
                    const formActionId = article.id || id;
                    if (!formActionId) {
                        console.error('Cannot set form action: No valid ID found', { articleId: article.id, buttonId: id });
                        showNotification('Error: Cannot set form action due to missing ID', 'error');
                        return;
                    }
                    editForm.action = `/admin/election-articles/${formActionId}`;
                    console.log(`Set edit form action to: ${editForm.action}`);
                }

                showModal('#edit-modal');
                
                if (quillEditors.edit) {
                    quillEditors.edit.root.innerHTML = article.content || '';
                }
                
            } catch (error) {
                console.error('Error fetching article for edit:', error);
                showNotification(`Error loading article for editing: ${error.message}`, 'error');
            }
        });
    });

    // Edit form submission
    const editForm = document.querySelector('#edit-form');
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            // Explicitly handle the show_status checkbox
            const showStatusCheckbox = form.querySelector('#edit-show-status');
            if (showStatusCheckbox) {
                // Remove any existing show_status value
                formData.delete('show_status');
                // Add the correct value based on checkbox state
                formData.append('show_status', showStatusCheckbox.checked ? '1' : '0');
            }
            
            console.log('Show status value:', showStatusCheckbox?.checked ? '1' : '0');

            // Get CSRF token
            const csrfToken = getCSRFToken();
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }

            // Log form data for debugging
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.innerHTML : 'Save Changes';
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                `;
            }

            try {
                console.log('Submitting to URL:', form.action);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                    try {
                        const errorText = await response.text();
                        console.error('Server error response:', errorText);
                        if (errorText) {
                            errorMessage += ` - ${errorText}`;
                        }
                    } catch (textError) {
                        console.error('Could not read error response text:', textError);
                    }
                    throw new Error(errorMessage);
                }

                // Check if response is JSON or redirect
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success || data.message) {
                        hideModals();
                        showNotification('Article updated successfully', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Update failed');
                    }
                } else {
                    // If not JSON, assume success and reload
                    hideModals();
                    showNotification('Article updated successfully', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
            } catch (error) {
                console.error('Error submitting edit form:', error);
                
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                
                // More specific error messages
                if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                    showNotification('Network error: Could not connect to server. Please check your internet connection and try again.', 'error');
                } else if (error.message.includes('419')) {
                    showNotification('Session expired. Please refresh the page and try again.', 'error');
                } else if (error.message.includes('422')) {
                    showNotification('Validation error. Please check your input and try again.', 'error');
                } else {
                    showNotification(`Error updating article: ${error.message}`, 'error');
                }
            }
        });
    }

    // Delete article
    document.querySelectorAll('.delete-article').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const form = document.querySelector('#delete-form');
            if (form && id) {
                form.action = `/admin/election-articles/${id}`;
                
                // Reset delete button state if it was previously in loading state
                const deleteButton = form.querySelector('button[type="submit"]');
                if (deleteButton) {
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = 'Delete';
                }
                
                showModal('#delete-modal');
            }
        });
    });

    // Delete form submission with enhanced error handling
    const deleteForm = document.querySelector('#delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const articleId = form.action.split('/').pop();
            const formData = new FormData(form);
            formData.append('_method', 'DELETE');
            
            // Get CSRF token
            const csrfToken = getCSRFToken();
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }
            
            // Add loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.innerHTML : 'Delete';
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Deleting...
                `;
            }
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    const responseData = await response.json();
                    throw new Error(responseData.message || `Error ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Hide the modal first
                    hideModals();
                    
                    // Show success notification
                    showNotification('Article deleted successfully', 'success');
                    
                    // Find and remove the row from the table without page reload
                    const articleRow = document.querySelector(`.delete-article[data-id="${articleId}"]`)?.closest('tr');
                    
                    if (articleRow) {
                        // Add fade-out animation
                        articleRow.style.transition = 'opacity 0.5s, transform 0.5s';
                        articleRow.style.opacity = '0';
                        articleRow.style.transform = 'translateX(20px)';
                        
                        // Remove after animation completes
                        setTimeout(() => {
                            articleRow.remove();
                            
                            // Check if table is now empty
                            const tableBody = document.querySelector('table tbody');
                            if (tableBody && tableBody.querySelectorAll('tr:not(.empty-row)').length === 0) {
                                // Add an empty state row
                                const emptyRow = document.createElement('tr');
                                emptyRow.className = 'empty-row';
                                emptyRow.innerHTML = `
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-base">
                                        No articles found. Please create your first article.
                                    </td>
                                `;
                                tableBody.appendChild(emptyRow);
                            }
                        }, 500);
                    }
                } else {
                    throw new Error(result.message || 'Delete operation failed');
                }
                
            } catch (error) {
                console.error('Error deleting article:', error);
                
                // Reset button state
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                
                // Hide the modal
                hideModals();
                
                // Show error notification
                showNotification(`Error deleting article: ${error.message}`, 'error');
            }
        });
    }

    // Image upload button listener
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ql-image')) {
            // Determine which editor triggered the image upload
            const editorContainer = e.target.closest('.ql-editor')?.parentNode?.parentNode;
            
            if (editorContainer) {
                const isNewEditor = editorContainer.id === 'new-content';
                const isEditEditor = editorContainer.id === 'edit-content';
                
                if (isNewEditor || isEditEditor) {
                    const activeEditor = isNewEditor ? quillEditors.new : quillEditors.edit;
                    const hiddenInput = isNewEditor ? '#new-image-urls' : '#edit-image-urls';
                    
                    activeUploadEditor = activeEditor;
                    activeUploadInput = hiddenInput;
                    
                    // Show the modal and setup the upload
                    showModal('#image-upload-modal');
                    setupImageUpload();
                }
            }
        }
    });
});