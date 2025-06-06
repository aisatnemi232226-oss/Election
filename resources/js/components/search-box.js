class SearchBox {
    constructor(options = {}) {
        this.options = {
            searchInputId: 'search-box',
            onSearch: null,
            debounceTime: 300,
            ...options
        };

        this.searchInput = null;
        this.clearButton = null;
        this.debounceTimer = null;

        this.init();
    }

    init() {
        this.searchInput = document.getElementById(this.options.searchInputId);
        
        if (!this.searchInput) {
            console.error(`Search input with id "${this.options.searchInputId}" not found`);
            return;
        }

        this.clearButton = this.searchInput.parentElement.querySelector('.clear-button');
        
        if (!this.clearButton) {
            console.error('Clear button not found in search box component');
            return;
        }

        this.setupEventListeners();
    }

    setupEventListeners() {
        // Add debounced search handler
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                const query = e.target.value.toLowerCase().trim();
                this.handleSearch(query);
            }, this.options.debounceTime);
        });

        // Add clear button handler
        this.clearButton.addEventListener('click', () => {
            this.clearSearch();
        });
    }

    handleSearch(query) {
        // Update clear button visibility
        this.clearButton.style.display = query ? 'block' : 'none';

        // Call the onSearch callback if provided
        if (typeof this.options.onSearch === 'function') {
            this.options.onSearch(query);
        }
    }

    clearSearch() {
        if (this.searchInput) {
            this.searchInput.value = '';
            this.clearButton.style.display = 'none';
            this.handleSearch('');
        }
    }

    getValue() {
        return this.searchInput ? this.searchInput.value : '';
    }

    setValue(value) {
        if (this.searchInput) {
            this.searchInput.value = value;
            this.handleSearch(value);
        }
    }
}

// Make SearchBox available globally
window.SearchBox = SearchBox;