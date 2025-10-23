/**
 * Sales Vue.js App
 * Handles customer search, selection, stock autocomplete, and invoice items
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
    const app = createApp({
        data() {
            // Process stocks data
            const processStocksData = (stocks) => {
                if (!stocks || !Array.isArray(stocks)) return [];
                return stocks.map(stock => {
                    let versionNames = '';
                    try {
                        if (stock.version_id) {
                            if (Array.isArray(stock.version_id)) {
                                versionNames = stock.version_id.join(', ');
                            } else if (typeof stock.version_id === 'string') {
                                const versions = JSON.parse(stock.version_id);
                                versionNames = Array.isArray(versions) ? versions.join(', ') : versions;
                            }
                        }
                    } catch(e) {
                        versionNames = stock.version_id || '';
                    }
                    
                    return {
                        id: stock.id,
                        name: stock.name,
                        brand: stock.brand,
                        version_names: versionNames
                    };
                });
            };

            // Ensure window.salesPageData exists
            const salesData = window.salesPageData || {};

            return {
                customers: salesData.customers || [],
                stocks: processStocksData(salesData.stocks || []),
                selectedCustomerId: '1',
                customerSearch: '',
                showCustomerDropdown: false,
                filteredCustomers: [],
                invoiceItems: [],
                currentRowId: 99999999,
                totalAmount: 0,
                stockSearchQueries: {},
                stockDropdowns: {},
                isLoaded: false
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

            // Invoice Items Management
            addInvoiceItem() {
                const newId = Math.floor(Math.random() * 100000);
                const newItem = {
                    id: newId,
                    stockCardId: '',
                    stockName: '',
                    serialNumber: '',
                    salePrice: 0,
                    costPrice: 0,
                    discount: 0,
                    description: '',
                    reasonId: 4
                };
                this.invoiceItems.push(newItem);
                this.currentRowId = newId;
                
                // Scroll to new item
                setTimeout(() => {
                    window.scrollBy(0, 400);
                }, 100);
            },

            removeInvoiceItem(itemId) {
                const index = this.invoiceItems.findIndex(item => item.id === itemId);
                if (index > -1) {
                    this.invoiceItems.splice(index, 1);
                    this.calculateTotal();
                }
            },

            // Stock Search
            filterStocks(rowId, query) {
                if (query.length < 2) {
                    this.stockDropdowns[rowId] = [];
                    return;
                }

                const search = query.toLowerCase();
                const filtered = this.stocks.filter(stock => {
                    const stockName = (stock.name || '').toLowerCase();
                    const brandName = (stock.brand?.name || '').toLowerCase();
                    return stockName.includes(search) || brandName.includes(search);
                }).slice(0, 10);

                this.stockDropdowns[rowId] = filtered;
            },

            selectStock(rowId, stock) {
                const item = this.invoiceItems.find(item => item.id === rowId);
                if (item) {
                    item.stockCardId = stock.id;
                    item.stockName = stock.name + ' - ' + (stock.brand?.name || '') + ' - ' + (stock.version_names || '');
                    this.stockSearchQueries[rowId] = item.stockName;
                    this.stockDropdowns[rowId] = [];
                    
                    // Open stock movement modal
                    this.openStockMovementModal(stock.id, rowId);
                }
            },

            // Serial Number Validation
            async validateSerialNumber(rowId, serialNumber) {
                if (!serialNumber || serialNumber.length < 6) {
                    return;
                }

                // Check for duplicates
                const duplicates = this.invoiceItems.filter(item => 
                    item.serialNumber === serialNumber && item.id !== rowId
                );
                if (duplicates.length > 0) {
                    Swal.fire("Aynı Seri numarası eklenemez");
                    const item = this.invoiceItems.find(item => item.id === rowId);
                    if (item) item.serialNumber = '';
                    return;
                }

                try {
                    const response = await fetch(`/serialcheck?id=${serialNumber}`);
                    const data = await response.json();
                    
                    if (!data.status) {
                        Swal.fire(data.message);
                        const item = this.invoiceItems.find(item => item.id === rowId);
                        if (item) item.serialNumber = '';
                        return;
                    }

                    // Update item with stock data
                    const item = this.invoiceItems.find(item => item.id === rowId);
                    if (item) {
                        item.stockCardId = data.stock_card_id;
                        item.salePrice = parseFloat(data.sales_price) || 0;
                        item.costPrice = parseFloat(data.base_cost_price) || 0;
                        item.serialNumber =  data.serial_number;
                        console.log(data);
                        // Find and set stock name
                        const stock = this.stocks.find(s => s.id == data.stock_card_id);
                        if (stock) {
                            item.stockName = stock.name + ' - ' + (stock.brand?.name || '') + ' - ' + (stock.version_names || '');
                            this.stockSearchQueries[rowId] = item.stockName;
                        }
                    }

                    this.calculateTotal();
                } catch (error) {
                    console.error('Serial check error:', error);
                    Swal.fire('Seri numarası kontrol edilemedi', '', 'error');
                }
            },

            // Discount Calculation
            applyDiscount(rowId, discountPercent) {
                const item = this.invoiceItems.find(item => item.id === rowId);
                if (!item) return;

                const maxDiscount = this.isAdmin ? 50 : 30; // Admin kontrolü gerekli
                
                if (discountPercent > maxDiscount) {
                    Swal.fire('İndirim oranı max değerden fazla olamaz');
                    item.discount = 0;
                    return;
                }

                if (discountPercent > 0) {
                    const originalPrice = item.salePrice;
                    const newPrice = originalPrice - ((discountPercent * originalPrice) / 100);
                    
                    if (newPrice > item.costPrice) {
                        item.salePrice = Math.round(newPrice);
                    } else {
                        Swal.fire('Destekli Satış Fiyatı altına satılamaz');
                        item.discount = 0;
                        return;
                    }
                } else {
                    // Reset to original price - burada orijinal fiyatı saklamamız gerekiyor
                    // Şimdilik basit bir çözüm
                }

                this.calculateTotal();
            },

            // Total Calculation
            calculateTotal() {
                this.totalAmount = this.invoiceItems.reduce((total, item) => {
                    return total + (parseFloat(item.salePrice) || 0);
                }, 0);
            },

            // Stock Movement Modal
            openStockMovementModal(stockId, rowId) {
                // Bu fonksiyon jQuery modal'ı açacak
                // Şimdilik basit bir implementasyon
                console.log('Opening stock movement modal for stock:', stockId, 'row:', rowId);
            },

            // Save Invoice
            async saveInvoice() {
                // Validation

                const staffEl = document.getElementById('staff_id_select');
                const staffVal = (typeof $ !== 'undefined' && typeof $.fn !== 'undefined') ? $('#staff_id_select').val() : (staffEl ? staffEl.value : '');
                if (!staffVal) {
                    alert("Personel Seçimi Yapmadınız");
                    return;
                }
                if (!this.selectedCustomerId || this.selectedCustomerId === '') {
                    alert("Müşteri Seçimi Yapmadınız");
                    return;
                }

                if (this.invoiceItems.length === 0 || this.invoiceItems.some(item => !item.serialNumber)) {
                    alert("Seri Seçimi Yapmadınız");
                    return;
                }

                // Form data preparation
                const formData = new FormData();
                formData.append('customer_id', this.selectedCustomerId);
                
                this.invoiceItems.forEach((item, index) => {
                    formData.append(`stock_card_id[${index}]`, item.stockCardId);
                    formData.append(`serial[${index}]`, item.serialNumber);
                    formData.append(`sale_price[${index}]`, item.salePrice);
                    formData.append(`base_cost_price[${index}]`, item.costPrice);
                    formData.append(`discount[${index}]`, item.discount);
                    formData.append(`description[${index}]`, item.description);
                    formData.append(`reason_id[${index}]`, item.reasonId);
                });

                // Add other form fields
                const form = document.getElementById('invoiceForm');
                const formElements = form.querySelectorAll('input, select, textarea');
                formElements.forEach(element => {
                    if (element.name && !element.name.includes('[]')) {
                        formData.append(element.name, element.value);
                    }
                });

                try {
                    const response = await fetch('/invoice/salesstore', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    Swal.fire(result);
                    window.location.href = window.salesPageData.salesIndexRoute;
                } catch (error) {
                    console.error('Save error:', error);
                    Swal.fire('Kaydetme sırasında hata oluştu', '', 'error');
                }
            },

            // URL'den gelen parametreler ile otomatik doldurma
            autoSelectFromURL() {
                const urlParams = new URLSearchParams(window.location.search);
                const serialFromUrl = urlParams.get('serial');
                const stockIdFromUrl = urlParams.get('id');
                
                // Eğer URL'de id varsa stok otomatik seç
                if (stockIdFromUrl) {
                    console.log('Stock ID from URL:', stockIdFromUrl);
                    
                    const selectedStock = this.stocks.find(stock => stock.id == stockIdFromUrl);
                    
                    if (selectedStock && this.invoiceItems.length > 0) {
                        const firstItem = this.invoiceItems[0];
                        firstItem.stockCardId = selectedStock.id;
                        firstItem.stockName = selectedStock.name + ' - ' + (selectedStock.brand?.name || '') + ' - ' + (selectedStock.version_names || '');
                        this.stockSearchQueries[firstItem.id] = firstItem.stockName;
                    }
                }
                
                // Eğer URL'de serial varsa seri numarası otomatik doldur
                if (serialFromUrl && serialFromUrl.length >= 6 && this.invoiceItems.length > 0) {
                    console.log('Serial from URL:', serialFromUrl);
                    this.invoiceItems[0].serialNumber = serialFromUrl;
                    this.validateSerialNumber(this.invoiceItems[0].id, serialFromUrl);
                }
            }
        },
        mounted() {
            try {
                // Initialize first invoice item
                const firstItem = {
                    id: 99999999,
                    stockCardId: '',
                    stockName: '',
                    serialNumber: '',
                    salePrice: 0,
                    costPrice: 0,
                    discount: 0,
                    description: '',
                    reasonId: 4
                };
                this.invoiceItems.push(firstItem);
                
                // URL'den gelen parametreler ile otomatik doldurma
                this.autoSelectFromURL();
                
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
                
                this.isLoaded = true;
                console.log('Vue.js app mounted with customers:', this.customers.length);
                console.log('Vue.js app mounted with stocks:', this.stocks.length);
                console.log('Vue.js app mounted with invoice items:', this.invoiceItems.length);
            } catch (error) {
                console.error('Error in Vue.js mounted:', error);
            }
        },
    });
    
    // Mount the app immediately
    try {
        const appElement = document.getElementById('invoice-sales-app');
        if (appElement) {
            app.mount('#invoice-sales-app');
            console.log('Vue.js app mounted successfully');
        } else {
            console.error('Invoice sales app element not found');
        }
    } catch (error) {
        console.error('Error mounting Vue.js app:', error);
    }
    
    // Global app instance for debugging
    window.salesVueApp = app;
});