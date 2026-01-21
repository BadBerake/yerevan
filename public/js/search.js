/**
 * Advanced Search & Filters
 * Client-side search functionality with debouncing and AJAX
 */

class SearchManager {
    constructor() {
        this.searchInput = document.getElementById('search-input');
        this.searchForm = document.getElementById('search-form');
        this.filtersForm = document.getElementById('filters-form');
        this.resultsContainer = document.getElementById('search-results');
        this.resultCount = document.getElementById('result-count');
        this.suggestionsBox = document.getElementById('search-suggestions');
        this.activeFiltersContainer = document.getElementById('active-filters');

        this.debounceTimer = null;
        this.currentFilters = this.getFiltersFromURL();

        this.init();
    }

    init() {
        if (!this.searchInput) return;

        // Search input with debounce
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.showSuggestions(e.target.value);
            }, 300);
        });

        // Search form submission
        if (this.searchForm) {
            this.searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }

        // Filter changes
        if (this.filtersForm) {
            const filterInputs = this.filtersForm.querySelectorAll('input, select');
            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.performSearch();
                });
            });
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.searchInput.contains(e.target) && !this.suggestionsBox?.contains(e.target)) {
                this.hideSuggestions();
            }
        });

        // Initialize with URL filters
        this.applyFiltersToUI();
        this.displayActiveFilters();

        // Get user location for distance filter
        this.getUserLocation();
    }

    /**
     * Show search suggestions
     */
    async showSuggestions(query) {
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }

        try {
            const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();

            if (suggestions.length > 0) {
                this.renderSuggestions(suggestions);
            } else {
                this.hideSuggestions();
            }
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    }

    /**
     * Render suggestions dropdown
     */
    renderSuggestions(suggestions) {
        if (!this.suggestionsBox) {
            this.createSuggestionsBox();
        }

        this.suggestionsBox.innerHTML = '';
        this.suggestionsBox.style.display = 'block';

        suggestions.forEach(item => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `
                <i class="fas fa-search"></i>
                <span>${this.highlightMatch(item.title, this.searchInput.value)}</span>
            `;
            div.addEventListener('click', () => {
                this.searchInput.value = item.title;
                this.hideSuggestions();
                this.performSearch();
            });
            this.suggestionsBox.appendChild(div);
        });
    }

    /**
     * Create suggestions box if it doesn't exist
     */
    createSuggestionsBox() {
        this.suggestionsBox = document.createElement('div');
        this.suggestionsBox.id = 'search-suggestions';
        this.suggestionsBox.className = 'search-suggestions';
        this.searchInput.parentNode.appendChild(this.suggestionsBox);
    }

    /**
     * Hide suggestions
     */
    hideSuggestions() {
        if (this.suggestionsBox) {
            this.suggestionsBox.style.display = 'none';
        }
    }

    /**
     * Highlight matching text
     */
    highlightMatch(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }

    /**
     * Perform search with current filters
     */
    async performSearch() {
        const query = this.searchInput.value;
        const filters = this.collectFilters();

        // Update URL
        this.updateURL(query, filters);

        // Show loading
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        }

        try {
            const params = new URLSearchParams({
                q: query,
                ...filters
            });

            const response = await fetch(`/api/search?${params}`);
            const data = await response.json();

            // Update results
            this.displayResults(data.results, data.count);
            this.displayActiveFilters();

            // Save to history
            this.saveSearchHistory(query, filters, data.count);

        } catch (error) {
            console.error('Search error:', error);
            if (this.resultsContainer) {
                this.resultsContainer.innerHTML = '<div class="error">An error occurred. Please try again.</div>';
            }
        }
    }

    /**
     * Collect filter values from form
     */
    collectFilters() {
        const filters = {};

        if (!this.filtersForm) return filters;

        // Category filters
        const categories = [];
        this.filtersForm.querySelectorAll('input[name="category[]"]:checked').forEach(cb => {
            categories.push(cb.value);
        });
        if (categories.length > 0) {
            filters.category = categories;
        }

        // Price range
        const priceRanges = [];
        this.filtersForm.querySelectorAll('input[name="price_range[]"]:checked').forEach(cb => {
            priceRanges.push(cb.value);
        });
        if (priceRanges.length > 0) {
            filters.price_range = priceRanges;
        }

        // Min rating
        const minRating = this.filtersForm.querySelector('input[name="min_rating"]');
        if (minRating && minRating.value) {
            filters.min_rating = minRating.value;
        }

        // Distance
        const maxDistance = this.filtersForm.querySelector('input[name="max_distance"]');
        if (maxDistance && maxDistance.value) {
            filters.max_distance = maxDistance.value;
        }

        // Open now
        const openNow = this.filtersForm.querySelector('input[name="open_now"]');
        if (openNow && openNow.checked) {
            filters.open_now = 'true';
        }

        // Sort
        const sort = this.filtersForm.querySelector('select[name="sort"]');
        if (sort && sort.value) {
            filters.sort = sort.value;
        }

        // User location (if available)
        if (this.userLocation) {
            filters.lat = this.userLocation.lat;
            filters.lng = this.userLocation.lng;
        }

        return filters;
    }

    /**
     * Display search results
     */
    displayResults(results, count) {
        if (!this.resultsContainer) return;

        // Update count
        if (this.resultCount) {
            this.resultCount.textContent = `${count} result${count !== 1 ? 's' : ''} found`;
        }

        if (results.length === 0) {
            this.resultsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3>No results found</h3>
                    <p>Try adjusting your filters or search query</p>
                </div>
            `;
            return;
        }

        this.resultsContainer.innerHTML = results.map(item => this.createResultCard(item)).join('');
    }

    /**
     * Create result card HTML
     */
    createResultCard(item) {
        const distance = item.distance ? `<span class="distance"><i class="fas fa-location-dot"></i> ${item.distance.toFixed(1)} km</span>` : '';
        const rating = item.rating_average > 0 ? `
            <div class="rating">
                <span class="stars">${this.renderStars(item.rating_average)}</span>
                <span class="rating-value">${item.rating_average}</span>
                <span class="review-count">(${item.review_count})</span>
            </div>
        ` : '';

        return `
            <div class="result-card">
                <a href="/place/${item.id}" class="card-link">
                    <div class="card-image" style="background-image: url('${item.image_url || '/public/img/placeholder.jpg'}')"></div>
                    <div class="card-content">
                        <h3 class="card-title">${item.title}</h3>
                        <p class="card-category">${item.category_name || ''}</p>
                        ${rating}
                        <p class="card-address"><i class="fas fa-map-marker-alt"></i> ${item.address || ''}</p>
                        ${distance}
                    </div>
                </a>
            </div>
        `;
    }

    /**
     * Render star rating
     */
    renderStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 >= 0.5;
        let stars = '';

        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star"></i>';
        }
        if (hasHalf) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        }
        const emptyStars = 5 - fullStars - (hasHalf ? 1 : 0);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }

        return stars;
    }

    /**
     * Display active filters as chips
     */
    displayActiveFilters() {
        if (!this.activeFiltersContainer) return;

        const filters = this.collectFilters();
        const chips = [];

        // Category chips
        if (filters.category) {
            const categories = Array.isArray(filters.category) ? filters.category : [filters.category];
            categories.forEach(cat => {
                chips.push(this.createFilterChip('Category', cat, () => {
                    const checkbox = this.filtersForm.querySelector(`input[name="category[]"][value="${cat}"]`);
                    if (checkbox) checkbox.checked = false;
                    this.performSearch();
                }));
            });
        }

        // Price range chips
        if (filters.price_range) {
            const prices = Array.isArray(filters.price_range) ? filters.price_range : [filters.price_range];
            prices.forEach(price => {
                chips.push(this.createFilterChip('Price', price, () => {
                    const checkbox = this.filtersForm.querySelector(`input[name="price_range[]"][value="${price}"]`);
                    if (checkbox) checkbox.checked = false;
                    this.performSearch();
                }));
            });
        }

        // Rating chip
        if (filters.min_rating) {
            chips.push(this.createFilterChip('Min Rating', `${filters.min_rating}★`, () => {
                const input = this.filtersForm.querySelector('input[name="min_rating"]');
                if (input) input.value = '';
                this.performSearch();
            }));
        }

        // Distance chip
        if (filters.max_distance) {
            chips.push(this.createFilterChip('Distance', `< ${filters.max_distance} km`, () => {
                const input = this.filtersForm.querySelector('input[name="max_distance"]');
                if (input) input.value = '';
                this.performSearch();
            }));
        }

        // Open now chip
        if (filters.open_now) {
            chips.push(this.createFilterChip('', 'Open Now', () => {
                const input = this.filtersForm.querySelector('input[name="open_now"]');
                if (input) input.checked = false;
                this.performSearch();
            }));
        }

        if (chips.length > 0) {
            this.activeFiltersContainer.innerHTML = chips.join('') + `
                <button class="clear-all-filters" onclick="searchManager.clearAllFilters()">
                    <i class="fas fa-times"></i> Clear All
                </button>
            `;
            this.activeFiltersContainer.style.display = 'flex';
        } else {
            this.activeFiltersContainer.style.display = 'none';
        }
    }

    /**
     * Create filter chip HTML
     */
    createFilterChip(label, value, onRemove) {
        return `
            <div class="filter-chip">
                ${label ? `<span class="chip-label">${label}:</span>` : ''}
                <span class="chip-value">${value}</span>
                <button class="chip-remove" onclick="event.preventDefault(); (${onRemove})()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    /**
     * Clear all filters
     */
    clearAllFilters() {
        if (!this.filtersForm) return;

        // Uncheck all checkboxes
        this.filtersForm.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

        // Clear all inputs
        this.filtersForm.querySelectorAll('input[type="range"], input[type="number"]').forEach(input => input.value = '');

        // Reset select
        this.filtersForm.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

        this.performSearch();
    }

    /**
     * Update URL with search params
     */
    updateURL(query, filters) {
        const params = new URLSearchParams();
        if (query) params.set('q', query);

        Object.keys(filters).forEach(key => {
            if (Array.isArray(filters[key])) {
                filters[key].forEach(val => params.append(key + '[]', val));
            } else {
                params.set(key, filters[key]);
            }
        });

        const newURL = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newURL);
    }

    /**
     * Get filters from URL
     */
    getFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        const filters = {};

        params.forEach((value, key) => {
            if (key.endsWith('[]')) {
                const cleanKey = key.slice(0, -2);
                if (!filters[cleanKey]) filters[cleanKey] = [];
                filters[cleanKey].push(value);
            } else {
                filters[key] = value;
            }
        });

        return filters;
    }

    /**
     * Apply filters from URL to UI
     */
    applyFiltersToUI() {
        if (!this.filtersForm) return;

        Object.keys(this.currentFilters).forEach(key => {
            const value = this.currentFilters[key];

            if (Array.isArray(value)) {
                value.forEach(val => {
                    const checkbox = this.filtersForm.querySelector(`input[name="${key}[]"][value="${val}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            } else {
                const input = this.filtersForm.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'checkbox') {
                        input.checked = value === 'true';
                    } else {
                        input.value = value;
                    }
                }
            }
        });
    }

    /**
     * Get user location for distance filter
     */
    getUserLocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    console.log('[Search] User location obtained');
                },
                (error) => {
                    console.log('[Search] Location access denied or unavailable');
                }
            );
        }
    }

    /**
     * Save search to history (analytics)
     */
    async saveSearchHistory(query, filters, count) {
        if (!query) return;

        try {
            await fetch('/api/search/history', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ query, filters, count })
            });
        } catch (error) {
            // Silently fail
        }
    }
}

// Initialize search manager
let searchManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        searchManager = new SearchManager();
    });
} else {
    searchManager = new SearchManager();
}
