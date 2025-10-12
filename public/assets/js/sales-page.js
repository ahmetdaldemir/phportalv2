/**
 * Sales Page JavaScript
 * Handles sales invoice functionality
 * Created: 2025-10-12
 */

// Global variables
var currentRowId = null;
var stocksData = [];

// Initialize stock search for all rows
function initStockSearch() {
    // Stock search input handler
    $(document).on('input', '.stock-search-input', function() {
        var $input = $(this);
        var $dropdown = $input.siblings('.stock-dropdown');
        var query = $input.val();
        var rowId = $input.data('rowid');
        
        if (query.length < 2) {
            $dropdown.hide();
            return;
        }
        
        // Filter stocks
        var filteredStocks = stocksData.filter(function(stock) {
            var stockName = (stock.name || '').toLowerCase();
            var brandName = (stock.brand_name || '').toLowerCase();
            var searchTerm = query.toLowerCase();
            return stockName.includes(searchTerm) || brandName.includes(searchTerm);
        }).slice(0, 10);
        
        // Clear and populate dropdown
        $dropdown.empty();
        if (filteredStocks.length > 0) {
            filteredStocks.forEach(function(stock) {
                var $item = $('<div class="stock-item p-2 border-bottom" data-stock-id="' + stock.id + '" style="cursor: pointer;">' + 
                    '<div class="fw-semibold">' + stock.name + '</div>' +
                    '<small class="text-muted">' + stock.brand_name + ' - ' + stock.version_names + '</small>' +
                    '</div>');
                $dropdown.append($item);
            });
            $dropdown.show();
        } else {
            $dropdown.html('<div class="p-3 text-muted text-center"><i class="bx bx-search me-2"></i>Stok bulunamadı</div>');
            $dropdown.show();
        }
    });
    
    // Stock item selection
    $(document).on('click', '.stock-item', function() {
        var $item = $(this);
        var $dropdown = $item.closest('.stock-dropdown');
        var $input = $dropdown.siblings('.stock-search-input');
        var rowId = $input.data('rowid');
        var stockId = $item.data('stock-id');
        var stockText = $item.find('.fw-semibold').text();
        
        // Update input value
        $input.val(stockText);
        
        // Store stock ID in hidden input
        $("#stockId" + rowId).val(stockId);
        
        // Hide dropdown
        $dropdown.hide();
        
        // Open modal to select serial number
        currentRowId = rowId;
        openStockMovementModal(stockId);
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.stock-search-container').length) {
            $('.stock-dropdown').hide();
        }
    });
    
    // Show dropdown on focus if there's text
    $(document).on('focus', '.stock-search-input', function() {
        var $input = $(this);
        var $dropdown = $input.siblings('.stock-dropdown');
        if ($input.val().length >= 2 && $dropdown.find('.stock-item').length > 0) {
            $dropdown.show();
        }
    });
}

// Load stocks data on page load
$(document).ready(function() {
    // Load stocks from window.salesPageData
    if (window.salesPageData && window.salesPageData.stocks) {
        stocksData = window.salesPageData.stocks.map(function(stock) {
            var versionNames = '';
            try {
                if (stock.version_id) {
                    if (Array.isArray(stock.version_id)) {
                        versionNames = stock.version_id.join(', ');
                    } else if (typeof stock.version_id === 'string') {
                        var versions = JSON.parse(stock.version_id);
                        versionNames = Array.isArray(versions) ? versions.join(', ') : versions;
                    }
                }
            } catch(e) {
                versionNames = stock.version_id || '';
            }
            
            return {
                id: stock.id,
                name: stock.name,
                brand_name: stock.brand?.name || 'Bilinmiyor',
                version_names: versionNames
            };
        });
        
        console.log('Stocks loaded:', stocksData.length);
        initStockSearch();
        
        // URL'den gelen serial number ile otomatik stok seçimi
        autoSelectFromURL();
    } else {
        console.error('No stocks data found');
    }
});

