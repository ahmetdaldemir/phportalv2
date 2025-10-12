/**
 * Global Vue.js Mixin for Common Functionality
 * Bu mixin tüm Vue component'lerde kullanılabilir
 */

import { useGlobalStore } from '../stores/globalStore.js';

export const GlobalMixin = {
    data() {
        return {
            // Global store instance
            globalStore: useGlobalStore(),
            
            // Common loading states
            commonLoading: {
                sellers: false,
                categories: false,
                warehouses: false,
                colors: false,
                brands: false,
                versions: false,
                reasons: false,
                customers: false
            }
        }
    },
    
    computed: {
        // Computed properties for easy access
        sellers() {
            return this.globalStore.cache.sellers;
        },
        
        categories() {
            return this.globalStore.cache.categories;
        },
        
        parentCategories() {
            return this.categories.filter(c => c.parent_id == 0 || !c.parent_id);
        },
        
        warehouses() {
            return this.globalStore.cache.warehouses;
        },
        
        colors() {
            return this.globalStore.cache.colors;
        },
        
        brands() {
            return this.globalStore.cache.brands;
        },
        
        versions() {
            return this.globalStore.cache.versions;
        },
        
        reasons() {
            return this.globalStore.cache.reasons;
        },
        
        customers() {
            return this.globalStore.cache.customers;
        },
        
        accountCustomers() {
            return this.customers.filter(c => c.type === 'account');
        },
        
        regularCustomers() {
            return this.customers.filter(c => c.type === 'customer');
        },
        
        cities() {
            return this.globalStore.cache.cities;
        },
        
        towns() {
            return this.globalStore.cache.towns;
        },
        
        currencies() {
            return this.globalStore.cache.currencies;
        },
        
        safes() {
            return this.globalStore.cache.safes;
        },
        
        users() {
            return this.globalStore.cache.users;
        }
    },
    
    methods: {
        /**
         * Load all common data - her component'te kullanılabilir
         */
        async loadCommonData(modules = []) {
            const loadPromises = [];
            
            // Eğer modules belirtilmemişse hepsini yükle
            if (modules.length === 0) {
                modules = ['sellers', 'categories', 'warehouses', 'colors', 'brands', 'reasons'];
            }
            
            // Sadece istenen modülleri yükle
            modules.forEach(module => {
                switch(module) {
                    case 'sellers':
                        loadPromises.push(this.globalStore.getSellers());
                        break;
                    case 'categories':
                        loadPromises.push(this.globalStore.getCategories());
                        break;
                    case 'warehouses':
                        loadPromises.push(this.globalStore.getWarehouses());
                        break;
                    case 'colors':
                        loadPromises.push(this.globalStore.getColors());
                        break;
                    case 'brands':
                        loadPromises.push(this.globalStore.getBrands());
                        break;
                    case 'versions':
                        loadPromises.push(this.globalStore.getVersions());
                        break;
                    case 'reasons':
                        loadPromises.push(this.globalStore.getReasons());
                        break;
                    case 'customers':
                        loadPromises.push(this.globalStore.getCustomers());
                        break;
                    case 'cities':
                        loadPromises.push(this.globalStore.getCities());
                        break;
                    case 'currencies':
                        loadPromises.push(this.globalStore.getCurrencies());
                        break;
                    case 'safes':
                        loadPromises.push(this.globalStore.getSafes());
                        break;
                    case 'users':
                        loadPromises.push(this.globalStore.getUsers());
                        break;
                }
            });
            
            try {
                await Promise.all(loadPromises);
            } catch (error) {
                console.error('Error loading common data:', error);
            }
        },
        
        /**
         * Get versions by brand ID
         */
        getVersionsByBrand(brandId) {
            return this.versions.filter(v => v.brand_id == brandId);
        },
        
        /**
         * Get child categories
         */
        getChildCategories(parentId) {
            return this.categories.filter(c => c.parent_id == parentId);
        },
        
        /**
         * Get towns by city ID
         */
        getTownsByCity(cityId) {
            return this.towns.filter(t => t.city_id == cityId);
        },
        
        /**
         * Currency formatting
         */
        formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY'
            }).format(amount || 0);
        },
        
        /**
         * Date formatting
         */
        formatDate(date) {
            return new Date(date).toLocaleDateString('tr-TR');
        },
        
        /**
         * Find item by ID from cache
         */
        findFromCache(type, id) {
            const cache = this.globalStore.cache[type];
            return cache ? cache.find(item => item.id == id) : null;
        },
        
        /**
         * Refresh specific cache
         */
        async refreshCache(type) {
            switch(type) {
                case 'sellers':
                    return await this.globalStore.getSellers(true);
                case 'categories':
                    return await this.globalStore.getCategories(true);
                case 'warehouses':
                    return await this.globalStore.getWarehouses(true);
                case 'colors':
                    return await this.globalStore.getColors(true);
                case 'brands':
                    return await this.globalStore.getBrands(true);
                case 'reasons':
                    return await this.globalStore.getReasons(true);
                case 'customers':
                    return await this.globalStore.getCustomers(null, true);
                case 'cities':
                    return await this.globalStore.getCities(true);
                case 'currencies':
                    return await this.globalStore.getCurrencies(true);
                case 'safes':
                    return await this.globalStore.getSafes(true);
                case 'users':
                    return await this.globalStore.getUsers(true);
                default:
            }
        }
    },
    
    created() {
        // Component oluşturulduğunda otomatik olarak common data'yı yükle
        // Bu, her component'te ayrı ayrı yazmamızı engeller
        if (this.$options.commonModules) {
            this.loadCommonData(this.$options.commonModules);
        }
    }
};
