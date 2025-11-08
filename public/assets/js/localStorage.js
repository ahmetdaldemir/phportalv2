/**
 * LocalStorage Management System
 * Handles caching of frequently used data like sellers, companies, users, categories
 * Optimized for performance and reduced API calls
 */

(function() {
    'use strict';

    // Check if already loaded
    if (window.localStorageManager) {
        console.log('LocalStorageManager already exists, skipping initialization');
        return;
    }

    class LocalStorageManager {
        constructor() {
            this.cacheExpiry = 60 * 60 * 1000; // 1 hour in milliseconds (reduced from 24h)
            this.cacheKeys = {
                sellers: 'sellers',
                companies: 'companies',
                users: 'users',
                categories: 'categories',
                brands: 'brands',
                colors: 'colors',
                reasons: 'reasons',
                warehouses: 'warehouses',
                versions: 'versions'
            };
            
            // Get company ID from meta tag or default to 1
            this.companyId = this.getCompanyId();
            
            // Add company-specific cache keys
            this.companyCacheKeys = {};
            Object.keys(this.cacheKeys).forEach(key => {
                this.companyCacheKeys[key] = `${key}_company_${this.companyId}`;
            });
        }

        /**
         * Get company ID from meta tag or default
         */
        getCompanyId() {
            try {
                const metaTag = document.querySelector('meta[name="company-id"]');
                return metaTag ? metaTag.getAttribute('content') : '1';
            } catch (error) {
                console.warn('Could not get company ID, using default:', error);
                return '1';
            }
        }

        /**
         * Get data from localStorage with expiry check
         */
        get(key) {
            try {
                const cacheKey = this.companyCacheKeys[key] || key;
                const item = localStorage.getItem(cacheKey);
                if (!item) return null;

                const data = JSON.parse(item);
                
                // Check if data has expired
                if (data.expiry && Date.now() > data.expiry) {
                    localStorage.removeItem(cacheKey);
                    return null;
                }

                return data.value;
            } catch (error) {
                console.error('Error reading from localStorage:', error);
                return null;
            }
        }

        /**
         * Set data in localStorage with expiry
         */
        set(key, value, expiry = null) {
            try {
                const cacheKey = this.companyCacheKeys[key] || key;
                const data = {
                    value: value,
                    expiry: expiry || (Date.now() + this.cacheExpiry),
                    timestamp: Date.now(),
                    companyId: this.companyId
                };
                localStorage.setItem(cacheKey, JSON.stringify(data));
                return true;
            } catch (error) {
                console.error('Error writing to localStorage:', error);
                return false;
            }
        }

        /**
         * Remove item from localStorage
         */
        remove(key) {
            try {
                const cacheKey = this.companyCacheKeys[key] || key;
                localStorage.removeItem(cacheKey);
                return true;
            } catch (error) {
                console.error('Error removing from localStorage:', error);
                return false;
            }
        }

        /**
         * Clear all cached data for current company
         */
        clearAll() {
            try {
                Object.values(this.companyCacheKeys).forEach(key => {
                    localStorage.removeItem(key);
                });
                return true;
            } catch (error) {
                console.error('Error clearing localStorage:', error);
                return false;
            }
        }

        /**
         * Get cached data or fetch from API with improved error handling
         */
        async getOrFetch(key, apiUrl, forceRefresh = false) {
            // If not forcing refresh, try to get from cache first
            if (!forceRefresh) {
                const cached = this.get(key);
                if (cached) {
                    console.log(`Cache hit for ${key}:`, cached.length, 'items');
                    return cached;
                }
            }

            try {
                console.log(`Fetching ${key} from API...`);
                const response = await fetch(apiUrl, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();

                if (data && Array.isArray(data)) {
                    // Cache the data
                    this.set(key, data);
                    console.log(`Cached ${key}:`, data.length, 'items');
                    return data;
                } else {
                    console.error(`Invalid data format for ${key}:`, data);
                    return [];
                }
            } catch (error) {
                console.error(`Error fetching ${key}:`, error);
                
                // Try to return cached data even if expired as fallback
                const cached = this.get(key);
                if (cached) {
                    console.log(`Using expired cache for ${key} as fallback`);
                    return cached;
                }
                
                return [];
            }
        }

        /**
         * Initialize all cached data with progress tracking
         */
        async initializeCache() {
            console.log('Initializing localStorage cache for company:', this.companyId);
            
            const promises = [
                this.getOrFetch('sellers', '/api/sellers'),
                this.getOrFetch('brands', '/api/brands'),
                this.getOrFetch('colors', '/api/colors'),
                this.getOrFetch('categories', '/api/categories'),
                this.getOrFetch('reasons', '/api/reasons'),
                this.getOrFetch('warehouses', '/api/warehouses')
            ];

            try {
                const results = await Promise.allSettled(promises);
                const successCount = results.filter(r => r.status === 'fulfilled').length;
                console.log(`Cache initialization completed: ${successCount}/${promises.length} successful`);
                
                // Dispatch custom event for other components
                window.dispatchEvent(new CustomEvent('cacheInitialized', {
                    detail: { successCount, total: promises.length }
                }));
                
            } catch (error) {
                console.error('Error initializing cache:', error);
            }
        }

        /**
         * Get sellers with fallback
         */
        async getSellers(forceRefresh = false) {
            return await this.getOrFetch('sellers', '/api/sellers', forceRefresh);
        }

        /**
         * Get brands with fallback
         */
        async getBrands(forceRefresh = false) {
            return await this.getOrFetch('brands', '/api/brands', forceRefresh);
        }

        /**
         * Get colors with fallback
         */
        async getColors(forceRefresh = false) {
            return await this.getOrFetch('colors', '/api/colors', forceRefresh);
        }

        /**
         * Get categories with fallback
         */
        async getCategories(forceRefresh = false) {
            return await this.getOrFetch('categories', '/api/categories', forceRefresh);
        }

        /**
         * Get reasons with fallback
         */
        async getReasons(forceRefresh = false) {
            return await this.getOrFetch('reasons', '/api/reasons', forceRefresh);
        }

        /**
         * Get warehouses with fallback
         */
        async getWarehouses(forceRefresh = false) {
            return await this.getOrFetch('warehouses', '/api/warehouses', forceRefresh);
        }

        /**
         * Get versions from cache or API
         */
        async getVersions(brandId = null) {
            if (brandId) {
                // For brand-specific versions, we don't cache as they change frequently
                try {
                    const response = await fetch(`/api/versions?brand_id=${brandId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    if (response.ok) {
                        return await response.json();
                    } else {
                        console.error('Failed to fetch versions:', response.statusText);
                        return [];
                    }
                } catch (error) {
                    console.error('Error fetching versions:', error);
                    return [];
                }
            }
            
            // For general versions, use cache
            return this.getOrFetch('versions', '/api/versions');
        }

        /**
         * Populate select element with cached data
         */
        populateSelect(selectElement, data, placeholder = 'Seçiniz', valueKey = 'id', textKey = 'name') {
            if (!selectElement || !data) return;

            // Clear existing options
            selectElement.innerHTML = `<option value="">${placeholder}</option>`;

            // Add options
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[textKey];
                selectElement.appendChild(option);
            });
        }

        /**
         * Get cache status for debugging
         */
        getCacheStatus() {
            const status = {};
            Object.entries(this.cacheKeys).forEach(([key, _]) => {
                const data = this.get(key);
                status[key] = {
                    exists: !!data,
                    count: data ? data.length : 0,
                    timestamp: data ? new Date(this.get(key + '_timestamp')).toLocaleString() : null,
                    companyId: this.companyId
                };
            });
            return status;
        }

        /**
         * Refresh specific cache
         */
        async refreshCache(key) {
            const apiUrl = `/api/${key}`;
            return await this.getOrFetch(key, apiUrl, true);
        }

        /**
         * Refresh all cache
         */
        async refreshAllCache() {
            console.log('Refreshing all cache...');
            await this.initializeCache();
            console.log('All cache refreshed');
        }

        /**
         * Check if cache is fresh (less than 30 minutes old)
         */
        isCacheFresh(key) {
            try {
                const cacheKey = this.companyCacheKeys[key] || key;
                const item = localStorage.getItem(cacheKey);
                if (!item) return false;

                const data = JSON.parse(item);
                const age = Date.now() - data.timestamp;
                return age < (30 * 60 * 1000); // 30 minutes
            } catch (error) {
                return false;
            }
        }
    }

    // Create global instance only if it doesn't exist
    window.localStorageManager = new LocalStorageManager();

    // Auto-initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize cache in background
        window.localStorageManager.initializeCache();
    });

    // Export for module systems
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = LocalStorageManager;
    }
})();