// URL'den gelen parametreler ile otomatik stok seçimi
function autoSelectFromURL() {
    var urlParams = new URLSearchParams(window.location.search);
    var serialFromUrl = urlParams.get('serial');
    var stockIdFromUrl = urlParams.get('id');
    
    // Eğer URL'de id varsa stok otomatik seç
    if (stockIdFromUrl) {
        console.log('Stock ID from URL:', stockIdFromUrl);
        
        // Stok bilgisini bul
        var selectedStock = stocksData.find(function(stock) {
            return stock.id == stockIdFromUrl;
        });
        
        if (selectedStock) {
            console.log('Auto-selecting stock:', selectedStock);
            
            // Stok bilgisini stock search input'a doldur
            var stockName = selectedStock.name + ' - ' + selectedStock.brand_name + ' - ' + selectedStock.version_names;
            $("#99999999").find('.stock-search-input').val(stockName);
            $("#stockId99999999").val(selectedStock.id);
            
            // Modal açılmasın, sadece stok seçilsin
            console.log('Stock auto-selected from URL');
        } else {
            console.warn('Stock not found with ID:', stockIdFromUrl);
        }
    }
    
    // Eğer URL'de serial varsa seri numarası otomatik doldur
    if (serialFromUrl && serialFromUrl.length >= 6) {
        console.log('Serial from URL:', serialFromUrl);
        
        // İlk satırın serial input'una değeri doldur
        $("#serialnumber99999999").val(serialFromUrl);
        
        // Backend'den seri numarası kontrolü yap
        var postUrl = window.location.origin + '/serialcheck?id=' + serialFromUrl;
        $.ajax({
            type: "GET",
            url: postUrl,
            success: function (data) {
                if (data.status == false) {
                    console.warn('Serial not found:', data.message);
                    return;
                }
                
                console.log('Auto-select from URL:', data);
                
                // Stok bilgisini stock search input'a doldur
                var stockName = data.sales_price.stock_name || '';
                var stockId = data.sales_price.stock_card_id;
                
                $("#99999999").find('.stock-search-input').val(stockName);
                $("#stockId99999999").val(stockId);
                
                // Fiyatları güncelle
                $("#serial99999999").attr('data-cost', data.sales_price.base_cost_price);
                $("#serial99999999").attr('data-sales', data.sales_price.sale_price);
                $("#serial99999999").val(data.sales_price.sale_price);
                
                // Maliyet fiyatını güncelle
                $("#99999999").find(".invoice-item-cost-price").val(data.sales_price.base_cost_price);

                // Toplam hesapla
                calculateTotal();
                
                console.log('Auto-selection completed from URL');
            },
            error: function(xhr, status, error) {
                console.error('URL serial check error:', error);
            }
        });
    }
}

// Serial number input handler - Barkod girişi için
$("#myList1").on("blur", ".serialnumber", function () {
    var $this = $(this);
    var dataId = $this.data('id');
    var newVal = $this.val();
    var rowId = $this.closest('tr').attr('id');
    
    // Minimum 6 karakter kontrolü
    if (!newVal || newVal.length < 6) {
        return;
    }

    // Duplicate kontrolü
    var Arr = [];
    $('.serialnumber').each(function () {
        if ($(this).val()) {
            Arr.push($(this).val());
        }
    });
    var totalSerial = Arr.filter(x => x == newVal).length;
    if (totalSerial > 1) {
        Swal.fire("Aynı Seri numarası eklenemez");
        $this.val('');
        return false;
    }

    // Backend'den seri numarası kontrolü ve stok bilgisi çekme
    var postUrl = window.location.origin + '/serialcheck?id=' + newVal;
    $.ajax({
        type: "GET",
        url: postUrl,
        success: function (data) {
            if (data.status == false) {
                Swal.fire(data.message);
                $this.val('');
                return false;
            }
            
            console.log('Serial check response:', data);
            
            // Stok bilgisini stock search input'a doldur
            var stockName = data.sales_price.stock_name || '';
            $("#" + rowId).find('.stock-search-input').val(stockName);
            $("#stockId" + rowId).val(data.sales_price.stock_card_id);
            
            // Fiyatları güncelle - Doğru ID kullan
            $("#serial" + rowId).attr('data-cost', data.sales_price.base_cost_price);
            $("#serial" + rowId).attr('data-sales', data.sales_price.sale_price);
            $("#serial" + rowId).val(data.sales_price.sale_price);
            
            // Maliyet fiyatını güncelle
            $("#" + rowId).find(".invoice-item-cost-price").val(data.sales_price.base_cost_price);

            // Toplam hesapla
            calculateTotal();
        },
        error: function(xhr, status, error) {
            console.error('Serial check error:', error);
            Swal.fire('Seri numarası kontrol edilemedi', '', 'error');
        }
    });
});

