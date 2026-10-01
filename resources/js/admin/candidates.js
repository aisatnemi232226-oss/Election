document.addEventListener('DOMContentLoaded', () => {
    // ===== MODULE DEFINITIONS =====
    const isArchivedView = new URLSearchParams(window.location.search).get('archived') === '1';
    const initialParams = new URLSearchParams(window.location.search);

    const ModalController = {
        modals: {
            view: document.getElementById('candidate-modal'),
            edit: document.getElementById('edit-modal'),
            confirmation: document.getElementById('confirmation-modal'),
            newCandidate: document.getElementById('new-candidate-modal'),
            error: document.getElementById('error-modal')
        },
        show(modalName) {
            if (this.modals[modalName]) {
                this.modals[modalName].classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        },
        hide(modalName) {
            if (this.modals[modalName]) {
                this.modals[modalName].classList.add('hidden');
                if (!Object.values(this.modals).some(modal => !modal.classList.contains('hidden'))) {
                    document.body.style.overflow = '';
                }
            }
        },
        hideAll() {
            Object.keys(this.modals).forEach(key => {
                if (this.modals[key]) {
                    this.modals[key].classList.add('hidden');
                }
            });
            document.body.style.overflow = '';
        },
        init() {
            document.addEventListener('click', e => {
                if (e.target.classList.contains('modal-backdrop')) {
                    this.hideAll();
                }
            });
            
            document.getElementById('close-modal')?.addEventListener('click', () => this.hide('view'));
            document.getElementById('cancel-edit')?.addEventListener('click', () => this.hide('edit'));
            document.getElementById('cancel-delete')?.addEventListener('click', () => this.hide('confirmation'));
            document.getElementById('cancel-new-candidate')?.addEventListener('click', () => {
                this.hide('newCandidate');
                FormController.resetForm('new-candidate-form');
            });
            document.getElementById('close-error')?.addEventListener('click', () => this.hide('error'));
        }
    };

    // Initialize search box safely
    try {
        if (typeof SearchBox !== 'undefined') {
            new SearchBox({
                searchInputId: 'candidates-search',
                onSearch: (query) => {
                    console.log('Search query:', query);
                    PaginationController.filters.search = query;
                    PaginationController.syncFilterQuery();
                    PaginationController.loadCandidates(1);
                }
            });
        }
    } catch (error) {
        console.error('Failed to initialize SearchBox:', error.message);
    }

    /** 
     * Pagination Controller - Handles Pages for candidates list 
     */
    const PaginationController = {
        currentPage: 1,
        filters: {
            search: initialParams.get('search') || '',
            course: initialParams.get('course') || '',
            party: initialParams.get('party') || '',
            position: initialParams.get('position') || '',
            name: '',
            status: initialParams.get('status') || '',
            pendingPage: null,
            isLoading: false
        },
        setupStatusFilters() {
            const container = document.getElementById('candidate-status-filters');
            if (!container) return;

            container.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-status-filter]');
                if (!btn) return;
                const status = btn.getAttribute('data-status-filter') || '';
                this.filters.status = status;
                this.syncFilterQuery();
                this.loadCandidates(1);
            });
        },
        setupColumnFilters() {
            document.querySelectorAll('[data-candidate-filter]').forEach((select) => {
                select.addEventListener('change', () => {
                    const field = select.getAttribute('data-candidate-filter');
                    if (!['course', 'party', 'position'].includes(field)) return;
                    this.filters[field] = select.value;
                    this.syncFilterQuery();
                    this.loadCandidates(1);
                });
            });
        },
        syncFilterQuery() {
            const params = new URLSearchParams(window.location.search);
            ['course', 'party', 'position', 'status'].forEach((field) => {
                if (this.filters[field]) {
                    params.set(field, this.filters[field]);
                } else {
                    params.delete(field);
                }
            });
            if (this.filters.search) {
                params.set('search', this.filters.search);
            } else {
                params.delete('search');
            }
            params.delete('sort');
            params.delete('direction');
            params.delete('page');
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setupPaginationLinks() {
            // Handle pagination clicks on both top and bottom containers
            document.addEventListener('click', (e) => {
                const link = e.target.closest('.pagination a, #pagination-container-top a, #pagination-container a');
                if (link) {
                    e.preventDefault();
                    const page = new URL(link.href).searchParams.get('page') || 1;
                    this.loadCandidates(parseInt(page));
                }
            });
        },
        updateResultsInfo(metadata) {
            const resultsInfo = document.getElementById('results-info');
            if (resultsInfo && metadata) {
                resultsInfo.textContent = `Showing ${metadata.showing_from || 1} to ${metadata.showing_to || metadata.total} of ${metadata.total} results`;
            }
        },
        loadCandidates(page = 1) {
            if (this.filters.isLoading) {
                this.filters.pendingPage = page;
                return;
            }
            
            this.filters.isLoading = true;
            this.currentPage = page;
            
            const tableBody = document.querySelector('#candidates-table-body');
            const paginationContainerTop = document.querySelector('#pagination-container-top');
            const paginationContainer = document.querySelector('#pagination-container');
            
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Loading...</td></tr>';
            }
            
            const url = `/admin/candidates/paginateCandidates?page=${page}` +
                `&search=${encodeURIComponent(this.filters.search)}` +
                `&course=${encodeURIComponent(this.filters.course)}` +
                `&party=${encodeURIComponent(this.filters.party)}` +
                `&position=${encodeURIComponent(this.filters.position)}` +
                `&name=${encodeURIComponent(this.filters.name)}` +
                `&status=${encodeURIComponent(this.filters.status)}` +
                `&archived=${isArchivedView ? 1 : 0}`;
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (!data.data || !data.data.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No candidates found</td></tr>';
                    } else {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        tableBody.innerHTML = data.data.map(candidate => `
                            <tr>
                                <td class="py-2 px-4 border-b">
                                    <img 
                                        src="${candidate.profile_image_url || '/images/default-profile.jpg'}"
                                        alt="${candidate.name}"
                                        class="w-10 h-10 object-cover rounded-full"
                                        onerror="this.onerror=null; this.src='/images/default-profile.jpg';"
                                    >
                                </td>
                                <td class="py-2 px-4 border-b">${candidate.name}</td>
                                <td class="py-2 px-4 border-b">${candidate.course?.name || 'N/A'}</td>
                                <td class="py-2 px-4 border-b">${candidate.party ? candidate.party.name : 'No Party'}</td>
                                <td class="py-2 px-4 border-b">${candidate.position?.name || 'N/A'}</td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span class="px-2 py-1 text-xs rounded-full ${candidate.status === 'Active' ? 'bg-green-100 text-green-800' : (candidate.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')}">
                                            ${candidate.status}
                                        </span>
                                        ${isArchivedView ? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Archived</span>' : ''}
                                    </div>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex gap-2">
                                        ${!isArchivedView && candidate.status === 'Pending' ? `
                                            <form method="POST" action="/admin/candidates/${candidate.id}/approve" class="inline">
                                                <input type="hidden" name="_token" value="${token}">
                                                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form method="POST" action="/admin/candidates/${candidate.id}/reject" class="inline">
                                                <input type="hidden" name="_token" value="${token}">
                                                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700">Reject</button>
                                            </form>
                                        ` : ''}
                                        <button class="view-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" 
                                            data-id="${candidate.id}" 
                                            data-candidate="${candidate.name}" 
                                            data-course="${candidate.course?.name || 'N/A'}" 
                                            data-position="${candidate.position?.name || 'N/A'}"
                                            data-party="${candidate.party ? candidate.party.name : 'No Party'}" 
                                            data-description="${(candidate.description || '').replace(/"/g, '&quot;')}" 
                                            data-status="${candidate.status}" 
                                            data-profile-image="${candidate.profile_image_url || '/images/default-profile.jpg'}" 
                                            data-banner-image="${candidate.banner_image_url || '/images/default-banner.png'}" 
                                            data-platforms="${(candidate.platforms || 'No Platforms').replace(/"/g, '&quot;')}" 
                                            data-related-events="${(candidate.related_events || '').replace(/"/g, '&quot;')}" 
                                            data-joined-elections="${String(candidate.joined_elections || '').replace(/"/g, '&quot;')}">
                                            View
                                        </button>
                                        ${!isArchivedView ? `
                                            <button class="edit-button bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" 
                                                data-id="${candidate.id}" 
                                                data-candidate="${candidate.name}" 
                                                data-course="${candidate.course?.name || ''}" 
                                                data-course-id="${candidate.course?.id || ''}" 
                                                data-position="${candidate.position?.name || ''}"
                                                data-position-id="${candidate.position?.id || ''}"
                                                data-party="${candidate.party ? candidate.party.name : 'No Party'}" 
                                                data-party-id="${candidate.party ? candidate.party.id : ''}" 
                                                data-description="${(candidate.description || '').replace(/"/g, '&quot;')}" 
                                                data-status="${candidate.status}" 
                                                data-profile-image="${candidate.profile_image_url || '/images/default-profile.jpg'}" 
                                                data-banner-image="${candidate.banner_image_url || '/images/default-banner.png'}" 
                                                data-platforms="${(candidate.platforms || '').replace(/"/g, '&quot;')}"
                                                data-related-events="${(candidate.related_events || '').replace(/"/g, '&quot;')}"
                                                data-joined-elections="${String(candidate.joined_elections || '').replace(/"/g, '&quot;')}">
                                                Edit
                                            </button>
                                        ` : ''}
                                        ${isArchivedView ? `
                                            <form method="POST" action="/admin/candidates/${candidate.id}/restore" class="inline">
                                                <input type="hidden" name="_token" value="${token}">
                                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Restore</button>
                                            </form>
                                            <form method="POST" action="/admin/candidates/${candidate.id}/force" id="force-delete-form-${candidate.id}" class="inline">
                                                <input type="hidden" name="_token" value="${token}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="button" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 force-delete-button" data-id="${candidate.id}">
                                                    Permanent Delete
                                                </button>
                                            </form>
                                        ` : `
                                            <form method="POST" action="/admin/candidates/${candidate.id}" id="delete-form-${candidate.id}" class="inline">
                                                <input type="hidden" name="_token" value="${token}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button" data-id="${candidate.id}">
                                                    Delete
                                                </button>
                                            </form>
                                        `}
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }
                    
                    // Update both pagination containers
                    if (paginationContainerTop && data.pagination_html) {
                        paginationContainerTop.innerHTML = data.pagination_html;
                    }
                    if (paginationContainer && data.pagination_html) {
                        paginationContainer.innerHTML = data.pagination_html;
                    }
                    
                    // Update results info
                    this.updateResultsInfo(data.metadata);
                    
                    // Re-attach event handlers to newly created buttons
                    this.reattachEventListeners();
                } else {
                    throw new Error(data.message || 'Failed to load candidates');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500">Error loading data: ${error.message}</td></tr>`;
                }
                NotificationController.showError(`Failed to load candidates: ${error.message}`);
            })
            .finally(() => {
                this.filters.isLoading = false;
                if (this.filters.pendingPage !== null) {
                    const pendingPage = this.filters.pendingPage;
                    this.filters.pendingPage = null;
                    this.loadCandidates(pendingPage);
                }
            });
        },
        reattachEventListeners() {
            document.querySelectorAll('.view-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    CandidateController.handleViewClick(btn);
                });
            });
            
            document.querySelectorAll('.edit-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    CandidateController.handleEditClick(btn);
                });
            });
            
            document.querySelectorAll('.delete-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const candidateId = btn.getAttribute('data-id');
                    CandidateController.handleDeleteClick(candidateId);
                });
            });

            document.querySelectorAll('.force-delete-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const candidateId = btn.getAttribute('data-id');
                    CandidateController.handleForceDeleteClick(candidateId);
                });
            });
        },
        init() {
            this.setupPaginationLinks();
            this.setupColumnFilters();
            // Don't auto-load on init as the page already has data
        }
    };

    /** 
     * Notification Controller - Handles messages to the user 
     */
    const NotificationController = {
        showError(message) {
            const errorMessageEl = document.getElementById('error-message');
            if (errorMessageEl) {
                errorMessageEl.textContent = message;
                ModalController.show('error');
            } else {
                console.error('Error message element not found');
                alert(message);
            }
        },
        showSuccess(message) {
            // Create a success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    };

    /** 
     * Form Controller - Handles form operations 
     */
    const FormController = {
        resetForm(formId) {
            const form = document.getElementById(formId);
            if (form) {
                form.reset();
                
                // Reset file input display texts
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    const id = input.id;
                    if (id.includes('profile')) {
                        const filenameSpan = document.getElementById(id.replace('-image', '-filename'));
                        if (filenameSpan) filenameSpan.textContent = 'Choose profile image...';
                    }
                    if (id.includes('banner')) {
                        const filenameSpan = document.getElementById(id.replace('-image', '-filename'));
                        if (filenameSpan) filenameSpan.textContent = 'Choose banner image...';
                    }
                });
                
                // Hide all preview images
                const previews = form.querySelectorAll('img[id$="-preview"]');
                previews.forEach(preview => {
                    preview.src = '';
                    preview.classList.add('hidden');
                });
                
                // Reset Quill editors if available
                if (window.QuillEditors) {
                    if (formId === 'new-candidate-form') {
                        if (window.QuillEditors.newDescription) {
                            window.QuillEditors.newDescription.root.innerHTML = '';
                        }
                        if (window.QuillEditors.newPlatforms) {
                            window.QuillEditors.newPlatforms.root.innerHTML = '';
                        }
                    }
                }
            }
        },
        validateForm(formData, requiredFields, imageFields = []) {
            for (const field of requiredFields) {
                if (!formData.get(field) || formData.get(field) === '') {
                    NotificationController.showError(`Please fill in the ${field.replace('_', ' ')} field`);
                    return false;
                }
            }

            for (const field of imageFields) {
                const file = formData.get(field);
                if (!file || (file instanceof File && file.size === 0)) {
                    NotificationController.showError(`Please choose a ${field.replace('_', ' ')}.`);
                    return false;
                }
            }
            
            const courseValue = formData.get('course_id');
            if (!courseValue || courseValue === "") {
                NotificationController.showError("Please select a valid course.");
                return false;
            }
            
            return true;
        },
        init() {
            // Handle file input changes for all forms
            const fileInputHandlers = [
                { input: 'new-candidate-image', filename: 'new-profile-filename', preview: 'new-candidate-image-preview' },
                { input: 'new-candidate-banner', filename: 'new-banner-filename', preview: 'new-candidate-banner-preview' },
                { input: 'edit-profile-image', filename: 'edit-profile-filename', preview: 'edit-profile-image-preview' },
                { input: 'edit-banner-image', filename: 'edit-banner-filename', preview: 'edit-banner-image-preview' }
            ];

            fileInputHandlers.forEach(handler => {
                const input = document.getElementById(handler.input);
                const filenameSpan = document.getElementById(handler.filename);
                const preview = document.getElementById(handler.preview);
                
                if (input && filenameSpan && preview) {
                    input.addEventListener('change', function() {
                        const filename = this.files[0]?.name || 
                            (handler.input.includes('profile') ? 'Choose profile image...' : 'Choose banner image...');
                        filenameSpan.textContent = filename;
                        
                        if (this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            preview.classList.add('hidden');
                        }
                    });
                }
            });
        }
    };

    /** 
     * API Controller - Handles all data operations 
     */
    const ApiController = {
        async postFormData(url, formData, method = 'POST') {
            try {
                console.log(`Submitting to ${url} with method ${method}`);
                
                if (method !== 'POST') {
                    formData.set('_method', method);
                }
                
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (token) {
                    formData.set('_token', token);
                } else {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok) {
                        if (data && data.errors && typeof data.errors === 'object') {
                            const firstKey = Object.keys(data.errors)[0];
                            const firstMsg = firstKey ? (data.errors[firstKey]?.[0] || data.errors[firstKey]) : null;
                            if (firstMsg) {
                                throw new Error(firstMsg);
                            }
                        }
                        if (data.errors && data.errors.course_id) {
                            throw new Error("Course not found. Please select a valid course.");
                        }
                        throw new Error(data.message || `Server error: ${response.status}`);
                    }
                    return data;
                } else {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error(`Server error: ${response.status}. Please check the server logs.`);
                }
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }
    };

    /** 
     * Candidate Controller - Handles candidate-specific operations 
     */
    const CandidateController = {
        candidateToDelete: null,
        
        handleViewClick(btn) {
            document.getElementById('candidate-status').textContent = btn.getAttribute('data-status') || "N/A";
            document.getElementById('candidate-name').textContent = btn.getAttribute('data-candidate') || "N/A";
            document.getElementById('candidate-course').textContent = btn.getAttribute('data-course') || "N/A";
            const posEl = document.getElementById('candidate-position');
            if (posEl) posEl.textContent = btn.getAttribute('data-position') || "N/A";
            document.getElementById('candidate-party').textContent = btn.getAttribute('data-party') || "N/A";
            
            const descriptionContent = btn.getAttribute('data-description') || "No description available.";
            document.getElementById('candidate-description').innerHTML = descriptionContent;
            
            document.getElementById('candidate-profile-image').src = btn.getAttribute('data-profile-image') || "/images/default-profile.jpg";
            document.getElementById('candidate-banner-image').src = btn.getAttribute('data-banner-image') || "/images/default-banner.png";
            
            const platformsContent = btn.getAttribute('data-platforms') || "No Platforms";
            const platformList = document.getElementById('candidate-platforms');
            platformList.innerHTML = '';
            
            if (platformsContent && platformsContent !== 'No Platforms') {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = platformsContent;
                
                const text = tempDiv.textContent || tempDiv.innerText || '';
                if (text.trim()) {
                    const platforms = text.split(',');
                    platforms.forEach(platform => {
                        const trimmed = platform.trim();
                        if (trimmed) {
                            const li = document.createElement('li');
                            li.textContent = trimmed;
                            platformList.appendChild(li);
                        }
                    });
                } else {
                    platformList.innerHTML = '<li>No platforms available</li>';
                }
            } else {
                platformList.innerHTML = '<li>No platforms available</li>';
            }
            
            // Handle related events
            const relatedEventsData = btn.getAttribute('data-related-events');
            const eventList = document.getElementById('candidate-related-events');
            eventList.innerHTML = '';
            
            if (relatedEventsData) {
                const events = relatedEventsData.split(',');
                events.forEach(event => {
                    const li = document.createElement('li');
                    li.textContent = event.trim();
                    eventList.appendChild(li);
                });
            } else {
                eventList.innerHTML = '<li>No related events available</li>';
            }
            
            // Handle joined elections
            const joinedElectionsData = btn.getAttribute('data-joined-elections');
            const electionList = document.getElementById('candidate-joined-elections');
            electionList.innerHTML = '';
            
            try {
                if (joinedElectionsData) {
                    const elections = JSON.parse(joinedElectionsData);
                    elections.forEach(election => {
                        const li = document.createElement('li');
                        li.textContent = `${election.year} - ${election.position} ${election.winner ? '(Winner)' : ''}`;
                        electionList.appendChild(li);
                    });
                } else {
                    electionList.innerHTML = '<li>No election history available</li>';
                }
            } catch (error) {
                console.error('Error parsing joined elections:', error);
                electionList.innerHTML = '<li>Error loading election history</li>';
            }
            
            ModalController.show('view');
        },
        
        handleEditClick(btn) {
            const id = btn.getAttribute('data-id');
            
            document.getElementById('edit-candidate-id').value = id;
            document.getElementById('edit-candidate-name').value = btn.getAttribute('data-candidate') || '';
            document.getElementById('edit-candidate-course').value = btn.getAttribute('data-course-id') || '';
            const editPos = document.getElementById('edit-candidate-position');
            if (editPos) editPos.value = btn.getAttribute('data-position-id') || '';
            document.getElementById('edit-candidate-party').value = btn.getAttribute('data-party-id') || '';
            document.getElementById('edit-candidate-status').value = btn.getAttribute('data-status') || 'Active';
            
            // Set current images
            const profileImage = btn.getAttribute('data-profile-image');
            const bannerImage = btn.getAttribute('data-banner-image');
            
            const profilePreview = document.getElementById('edit-profile-image-preview');
            const bannerPreview = document.getElementById('edit-banner-image-preview');
            
            if (profileImage) {
                profilePreview.src = profileImage;
                profilePreview.classList.remove('hidden');
            }
            
            if (bannerImage) {
                bannerPreview.src = bannerImage;
                bannerPreview.classList.remove('hidden');
            }
            
            // Update Quill editors if available
            if (window.QuillEditors) {
                const description = btn.getAttribute('data-description') || '';
                const platforms = btn.getAttribute('data-platforms') || '';
                
                if (window.QuillEditors.editDescription) {
                    window.QuillEditors.editDescription.root.innerHTML = description;
                }
                
                if (window.QuillEditors.editPlatforms) {
                    window.QuillEditors.editPlatforms.root.innerHTML = platforms;
                }
            }

            const relatedEl = document.getElementById('edit-candidate-related-events');
            if (relatedEl) relatedEl.value = btn.getAttribute('data-related-events') || '';

            const joinedEl = document.getElementById('edit-candidate-joined-elections');
            if (joinedEl) joinedEl.value = btn.getAttribute('data-joined-elections') || '';
            
            ModalController.show('edit');
        },
        
        handleDeleteClick(id) {
            this.candidateToDelete = document.querySelector(`#delete-form-${id}`);
            ModalController.show('confirmation');
        },

        handleForceDeleteClick(id) {
            this.candidateToDelete = document.querySelector(`#force-delete-form-${id}`);
            ModalController.show('confirmation');
        },
        
        async submitNewForm(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            // Get form values
            const name = document.getElementById('new-candidate-name').value;
            const course = document.getElementById('new-candidate-course').value;
            const position = document.getElementById('new-candidate-position')?.value;
            const status = document.getElementById('new-candidate-status').value;
            const party = document.getElementById('new-candidate-party').value;
            const relatedEvents = document.getElementById('new-candidate-related-events')?.value || '';
            const joinedElections = document.getElementById('new-candidate-joined-elections')?.value || '';
            
            let description = '';
            let platforms = '';
            
            // Get content from Quill editors or fallback to hidden inputs
            if (window.QuillEditors && window.QuillEditors.newDescription) {
                description = window.QuillEditors.newDescription.root.innerHTML;
                platforms = window.QuillEditors.newPlatforms.root.innerHTML;
            } else {
                const descEditor = document.getElementById('new-description-editor');
                const platsEditor = document.getElementById('new-platforms-editor');
                description = descEditor ? descEditor.innerHTML : document.getElementById('new-candidate-description')?.value;
                platforms = platsEditor ? platsEditor.innerHTML : document.getElementById('new-candidate-platforms')?.value;
            }
            
            // Append all data to FormData
            formData.set('name', name);
            formData.set('description', description);
            formData.set('course_id', course);
            formData.set('position_id', position || '');
            formData.set('platforms', platforms);
            formData.set('status', status);
            formData.set('party_id', party || '');
            formData.set('related_events', relatedEvents);
            formData.set('joined_elections', joinedElections);
            
            // Validate required fields
            const requiredFields = ['name', 'description', 'course_id', 'platforms', 'status'];
            const imageFields = ['profile_img', 'banner_img'];
            
            if (!FormController.validateForm(formData, requiredFields, imageFields)) {
                return;
            }
            
            const saveButton = document.getElementById('save-new-candidate');
            const originalButtonText = saveButton.innerHTML;
            saveButton.innerHTML = 'Saving...';
            saveButton.disabled = true;
            
            try {
                const result = await ApiController.postFormData('/admin/candidates', formData);
                if (result.success) {
                    ModalController.hide('newCandidate');
                    FormController.resetForm('new-candidate-form');
                    NotificationController.showSuccess('Candidate created successfully!');
                    // Refresh the candidates list to show the new candidate
                    PaginationController.loadCandidates(1); // Go to first page to see new candidate
                } else {
                    NotificationController.showError('Failed to create candidate: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                NotificationController.showError(error.message || 'Failed to create candidate');
            } finally {
                saveButton.innerHTML = originalButtonText;
                saveButton.disabled = false;
            }
        },
        
        async submitEditForm(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const candidateId = document.getElementById('edit-candidate-id').value;
            
            // Get form values
            const name = document.getElementById('edit-candidate-name').value;
            const course = document.getElementById('edit-candidate-course').value;
            const position = document.getElementById('edit-candidate-position')?.value;
            const status = document.getElementById('edit-candidate-status').value;
            const party = document.getElementById('edit-candidate-party').value;
            const relatedEvents = document.getElementById('edit-candidate-related-events')?.value || '';
            const joinedElections = document.getElementById('edit-candidate-joined-elections')?.value || '';
            
            let description = '';
            let platforms = '';
            
            // Get content from Quill editors or fallback to hidden inputs
            if (window.QuillEditors && window.QuillEditors.editDescription) {
                description = window.QuillEditors.editDescription.root.innerHTML;
                platforms = window.QuillEditors.editPlatforms.root.innerHTML;
            } else {
                const descEditor = document.getElementById('edit-description-editor');
                const platsEditor = document.getElementById('edit-platforms-editor');
                description = descEditor ? descEditor.innerHTML : document.getElementById('edit-candidate-description')?.value;
                platforms = platsEditor ? platsEditor.innerHTML : document.getElementById('edit-candidate-platforms')?.value;
            }
            
            // Append all data to FormData
            formData.set('name', name);
            formData.set('description', description);
            formData.set('course_id', course);
            formData.set('position_id', position || '');
            formData.set('platforms', platforms);
            formData.set('status', status);
            formData.set('party_id', party || '');
            formData.set('related_events', relatedEvents);
            formData.set('joined_elections', joinedElections);
            
            // Validate required fields
            const requiredFields = ['name', 'description', 'course_id', 'platforms', 'status'];
            
            if (!FormController.validateForm(formData, requiredFields)) {
                return;
            }
            
            const saveButton = document.getElementById('save-edit');
            const originalButtonText = saveButton.innerHTML;
            saveButton.innerHTML = 'Saving...';
            saveButton.disabled = true;
            
            try {
                const result = await ApiController.postFormData(`/admin/candidates/${candidateId}`, formData, 'PUT');
                if (result.success) {
                    ModalController.hide('edit');
                    NotificationController.showSuccess('Candidate updated successfully!');
                    // Refresh the candidates list to show the updated candidate
                    PaginationController.loadCandidates(PaginationController.currentPage);
                } else {
                    NotificationController.showError('Failed to update candidate: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                NotificationController.showError(error.message || 'Failed to update candidate');
            } finally {
                saveButton.innerHTML = originalButtonText;
                saveButton.disabled = false;
            }
        },
        
        confirmDelete() {
            if (this.candidateToDelete) {
                const formData = new FormData(this.candidateToDelete);
                const url = this.candidateToDelete.action;
                
                ApiController.postFormData(url, formData, 'DELETE')
                    .then(result => {
                        if (result.success) {
                            NotificationController.showSuccess('Candidate deleted successfully!');
                            // Refresh the candidates list
                            PaginationController.loadCandidates(PaginationController.currentPage);
                        } else {
                            NotificationController.showError(result.message || 'Deletion failed');
                        }
                    })
                    .catch(error => {
                        NotificationController.showError(error.message || 'Deletion failed');
                    });
            }
            
            ModalController.hide('confirmation');
        },
        
        setupButtonListeners() {
            // Add listeners for View, Edit, Delete buttons
            document.querySelectorAll('.view-button').forEach(btn => {
                btn.addEventListener('click', () => this.handleViewClick(btn));
            });
            
            document.querySelectorAll('.edit-button').forEach(btn => {
                btn.addEventListener('click', () => this.handleEditClick(btn));
            });
            
            document.querySelectorAll('.delete-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const candidateId = btn.getAttribute('data-id');
                    this.handleDeleteClick(candidateId);
                });
            });

            document.querySelectorAll('.force-delete-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const candidateId = btn.getAttribute('data-id');
                    this.handleForceDeleteClick(candidateId);
                });
            });
        },
        
        init() {
            // Initial setup of button listeners
            this.setupButtonListeners();
            
            // Add New Candidate button listener
            const newCandidateButton = document.getElementById('new-candidate-btn');
            if (newCandidateButton) {
                newCandidateButton.addEventListener('click', e => {
                    e.preventDefault();
                    FormController.resetForm('new-candidate-form');
                    ModalController.show('newCandidate');
                });
            }
            
            // New candidate form submission
            const newForm = document.getElementById('new-candidate-form');
            if (newForm) {
                newForm.addEventListener('submit', this.submitNewForm.bind(this));
            }
            
            // Edit candidate form submission
            const editForm = document.getElementById('edit-candidate-form');
            if (editForm) {
                editForm.addEventListener('submit', this.submitEditForm.bind(this));
            }
            
            // Delete confirmation
            const confirmDeleteBtn = document.getElementById('confirm-delete');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', this.confirmDelete.bind(this));
            }
        }
    };

    /** 
     * Rich Text Editor Controller 
     */
    const RichTextController = {
        loadQuill() {
            if (!document.querySelector('link[href*="quill.snow.css"]')) {
                const quillCss = document.createElement('link');
                quillCss.rel = 'stylesheet';
                quillCss.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                document.head.appendChild(quillCss);
            }
            
            if (typeof Quill === 'undefined') {
                const quillScript = document.createElement('script');
                quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
                quillScript.onload = this.initQuillEditors.bind(this);
                quillScript.onerror = () => {
                    console.error('Failed to load Quill editor');
                    this.initQuillEditors();
                };
                document.body.appendChild(quillScript);
            } else {
                this.initQuillEditors();
            }
        },
        
        initQuillEditors() {
            window.QuillEditors = {};
            
            const editorConfig = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'font': ['roboto', 'arial', 'georgia', 'times-new-roman', 'verdana', 'courier-new'] }, { 'size': ['12px', '14px', '16px', '18px', '24px', '32px'] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            };
            
            // Initialize editors if Quill is available
            if (typeof Quill !== 'undefined') {
                if (document.getElementById('edit-description-editor')) {
                    window.QuillEditors.editDescription = new Quill('#edit-description-editor', { 
                        ...editorConfig, 
                        placeholder: 'Write candidate description.' 
                    });
                    
                    window.QuillEditors.editPlatforms = new Quill('#edit-platforms-editor', { 
                        ...editorConfig, 
                        placeholder: 'Write candidate platforms.' 
                    });
                }
                
                if (document.getElementById('new-description-editor')) {
                    window.QuillEditors.newDescription = new Quill('#new-description-editor', { 
                        ...editorConfig, 
                        placeholder: 'Write candidate description.' 
                    });
                    
                    window.QuillEditors.newPlatforms = new Quill('#new-platforms-editor', { 
                        ...editorConfig, 
                        placeholder: 'Write candidate platforms.' 
                    });
                }
            }
        },
        
        init() {
            const editorContainers = [
                'edit-description-editor',
                'edit-platforms-editor',
                'new-description-editor',
                'new-platforms-editor'
            ];
            
            if (editorContainers.some(id => document.getElementById(id))) {
                this.loadQuill();
            }
        }
    };

    // ===== INITIALIZATION =====
    ModalController.init();
    PaginationController.setupPaginationLinks();
    PaginationController.setupStatusFilters();
    RichTextController.init();
    
    // Load initial candidates
    PaginationController.loadCandidates(1);
    PaginationController.init();
});