/**
 * Global Store for Common Modules
 * Centralized state management for frequently used data
 */

export const useGlobalStore = () => {
    return {
        // Data cache
        cache: {
            sellers: [],
            categories: [],
            warehouses: [],
            colors: [],
            brands: [],
            versions: [],
            reasons: [],
            customers: [],
            cities: [],
            towns: [],
            currencies: [],
            safes: [],
            users: []
        },
        
        // Loading states
        loading: {
            sellers: false,
            categories: false,
            warehouses: false,
            colors: false,
            brands: false,
            versions: false,
            reasons: false,
            customers: false,
            cities: false,
            towns: false,
            currencies: false,
            safes: false,
            users: false
        },
        
        // Cache timestamps for invalidation
        cacheTimestamps: {},
        
        // Cache duration (5 minutes)
        cacheDuration: 5 * 60 * 1000,
        
        /**
         * Check if cache is valid
         */
        isCacheValid(key) {
            const timestamp = this.cacheTimestamps[key];
            if (!timestamp) return false;
            return (Date.now() - timestamp) < this.cacheDuration;
        },
        
        /**
         * Get Sellers
         */
        async getSellers(forceRefresh = false) {
            if (!forceRefresh && this.cache.sellers.length > 0 && this.isCacheValid('sellers')) {
                return this.cache.sellers;
            }
            
            this.loading.sellers = true;
            try {
                const response = await fetch('/api/common/sellers');
                const data = await response.json();
                this.cache.sellers = data;
                this.cacheTimestamps.sellers = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading sellers:', error);
                return [];
            } finally {
                this.loading.sellers = false;
            }
        },
        
        /**
         * Get Categories
         */
        async getCategories(forceRefresh = false) {
            if (!forceRefresh && this.cache.categories.length > 0 && this.isCacheValid('categories')) {
                return this.cache.categories;
            }
            
            this.loading.categories = true;
            try {
                const response = await fetch('/api/common/categories');
                const data = await response.json();
                this.cache.categories = data;
                this.cacheTimestamps.categories = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading categories:', error);
                return [];
            } finally {
                this.loading.categories = false;
            }
        },
        
        /**
         * Get Warehouses
         */
        async getWarehouses(forceRefresh = false) {
            if (!forceRefresh && this.cache.warehouses.length > 0 && this.isCacheValid('warehouses')) {
                return this.cache.warehouses;
            }
            
            this.loading.warehouses = true;
            try {
                const response = await fetch('/api/common/warehouses');
                const data = await response.json();
                this.cache.warehouses = data;
                this.cacheTimestamps.warehouses = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading warehouses:', error);
                return [];
            } finally {
                this.loading.warehouses = false;
            }
        },
        
        /**
         * Get Colors
         */
        async getColors(forceRefresh = false) {
            if (!forceRefresh && this.cache.colors.length > 0 && this.isCacheValid('colors')) {
                return this.cache.colors;
            }
            
            this.loading.colors = true;
            try {
                const response = await fetch('/api/common/colors');
                const data = await response.json();
                this.cache.colors = data;
                this.cacheTimestamps.colors = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading colors:', error);
                return [];
            } finally {
                this.loading.colors = false;
            }
        },
        
        /**
         * Get Brands
         */
        async getBrands(forceRefresh = false) {
            if (!forceRefresh && this.cache.brands.length > 0 && this.isCacheValid('brands')) {
                return this.cache.brands;
            }
            
            this.loading.brands = true;
            try {
                const response = await fetch('/api/common/brands');
                const data = await response.json();
                this.cache.brands = data;
                this.cacheTimestamps.brands = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading brands:', error);
                return [];
            } finally {
                this.loading.brands = false;
            }
        },
        
        /**
         * Get Versions by Brand
         */
        async getVersions(brandId = null, forceRefresh = false) {
            const cacheKey = brandId ? `versions_${brandId}` : 'versions';
            
            if (!forceRefresh && this.cache.versions.length > 0 && this.isCacheValid(cacheKey)) {
                return brandId ? 
                    this.cache.versions.filter(v => v.brand_id == brandId) : 
                    this.cache.versions;
            }
            
            this.loading.versions = true;
            try {
                const url = brandId ? `/api/common/versions?brand_id=${brandId}` : '/api/common/versions';
                const response = await fetch(url);
                const data = await response.json();
                
                if (brandId) {
                    // Update cache with brand-specific versions
                    this.cache.versions = this.cache.versions.filter(v => v.brand_id != brandId);
                    this.cache.versions.push(...data);
                } else {
                    this.cache.versions = data;
                }
                
                this.cacheTimestamps[cacheKey] = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading versions:', error);
                return [];
            } finally {
                this.loading.versions = false;
            }
        },
        
        /**
         * Get Reasons
         */
        async getReasons(forceRefresh = false) {
            if (!forceRefresh && this.cache.reasons.length > 0 && this.isCacheValid('reasons')) {
                return this.cache.reasons;
            }
            
            this.loading.reasons = true;
            try {
                const response = await fetch('/api/common/reasons');
                const data = await response.json();
                this.cache.reasons = data;
                this.cacheTimestamps.reasons = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading reasons:', error);
                return [];
            } finally {
                this.loading.reasons = false;
            }
        },
        
        /**
         * Get Customers by Type
         */
        async getCustomers(type = null, forceRefresh = false) {
            const cacheKey = type ? `customers_${type}` : 'customers';
            
            if (!forceRefresh && this.cache.customers.length > 0 && this.isCacheValid(cacheKey)) {
                return type ? 
                    this.cache.customers.filter(c => c.type === type) : 
                    this.cache.customers;
            }
            
            this.loading.customers = true;
            try {
                const url = type ? `/api/customers?type=${type}` : '/api/customers';
                const response = await fetch(url);
                const data = await response.json();
                this.cache.customers = data;
                this.cacheTimestamps[cacheKey] = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading customers:', error);
                return [];
            } finally {
                this.loading.customers = false;
            }
        },
        
        /**
         * Get Cities
         */
        async getCities(forceRefresh = false) {
            if (!forceRefresh && this.cache.cities.length > 0 && this.isCacheValid('cities')) {
                return this.cache.cities;
            }
            
            this.loading.cities = true;
            try {
                const response = await fetch('/api/common/cities');
                const data = await response.json();
                this.cache.cities = data;
                this.cacheTimestamps.cities = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading cities:', error);
                return [];
            } finally {
                this.loading.cities = false;
            }
        },
        
        /**
         * Get Towns by City
         */
        async getTowns(cityId, forceRefresh = false) {
            const cacheKey = `towns_${cityId}`;
            
            if (!forceRefresh && this.isCacheValid(cacheKey)) {
                return this.cache.towns.filter(t => t.city_id == cityId);
            }
            
            this.loading.towns = true;
            try {
                const response = await fetch(`/get_cities?city_id=${cityId}`);
                const data = await response.json();
                const towns = data.towns || [];
                
                // Update cache
                this.cache.towns = this.cache.towns.filter(t => t.city_id != cityId);
                this.cache.towns.push(...towns);
                this.cacheTimestamps[cacheKey] = Date.now();
                
                return towns;
            } catch (error) {
                console.error('Error loading towns:', error);
                return [];
            } finally {
                this.loading.towns = false;
            }
        },
        
        /**
         * Get Currencies
         */
        async getCurrencies(forceRefresh = false) {
            if (!forceRefresh && this.cache.currencies.length > 0 && this.isCacheValid('currencies')) {
                return this.cache.currencies;
            }
            
            this.loading.currencies = true;
            try {
                const response = await fetch('/api/common/currencies');
                const data = await response.json();
                this.cache.currencies = data;
                this.cacheTimestamps.currencies = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading currencies:', error);
                return [];
            } finally {
                this.loading.currencies = false;
            }
        },
        
        /**
         * Get Safes
         */
        async getSafes(forceRefresh = false) {
            if (!forceRefresh && this.cache.safes.length > 0 && this.isCacheValid('safes')) {
                return this.cache.safes;
            }
            
            this.loading.safes = true;
            try {
                const response = await fetch('/api/common/safes');
                const data = await response.json();
                this.cache.safes = data;
                this.cacheTimestamps.safes = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading safes:', error);
                return [];
            } finally {
                this.loading.safes = false;
            }
        },
        
        /**
         * Get Users
         */
        async getUsers(forceRefresh = false) {
            if (!forceRefresh && this.cache.users.length > 0 && this.isCacheValid('users')) {
                return this.cache.users;
            }
            
            this.loading.users = true;
            try {
                const response = await fetch('/api/common/users');
                const data = await response.json();
                this.cache.users = data;
                this.cacheTimestamps.users = Date.now();
                return data;
            } catch (error) {
                console.error('Error loading users:', error);
                return [];
            } finally {
                this.loading.users = false;
            }
        },
        
        /**
         * Preload all common data
         */
        async preloadAllData() {
            const promises = [
                this.getSellers(),
                this.getCategories(),
                this.getWarehouses(),
                this.getColors(),
                this.getBrands(),
                this.getReasons(),
                this.getCities(),
                this.getCurrencies(),
                this.getSafes(),
                this.getUsers()
            ];
            
            try {
                await Promise.all(promises);
            } catch (error) {
                console.error('Error preloading data:', error);
            }
        },
        
        /**
         * Clear all cache
         */
        clearCache() {
            this.cache = {
                sellers: [],
                categories: [],
                warehouses: [],
                colors: [],
                brands: [],
                versions: [],
                reasons: [],
                customers: [],
                cities: [],
                towns: [],
                currencies: [],
                safes: [],
                users: []
            };
            this.cacheTimestamps = {};
        },
        
        /**
         * Add new item to cache
         */
        addToCache(type, item) {
            if (this.cache[type]) {
                this.cache[type].push(item);
            }
        },
        
        /**
         * Update item in cache
         */
        updateInCache(type, item) {
            if (this.cache[type]) {
                const index = this.cache[type].findIndex(i => i.id === item.id);
                if (index !== -1) {
                    this.cache[type][index] = item;
                }
            }
        },
        
        /**
         * Remove item from cache
         */
        removeFromCache(type, itemId) {
            if (this.cache[type]) {
                this.cache[type] = this.cache[type].filter(i => i.id !== itemId);
            }
        }
    };
};