// Initial serial number check with event delegation
$(document).ready(function () {
    $(document).on('keyup', 'input[name="serial[]"]', function() {
        var serialInput = $(this);
        var postUrl = window.location.origin + '/serialcheck?id=' + serialInput.val() + '';
        $.ajax({
            type: "GET",
            url: postUrl,
            success: function (data) {
                if (data.status === false) {
                    $("#saveButton").attr('disabled', true);
                } else {
                    // Aynı satırdaki sales price input'unu bul
                    var row = serialInput.closest('tr');
                    var salesPriceInput = row.find('.invoice-item-sales-price');
                    var costPriceInput = row.find('.invoice-item-cost-price');
                    var stockSearchInput = row.find('.stock-search-input');
                    var stockIdInput = row.find('input[name="stock_card_id[]"]');
                    
                    // Fiyatları güncelle
                    salesPriceInput.val(data.sales_price.sale_price);
                    salesPriceInput.attr('sales', data.sales_price.sale_price);
                    salesPriceInput.attr('data-sales', data.sales_price.sale_price);
                    
                    // Maliyet fiyatını güncelle (eğer data'da varsa)
                    if (data.sales_price.base_cost_price) {
                        costPriceInput.val(data.sales_price.base_cost_price);
                        salesPriceInput.attr('data-cost', data.sales_price.base_cost_price);
                    }
                    
                    // Stock card'ı bul ve autocomplete'e doldur
                    if (data.sales_price.stock_card_id && typeof stocksData !== 'undefined') {
                        var selectedStock = stocksData.find(function(stock) {
                            return stock.id == data.sales_price.stock_card_id;
                        });
                        
                        if (selectedStock) {
                            // Stock bilgisini autocomplete input'a doldur
                            var stockName = selectedStock.name;
                            if (selectedStock.brand_name) {
                                stockName += ' - ' + selectedStock.brand_name;
                            }
                            if (selectedStock.version_names) {
                                stockName += ' - ' + selectedStock.version_names;
                            }
                            
                            stockSearchInput.val(stockName);
                            stockIdInput.val(selectedStock.id);
                            
                            console.log('Stock auto-filled from serial:', {
                                serial: serialInput.val(),
                                stock_card_id: data.sales_price.stock_card_id,
                                stock_name: stockName
                            });
                        } else {
                            console.warn('Stock not found in stocksData with ID:', data.sales_price.stock_card_id);
                        }
                    }
                    
                    $('#saveButton').prop('disabled', false);
                    
                    calculateTotal();
                }
            }
        });
    });

    // Discount input handler
    $("#myList1").on("change", "#discountInput", function () {
        var max = $(this).attr('max');
        var newID = $(this).data('newid');
        var salesprice = $("#" + newID).find('#serial' + newID).data('sales');
        var baseCostprice = $("#" + newID).find('#serial' + newID).data('cost');
        var discount = $(this).val();

        if (discount > 0) {
            var newSalesPrice = salesprice - ((discount * salesprice) / 100);
            console.log(discount, max);
            if (parseInt(discount) > parseInt(max)) {
                Swal.fire('İndirim oranı max değerden fazla olamaz');
                $(this).val('');
            } else {
                if (newSalesPrice > baseCostprice) {
                    $("#" + newID).find('#serial' + newID).val(Math.round(newSalesPrice));
                    calculateTotal();
                } else {
                    Swal.fire('Destekli Satış Fiyatı altına satılamaz');
                    $(this).val('');
                }
            }
        } else if (discount == 0) {
            // İndirim 0 ise orijinal fiyata dön
            $("#" + newID).find('#serial' + newID).val(salesprice);
            calculateTotal();
        } else {
            $(this).val('');
            return false;
        }
    })
});

// Validate serial number
function validateSerial(array) {
    let serialError = true;
    let hasEmptySerial = false;
    
    // Tüm serial input'larını kontrol et
    $('input[name="serial[]"]').each(function() {
        if ($(this).val().length === 0) {
            hasEmptySerial = true;
            return false; // break loop
        }
    });
    
    if (hasEmptySerial) {
        $("#serialcheck").show();
        serialError = false;
        return false;
    } else {
        $("#serialcheck").hide();
    }
}

