/**
 * Sales Vue.js App
 * Handles customer search, selection, and stock autocomplete
 * Created: 2025-10-12
 */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof Vue === 'undefined') {
        console.error('Vue.js is not loaded.');
        return;
    }

    const { createApp } = Vue;
    
    console.log('Vue.js loaded successfully');


    // Main Vue App
    createApp({
        data() {
            return {
                customers: window.salesPageData?.customers || [],
                stocks: window.salesPageData?.stocks || [],
                selectedCustomerId: '1',
                customerSearch: '',
                showCustomerDropdown: false,
                filteredCustomers: []
            }
        },
        computed: {
            selectedCustomerName() {
                if (this.selectedCustomerId === '1') return 'Genel Cari';
                const customer = this.customers.find(c => c.id == this.selectedCustomerId);
                return customer ? customer.fullname : 'Müşteri Seçiniz';
            }
        },
        methods: {
            filterCustomers() {
                if (this.customerSearch.length < 1) {
                    this.filteredCustomers = [];
                    return;
                }

                const search = this.customerSearch.toLowerCase();
                this.filteredCustomers = this.customers.filter(customer => {
                    if (customer.type !== 'customer') return false;
                    const fullname = (customer.fullname || '').toLowerCase();
                    const phone = (customer.phone1 || '').toLowerCase();
                    return fullname.includes(search) || phone.includes(search);
                }).slice(0, 10); // Limit to 10 results
            },

            selectCustomer(customer) {
                this.selectedCustomerId = customer.id;
                this.customerSearch = customer.fullname;
                this.showCustomerDropdown = false;
            },

            showDropdown() {
                this.showCustomerDropdown = true;
                this.filterCustomers();
            },

            hideDropdown() {
                setTimeout(() => {
                    this.showCustomerDropdown = false;
                }, 200);
            },

        },
        mounted() {
            // Listen for customer saved event from modal
            window.addEventListener('customerSaved', (event) => {
                const customer = event.detail;
                if (customer && customer.id) {
                    // Add to customers list
                    this.customers.push(customer);

                    // Auto select the new customer
                    this.selectedCustomerId = customer.id;
                    this.customerSearch = customer.fullname || (customer.firstname + ' ' + customer.lastname);

                    console.log('New customer added and selected:', customer);
                }
            });
            
            console.log('Vue.js app mounted with customers:', this.customers.length);
        },
    }).mount('#invoice-sales-app');
});