// Get customer details
function getCustomer(id) {
    var postUrl = window.location.origin + '/custom_customerget?id=' + id + '';
    $.ajax({
        type: "POST",
        url: postUrl,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        encode: true,
    }).done(function (data) {
        $(".customerinformation").html('<p className="mb-1">' + data.address + '</p><p className="mb-1">' + data.phone1 + '</p><p className="mb-1">' + data.email + '</p>');
    });
}

// Save sales invoice
function save() {
    if ($("select.StaffIdClass").length === 0 || $("select.StaffIdClass").val().length <= 0) {
        alert("Personel Seçimi Yapmadınız");
        return false;
    }
    // Tüm serial input'ları kontrol et
    let hasEmptySerial = false;
    $('input[name="serial[]"]').each(function() {
        if ($(this).val().length === 0) {
            hasEmptySerial = true;
            return false; // break loop
        }
    });
    
    if (hasEmptySerial || $('input[name="serial[]"]').length === 0) {
        alert("Seri Seçimi Yapmadınız");
        return false;
    }

    var arrayNew = []
    $('input.serialnumber').each(function (index, elem) {
        var xyz = $(elem).val();
        arrayNew.push(xyz);
    });
    validateSerial(arrayNew);

    $("#saveButton").prop("disabled", true);

    var postUrl = window.location.origin + '/invoice/salesstore';
    $.ajax({
        type: "POST",
        url: postUrl,
        data: $("#invoiceForm").serialize(),
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        encode: true,
        beforeSend: function () {
            $('#loader').removeClass('display-none')
        },
        success: function (data) {
            Swal.fire(data);
            window.location.href = salesIndexRoute;
        },
        error: function (xhr) {
            Swal.fire(xhr.responseJSON, '', "error");
            $("#saveButton").prop("disabled", false);
        },
        complete: function () {
            $("#saveButton").prop("disabled", true);
        },
    });
}

// Payment status change handler
$("#paymentStatus").change(function () {
    var type = $(this).val();
    if (type == 'paid') {
        $("#safeArea").html(safeAreaHTML);
    } else if (type == 'paidOutOfPocket') {
        $("#safeArea").html(paidOutOfPocketHTML);
    } else {
        $("#safeArea").html('');
    }
})

// Add new invoice item row
function myFunction() {
    var rand = Math.floor(Math.random() * 100000);
    
    const node = document.getElementById("99999999");
    const clone = node.cloneNode(true);
    clone.setAttribute('id', rand);
    document.getElementById("myList1").appendChild(clone);
    
    // Update IDs and attributes
    $("#" + rand).find('#removeDiv').attr('data-id', rand);
    $("#" + rand).find('.serialnumber').attr('data-id', 'serialnumber' + rand);
    $("#" + rand).find('.serialnumber').attr('id', 'serialnumber' + rand);
    $("#" + rand).find('.invoice-item-sales-price').attr('id', "serial" + rand);
    $("#" + rand).find('input[name="stock_card_id[]"]').attr('id', "stockId" + rand);
    $("#" + rand).find('.invoice-item-cost-price').attr('data-newid', rand);
    $("#" + rand).find('#discountInput').attr('data-newid', rand);
    
    // Update stock search elements
    $("#" + rand).find('.stock-search-input').attr('data-rowid', rand);
    $("#" + rand).find('input[name="stock_card_id[]"]').attr('id', "stockId" + rand);
    
    // Clear all input values
    $("#" + rand).find('input:text').val('');
    $("#" + rand).find('input[type="number"]').val('');
    $("#" + rand).find('input[type="hidden"][name="stock_card_id[]"]').val('');
    
    window.scrollBy(0, 400);
}

// Remove invoice item row
$(document).on("click", "#removeDiv", function () {
    var Divid = $(this).data('id');
    console.log('Removing row:', Divid);
    $("#" + Divid).remove();
    calculateTotal();
});


// Open stock movement modal and load data
function openStockMovementModal(stockId) {
    // Open modal
    var modal = new bootstrap.Modal(document.getElementById('stockMovementModal'));
    modal.show();

    // Show loader
    $('#stockMovementLoader').show();
    $('#stockMovementContent').hide();
    $('#noDataMessage').hide();

    // Fetch stock movements via AJAX
    $.ajax({
        url: '/getStockMovementList',
        type: 'GET',
        data: {
            id: stockId,
            serialNumber: 'undefined',
            seller: 'all',
            color: 'undefined'
        },
        success: function (response) {
            $('#stockMovementLoader').hide();

            if (response.data && response.data.length > 0) {
                // Filter available stock (type 1 or 4)
                var availableStock = response.data.filter(function (item) {
                    return item.type == 1 || item.type == 4;
                });

                if (availableStock.length > 0) {
                    renderStockMovementTable(availableStock);
                    $('#stockMovementContent').show();
                } else {
                    $('#noDataMessage').show();
                }
            } else {
                $('#noDataMessage').show();
            }
        },
        error: function (xhr, status, error) {
            $('#stockMovementLoader').hide();
            console.error('Stok hareketleri yüklenemedi:', error);
            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: 'Stok hareketleri yüklenemedi!'
            });
        }
    });
}

// Render stock movement table
function renderStockMovementTable(data) {
    var tbody = $('#stockMovementTableBody');
    tbody.empty();

    data.forEach(function (item, index) {
        var salePrice = item.sale_price ? String(item.sale_price).replace(',', '') : '0';
        var costPrice = item.cost_price ? String(item.cost_price).replace(',', '') : '0';
        var baseCostPrice = item.base_cost_price ? String(item.base_cost_price).replace(',', '') : '0';
        
        var row = `
            <tr class="stock-movement-row" style="cursor: pointer;">
                <td><strong>${item.serial_number || '-'}</strong></td>
                <td>${item.color_name || '-'}</td>
                <td><span class="badge bg-success">${salePrice} ₺</span></td>
                <td>${baseCostPrice} ₺</td>
                <td>${item.seller_name || '-'}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary select-stock-movement"
                            data-id="${item.id}"
                            data-serial="${item.serial_number || ''}"
                            data-sale-price="${salePrice}"
                            data-cost-price="${costPrice}"
                            data-base-cost-price="${baseCostPrice}"
                            data-color-id="${item.color_id || ''}"
                            data-color-name="${item.color_name || ''}">
                        <i class="bx bx-check me-1"></i>Seç
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Stock movement selection handler - Modal'dan seri numarası seçildiğinde
$(document).on('click', '.select-stock-movement', function () {
    var selectedData = {
        id: $(this).data('id'),
        serial: $(this).data('serial'),
        salePrice: parseFloat($(this).data('sale-price')) || 0,
        costPrice: parseFloat($(this).data('cost-price')) || 0,
        baseCostPrice: parseFloat($(this).data('base-cost-price')) || 0,
        colorId: $(this).data('color-id'),
        colorName: $(this).data('color-name')
    };

    if (currentRowId) {
        console.log('Selected from modal:', selectedData);
        
        // Seri numarasını doldur - Doğru ID kullan
        $("#serialnumber" + currentRowId).val(selectedData.serial);

        // Satış fiyatı güncelle - Doğru ID kullan
        $("#serial" + currentRowId).val(selectedData.salePrice);
        $("#serial" + currentRowId).attr('data-sales', selectedData.salePrice);
        $("#serial" + currentRowId).attr('data-cost', selectedData.baseCostPrice);

        // Maliyet fiyatını güncelle
        $("#" + currentRowId).find('.invoice-item-cost-price').val(selectedData.baseCostPrice);

        // İndirim inputunu sıfırla
        $("#" + currentRowId).find('#discountInput').val('');

        // Genel toplam hesapla
        calculateTotal();
    }

    // Modal'ı kapat
    var modal = bootstrap.Modal.getInstance(document.getElementById('stockMovementModal'));
    if (modal) {
        modal.hide();
    }
});

// Sales price change handler
$("#myList1").on("change", ".invoice-item-sales-price", function () {
    calculateTotal();
});

// Calculate total
function calculateTotal() {
    var sum = 0;
    $('.invoice-item-sales-price').each(function () {
        var price = parseFloat($(this).val()) || 0;
        sum += price;
    });
    $("#totalArea").html(sum.toFixed(2) + "₺");
}

