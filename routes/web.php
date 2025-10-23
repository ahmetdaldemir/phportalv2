<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// logout route is already included in Auth::routes()
// Custom logout route (overrides Auth::routes logout if needed)
Route::get('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');

// AJAX endpoints - performans optimizasyonu
Route::get('/api/stocks', [App\Http\Controllers\HomeController::class, 'getStocksAjax'])->name('stocks.ajax');
Route::get('/api/colors', [App\Http\Controllers\HomeController::class, 'getColorsAjax'])->name('colors.ajax');
Route::get('/api/reasons', [App\Http\Controllers\HomeController::class, 'getReasonsAjax'])->name('reasons.ajax');

// Common Data API - Web routes for Vue.js (with session auth)
Route::middleware(['auth', 'companies'])->prefix('api/common')->group(function () {
    Route::get('/sellers', function() {
        return response()->json(app(\App\Services\Seller\SellerService::class)->get());
    });
    Route::get('/categories', function() {
        return response()->json(app(\App\Services\Category\CategoryService::class)->get());
    });
    Route::get('/warehouses', function() {
        return response()->json(app(\App\Services\Warehouse\WarehouseService::class)->get());
    });
    Route::get('/colors', function() {
        return response()->json(app(\App\Services\Color\ColorService::class)->get());
    });
    Route::get('/brands', function() {
        return response()->json(app(\App\Services\Brand\BrandService::class)->get());
    });
    Route::get('/versions', function() {
        $brandId = request('brand_id');
        $versions = app(\App\Services\Version\VersionService::class)->get();
        if ($brandId) {
            $versions = $versions->filter(function($version) use ($brandId) {
                return $version->brand_id == $brandId;
            });
        }
        return response()->json($versions->values());
    });
    Route::get('/reasons', function() {
        return response()->json(app(\App\Services\Reason\ReasonService::class)->get());
    });
    Route::get('/customers', function() {
        $type = request('type');
        $customers = app(\App\Services\Customer\CustomerService::class)->all();
        if ($type) {
            $customers = $customers->filter(function($customer) use ($type) {
                return $customer->type === $type;
            });
        }
        return response()->json($customers->values());
    });
    Route::get('/cities', function() {
        return response()->json(\App\Models\City::all());
    });
    Route::get('/towns', function() {
        $cityId = request('city_id');
        return response()->json(\App\Models\Town::where('city_id', $cityId)->get());
    });
    Route::get('/currencies', function() {
        return response()->json(\App\Models\Currency::all());
    });
    Route::get('/safes', function() {
        return response()->json(app(\App\Services\Safe\SafeService::class)->all());
    });
    Route::get('/users', function() {
        return response()->json(app(\App\Services\User\UserService::class)->get());
    });
});

// Sale AJAX endpoints - middleware dışında
Route::get('/sale/ajax', [App\Http\Controllers\SaleController::class, 'getSalesAjax'])->name('sale.ajax');
Route::get('/sale/versions-ajax', [App\Http\Controllers\SaleController::class, 'getVersionsAjax'])->name('sale.versions.ajax');

// Phone AJAX endpoints - middleware dışında
Route::get('/phone/ajax', [App\Http\Controllers\PhoneController::class, 'getPhonesAjax'])->name('phone.ajax');
Route::get('/phone/versions-ajax', [App\Http\Controllers\PhoneController::class, 'getVersionsAjax'])->name('phone.versions.ajax');

// StockCard AJAX endpoints - middleware dışında
Route::get('/stockcard/movements-ajax', [App\Http\Controllers\StockCardController::class, 'getMovementsAjax'])->name('stockcard.movements.ajax');


// Auth routes already defined above (line 21)
// Removed duplicate Auth::routes()

Route::middleware(['companies'])->group(function () {

    // /home route already defined above (line 23)
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboardReport', [App\Http\Controllers\HomeController::class, 'dashboardReport'])->name('dashboardReport');
    Route::get('/dashboardNewReport', [App\Http\Controllers\HomeController::class, 'dashboardNewReport'])->name('dashboardNewReport');
    Route::get('/dashboardMounthNewReport', [App\Http\Controllers\HomeController::class, 'dashboardMounthNewReport'])->name('dashboardMounthNewReport');

    Route::prefix('seller')->name('seller.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\SellerController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\SellerController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\SellerController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\SellerController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\SellerController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\SellerController::class, 'update'])->name('update');
    });


    Route::prefix('company')->name('company.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\CompanyController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\CompanyController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\CompanyController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\CompanyController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\CompanyController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\CompanyController::class, 'update'])->name('update');
    });

    Route::prefix('role')->name('role.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\RoleController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\RoleController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\RoleController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\RoleController::class, 'update'])->name('update');
        Route::get('permission', [App\Http\Controllers\RoleController::class, 'permission'])->name('permission');
    });

    Route::prefix('permission')->name('permission.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\PermissionController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\PermissionController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\PermissionController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\PermissionController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\PermissionController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\PermissionController::class, 'update'])->name('update');
    });

    Route::prefix('user')->name('user.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\UserController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::post('fieldUpdate', [App\Http\Controllers\UserController::class, 'fieldUpdate'])->name('fieldUpdate');
        Route::post('change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('changePassword');
    });

    Route::prefix('category')->name('category.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\CategoryController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\CategoryController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\CategoryController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\CategoryController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\CategoryController::class, 'update'])->name('update');
    });

    Route::prefix('brand')->name('brand.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\BrandController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\BrandController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\BrandController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\BrandController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\BrandController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\BrandController::class, 'update'])->name('update');
        Route::post('technical', [App\Http\Controllers\BrandController::class, 'technical'])->name('technical');
    });

    Route::prefix('version')->name('version.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\VersionController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\VersionController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\VersionController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\VersionController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\VersionController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\VersionController::class, 'update'])->name('update');
        Route::post('technical', [App\Http\Controllers\VersionController::class, 'technical'])->name('technical');

    });

    Route::prefix('bank')->name('bank.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\BankController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\BankController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\BankController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\BankController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\BankController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\BankController::class, 'update'])->name('update');
    });

    Route::prefix('safe')->name('safe.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\SafeController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\SafeController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\SafeController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\SafeController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\SafeController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\SafeController::class, 'update'])->name('update');
    });

    Route::prefix('customer')->name('customer.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\CustomerController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\CustomerController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\CustomerController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\CustomerController::class, 'update'])->name('update');
        Route::post('updateDanger', [App\Http\Controllers\CustomerController::class, 'updateDanger'])->name('updateDanger');
    });

    Route::prefix('site_customer')->name('site_customer.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\SiteCustomerController::class, 'index'])->name('index');
        Route::get('detail', [App\Http\Controllers\SiteCustomerController::class, 'detail'])->name('detail');
        Route::get('profil', [App\Http\Controllers\SiteCustomerController::class, 'profil'])->name('profil');

        Route::get('delete', [App\Http\Controllers\SiteCustomerController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\SiteCustomerController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\SiteCustomerController::class, 'store'])->name('store');
        Route::post('updateDanger', [App\Http\Controllers\SiteCustomerController::class, 'updateDanger'])->name('updateDanger');
    });

    Route::prefix('color')->name('color.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\ColorController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\ColorController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\ColorController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\ColorController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\ColorController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\ColorController::class, 'update'])->name('update');
    });

    Route::prefix('warehouse')->name('warehouse.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\WarehouseController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\WarehouseController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\WarehouseController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\WarehouseController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\WarehouseController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\WarehouseController::class, 'update'])->name('update');
    });

    Route::prefix('stockcard')->name('stockcard.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\StockCardController::class, 'index'])->name('index');
        Route::get('export', [App\Http\Controllers\StockCardController::class, 'exportToExcel'])->name('export');
        Route::post('/search-ajax', [App\Http\Controllers\StockCardController::class, 'searchAjax'])->name('search.ajax');
        Route::get('/brands-ajax', [App\Http\Controllers\StockCardController::class, 'getBrandsAjax'])->name('brands.ajax');
        Route::get('/versions-ajax', [App\Http\Controllers\StockCardController::class, 'getVersionsAjax'])->name('versions.ajax');
        Route::get('/categories-ajax', [App\Http\Controllers\StockCardController::class, 'getCategoriesAjax'])->name('categories.ajax');
        Route::get('/stock-names-ajax', [App\Http\Controllers\StockCardController::class, 'getStockNamesAjax'])->name('stock.names.ajax');
        Route::get('edit', [App\Http\Controllers\StockCardController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\StockCardController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\StockCardController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\StockCardController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\StockCardController::class, 'update'])->name('update');
        Route::get('barcode', [App\Http\Controllers\StockCardController::class, 'barcode'])->name('barcode');
        Route::post('barcodes', [App\Http\Controllers\StockCardController::class, 'barcodes'])->name('barcodes');
        Route::get('movement', [App\Http\Controllers\StockCardController::class, 'movement'])->name('movement');
        Route::post('add_movement', [App\Http\Controllers\StockCardController::class, 'add_movement'])->name('add_movement');
        Route::get('showmovemnet', [App\Http\Controllers\StockCardController::class, 'showmovemnet'])->name('showmovemnet');
        Route::get('movementdelete', [App\Http\Controllers\StockCardController::class, 'movementdelete'])->name('movementdelete');
        Route::get('show', [App\Http\Controllers\StockCardController::class, 'show'])->name('show');
        Route::post('sevk', [App\Http\Controllers\StockCardController::class, 'sevk'])->name('sevk');
        Route::get('list', [App\Http\Controllers\StockCardController::class, 'list'])->name('list');
        Route::get('/getListData', [App\Http\Controllers\StockCardController::class, 'getListData'])->name('getListData');
        Route::post('priceupdate', [App\Http\Controllers\StockCardController::class, 'priceupdate'])->name('priceupdate');
        Route::post('singlepriceupdate', [App\Http\Controllers\StockCardController::class, 'singlepriceupdate'])->name('singlepriceupdate');
        Route::post('multiplepriceupdate', [App\Http\Controllers\StockCardController::class, 'multiplepriceupdate'])->name('multiplepriceupdate');
        Route::post('multiplesaleupdate', [App\Http\Controllers\StockCardController::class, 'multiplesaleupdate'])->name('multiplesaleupdate');
        Route::get('singleserialprint', [App\Http\Controllers\StockCardController::class, 'singleserialprint'])->name('singleserialprint');
        Route::post('refund', [App\Http\Controllers\StockCardController::class, 'refund'])->name('refund');
        Route::get('refunddetail', [App\Http\Controllers\StockCardController::class, 'refunddetail'])->name('refunddetail');
        Route::post('refunddetailStore', [App\Http\Controllers\StockCardController::class, 'refunddetailStore'])->name('refunddetailStore');
        Route::get('refundlist', [App\Http\Controllers\StockCardController::class, 'refundlist'])->name('refundlist');
        Route::get('refundcomfirm', [App\Http\Controllers\StockCardController::class, 'refundcomfirm'])->name('refundcomfirm');
        Route::get('refundreturn', [App\Http\Controllers\StockCardController::class, 'refundreturn'])->name('refundreturn');
        Route::get('newSale', [App\Http\Controllers\StockCardController::class, 'newSale'])->name('newSale');
        Route::post('newSaleStore', [App\Http\Controllers\StockCardController::class, 'newSaleStore'])->name('newSaleStore');
        Route::post('category_id', [App\Http\Controllers\StockCardController::class, 'category_id'])->name('category_id');
        Route::get('deleted', [App\Http\Controllers\StockCardController::class, 'deleted'])->name('deleted');
        Route::get('deleted-data', [App\Http\Controllers\StockCardController::class, 'getDeletedData'])->name('deleted.data');
        Route::post('restore', [App\Http\Controllers\StockCardController::class, 'restore'])->name('restore');
        Route::get('serialList', [App\Http\Controllers\StockCardController::class, 'serialList'])->name('serialList');
        Route::get('stockforserial', [App\Http\Controllers\StockCardController::class, 'stockforserial'])->name('stockforserial');
        Route::get('getStockCardsData', [App\Http\Controllers\StockCardController::class, 'getStockCardsData'])->name('getStockCardsData');
        // AJAX endpoints - performans optimizasyonu (StockCardController)
        Route::get('/stockcard/sellers-ajax', [App\Http\Controllers\StockCardController::class, 'getSellersAjax'])->name('stockcard.sellers.ajax');
        Route::get('/stockcard/colors-ajax', [App\Http\Controllers\StockCardController::class, 'getColorsAjax'])->name('stockcard.colors.ajax');
        Route::get('/stockcard/brands-ajax', [App\Http\Controllers\StockCardController::class, 'getBrandsAjax'])->name('stockcard.brands.ajax');
        Route::get('/stockcard/categories-ajax', [App\Http\Controllers\StockCardController::class, 'getCategoriesAjax'])->name('stockcard.categories.ajax');
        Route::get('/stockcard/versions-ajax', [App\Http\Controllers\StockCardController::class, 'getVersionsAjax'])->name('stockcard.versions.ajax');
        Route::get('/stockcard/stock-names-ajax', [App\Http\Controllers\StockCardController::class, 'getStockNamesAjax'])->name('stockcard.stock.names.ajax');
    });

    Route::prefix('transfer')->name('transfer.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\TransferController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\TransferController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\TransferController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\TransferController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\TransferController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\TransferController::class, 'update'])->name('update');
        Route::get('show', [App\Http\Controllers\TransferController::class, 'show'])->name('show');
        
        // AJAX endpoints - Vue.js için
        Route::get('/incoming-ajax', [App\Http\Controllers\TransferController::class, 'getIncomingTransfersAjax'])->name('incoming.ajax');
        Route::get('/outgoing-ajax', [App\Http\Controllers\TransferController::class, 'getOutgoingTransfersAjax'])->name('outgoing.ajax');
        Route::get('/versions-ajax', [App\Http\Controllers\TransferController::class, 'getVersionsAjax'])->name('versions.ajax');
        Route::get('/{id}', [App\Http\Controllers\TransferController::class, 'getTransferJson'])->where('id', '[0-9]+')->name('json');
    });

    Route::prefix('reason')->name('reason.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\ReasonController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\ReasonController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\ReasonController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\ReasonController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\ReasonController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\ReasonController::class, 'update'])->name('update');
    });

    Route::prefix('invoice')->name('invoice.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\InvoiceController::class, 'index'])->name('index');
        Route::get('invoices-data', [App\Http\Controllers\InvoiceController::class, 'getInvoicesData'])->name('invoices.data');
        Route::get('create', [App\Http\Controllers\InvoiceController::class, 'create'])->name('create');
        Route::get('edit', [App\Http\Controllers\InvoiceController::class, 'edit'])->name('edit');
        Route::get('show', [App\Http\Controllers\InvoiceController::class, 'show'])->name('show');
        Route::get('delete', [App\Http\Controllers\InvoiceController::class, 'delete'])->name('delete');
        Route::post('store', [App\Http\Controllers\InvoiceController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\InvoiceController::class, 'update'])->name('update');
        Route::get('einvoice', [App\Http\Controllers\InvoiceController::class, 'einvoice'])->name('einvoice');
        Route::get('fast', [App\Http\Controllers\InvoiceController::class, 'fast'])->name('create.fast');
        Route::get('personal', [App\Http\Controllers\InvoiceController::class, 'personal'])->name('create.personal');
        Route::get('accomodation', [App\Http\Controllers\InvoiceController::class, 'accomodation'])->name('create.accomodation');
        Route::get('bank', [App\Http\Controllers\InvoiceController::class, 'bank'])->name('create.bank');
        Route::get('tax', [App\Http\Controllers\InvoiceController::class, 'tax'])->name('create.tax');
        Route::get('serialprint', [App\Http\Controllers\InvoiceController::class, 'serialprint'])->name('serialprint');
        Route::get('sales', [App\Http\Controllers\InvoiceController::class, 'sales'])->name('sales');
        Route::get('salesedit', [App\Http\Controllers\InvoiceController::class, 'salesedit'])->name('salesedit');
        Route::post('salesstore', [App\Http\Controllers\InvoiceController::class, 'salesstore'])->name('salesstore');
        Route::post('salesupdate', [App\Http\Controllers\InvoiceController::class, 'salesupdate'])->name('salesupdate');
        Route::get('stockcardmovementform', [App\Http\Controllers\InvoiceController::class, 'stockcardmovementform'])->name('stockcardmovementform');
        Route::post('update-movements', [App\Http\Controllers\InvoiceController::class, 'updateMovements'])->name('update-movements');
        Route::get('stockcardmovementformrefund', [App\Http\Controllers\InvoiceController::class, 'stockcardmovementformrefund'])->name('stockcardmovementformrefund');
        Route::post('stockcardmovementstore', [App\Http\Controllers\InvoiceController::class, 'stockcardmovementstore'])->name('stockcardmovementstore');
        Route::get('stockmovementdelete', [App\Http\Controllers\InvoiceController::class, 'stockmovementdelete'])->name('stockmovementdelete');
        Route::get('pdf', [App\Http\Controllers\InvoiceController::class, 'pdf'])->name('pdf');
        Route::post('itemSave', [App\Http\Controllers\InvoiceController::class, 'itemSave'])->name('itemSave');

    });

    Route::prefix('e_invoice')->name('e_invoice.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\EInvoiceController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\EInvoiceController::class, 'edit'])->name('edit');
        Route::get('show', [App\Http\Controllers\EInvoiceController::class, 'show'])->name('show');
        Route::get('delete', [App\Http\Controllers\EInvoiceController::class, 'delete'])->name('delete');
        Route::post('create', [App\Http\Controllers\EInvoiceController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\EInvoiceController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\EInvoiceController::class, 'update'])->name('update');
        Route::post('e_invoice_create', [App\Http\Controllers\EInvoiceController::class, 'e_invoice_create'])->name('e_invoice_create');
    });

    Route::prefix('fakeproduct')->name('fakeproduct.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\FakeProductController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\FakeProductController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\FakeProductController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\FakeProductController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\FakeProductController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\FakeProductController::class, 'update'])->name('update');
    });

    Route::prefix('accounting_category')->name('accounting_category.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\AccountingCategoryController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\AccountingCategoryController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\AccountingCategoryController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\AccountingCategoryController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\AccountingCategoryController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\AccountingCategoryController::class, 'update'])->name('update');
    });

    Route::prefix('technical_service')->name('technical_service.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\TechnicalServiceController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\TechnicalServiceController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\TechnicalServiceController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\TechnicalServiceController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\TechnicalServiceController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\TechnicalServiceController::class, 'update'])->name('update');
        Route::get('detail', [App\Http\Controllers\TechnicalServiceController::class, 'detail'])->name('detail');
        Route::get('payment', [App\Http\Controllers\TechnicalServiceController::class, 'payment'])->name('payment');
        Route::post('detailstore', [App\Http\Controllers\TechnicalServiceController::class, 'detailstore'])->name('detailstore');
        Route::post('coveringdetailstore', [App\Http\Controllers\TechnicalServiceController::class, 'coveringdetailstore'])->name('coveringdetailstore');
        Route::post('coveringupdate', [App\Http\Controllers\TechnicalServiceController::class, 'coveringupdate'])->name('coveringupdate');
        Route::get('detaildelete', [App\Http\Controllers\TechnicalServiceController::class, 'detaildelete'])->name('detaildelete');
        Route::post('paymentcomplate', [App\Http\Controllers\TechnicalServiceController::class, 'paymentcomplate'])->name('paymentcomplate');
        Route::post('statusCgange', [App\Http\Controllers\TechnicalServiceController::class, 'statusCgange'])->name('statusCgange');

        Route::get('print', [App\Http\Controllers\TechnicalServiceController::class, 'print'])->name('print');
        Route::post('paymentstore', [App\Http\Controllers\TechnicalServiceController::class, 'payment'])->name('paymentstore');
        Route::post('sms', [App\Http\Controllers\TechnicalServiceController::class, 'sms'])->name('sms');
        Route::get('show', [App\Http\Controllers\TechnicalServiceController::class, 'show'])->name('show');
        Route::get('category', [App\Http\Controllers\TechnicalServiceController::class, 'category'])->name('category');
        Route::post('categorystore', [App\Http\Controllers\TechnicalServiceController::class, 'categorystore'])->name('categorystore');
        Route::get('categorydelete', [App\Http\Controllers\TechnicalServiceController::class, 'categorydelete'])->name('categorydelete');
        Route::get('covering', [App\Http\Controllers\TechnicalServiceController::class, 'covering'])->name('covering');
        Route::post('coveringstore', [App\Http\Controllers\TechnicalServiceController::class, 'coveringstore'])->name('coveringstore');
        Route::get('coverprint', [App\Http\Controllers\TechnicalServiceController::class, 'coverprint'])->name('coverprint');
        Route::get('coveredit', [App\Http\Controllers\TechnicalServiceController::class, 'coveredit'])->name('coveredit');
        Route::get('coverdetaildelete', [App\Http\Controllers\TechnicalServiceController::class, 'coverdetaildelete'])->name('coverdetaildelete');
    });

    Route::prefix('settings')->name('settings.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\SettingController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\SettingController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\SettingController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\SettingController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\SettingController::class, 'update'])->name('update');
    });

    Route::prefix('sale')->name('sale.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\SaleController::class, 'index'])->name('index');
        Route::get('export', [App\Http\Controllers\SaleController::class, 'exportToExcel'])->name('export');
        Route::get('edit', [App\Http\Controllers\SaleController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\SaleController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\SaleController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\SaleController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\SaleController::class, 'update'])->name('update');
        Route::get('show', [App\Http\Controllers\SaleController::class, 'show'])->name('show');
        
        // AJAX endpoints - Vue.js için
        Route::get('/ajax', [App\Http\Controllers\SaleController::class, 'getSalesAjax'])->name('ajax');
        Route::get('/invoice-details/{id}', [App\Http\Controllers\SaleController::class, 'getInvoiceSalesDetails'])->name('invoice.details');
        Route::get('/totals-async', [App\Http\Controllers\SaleController::class, 'calculateTotalsAsync'])->name('totals.async');
        Route::get('/versions-ajax', [App\Http\Controllers\SaleController::class, 'getVersionsAjax'])->name('versions.ajax');
    });

    Route::prefix('demand')->name('demand.')->middleware([])->group(function () {
        Route::post('store', [App\Http\Controllers\CustomController::class, 'demandStore'])->name('store');
        Route::post('status', [App\Http\Controllers\CustomController::class, 'demandStatus'])->name('status');
        Route::get('list', [App\Http\Controllers\CustomController::class, 'demandList'])->name('list');
        Route::get('print', [App\Http\Controllers\CustomController::class, 'demandPrint'])->name('print');

    });

    Route::prefix('phone')->name('phone.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\PhoneController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\PhoneController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\PhoneController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\PhoneController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\PhoneController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\PhoneController::class, 'update'])->name('update');
        Route::get('barcode', [App\Http\Controllers\PhoneController::class, 'barcode'])->name('barcode');
        Route::get('show', [App\Http\Controllers\PhoneController::class, 'show'])->name('show');
        Route::get('sale', [App\Http\Controllers\PhoneController::class, 'sale'])->name('sale');
        Route::post('salestore', [App\Http\Controllers\PhoneController::class, 'salestore'])->name('salestore');
        Route::get('confirm', [App\Http\Controllers\PhoneController::class, 'confirm'])->name('confirm');
        Route::get('printconfirm', [App\Http\Controllers\PhoneController::class, 'printconfirm'])->name('printconfirm');
        Route::get('list', [App\Http\Controllers\PhoneController::class, 'list'])->name('list');
    });


    Route::prefix('blog')->name('blog.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\BlogController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\BlogController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\BlogController::class, 'store'])->name('store');
        Route::post('update', [App\Http\Controllers\BlogController::class, 'update'])->name('update');
        Route::get('show', [App\Http\Controllers\BlogController::class, 'show'])->name('show');

    });
    Route::prefix('site')->name('site.')->middleware([])->group(function () {
        Route::prefix('technical_service_category')->name('technical_service_category.')->middleware([])->group(function () {
            Route::get('/', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'index'])->name('index');
            Route::get('edit', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'edit'])->name('edit');
            Route::get('delete', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'delete'])->name('delete');
            Route::get('create', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'create'])->name('create');
            Route::post('store', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'store'])->name('store');
            Route::post('update', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'update'])->name('update');
            Route::get('show', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'show'])->name('show');
            Route::get('get', [App\Http\Controllers\SiteTechnicalServiceCategoryController::class, 'get'])->name('get');
        });
    });
    Route::prefix('enumeration')->name('enumeration.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\EnumerationController::class, 'index'])->name('index');
        Route::get('edit', [App\Http\Controllers\EnumerationController::class, 'edit'])->name('edit');
        Route::get('delete', [App\Http\Controllers\EnumerationController::class, 'delete'])->name('delete');
        Route::get('create', [App\Http\Controllers\EnumerationController::class, 'create'])->name('create');
        Route::post('store', [App\Http\Controllers\EnumerationController::class, 'start'])->name('store');
        Route::post('update', [App\Http\Controllers\EnumerationController::class, 'update'])->name('update');
        Route::get('show', [App\Http\Controllers\EnumerationController::class, 'show'])->name('show');
        Route::get('get', [App\Http\Controllers\EnumerationController::class, 'get'])->name('get');
        Route::get('finish', [App\Http\Controllers\EnumerationController::class, 'finish'])->name('finish');
        Route::get('print', [App\Http\Controllers\EnumerationController::class, 'print'])->name('print');
        Route::get('/stocktracking', [App\Http\Controllers\EnumerationController::class, 'stocktracking'])->name('stocktracking');
        Route::get('/newGet', [App\Http\Controllers\EnumerationController::class, 'newGet'])->name('newGet');
        Route::get('/runquene', [App\Http\Controllers\EnumerationController::class, 'runquene'])->name('runquene');
        Route::post('/getLastSerial', [App\Http\Controllers\EnumerationController::class, 'getLastSerial'])->name('getLastSerial');
        Route::get('/newPrint', [App\Http\Controllers\EnumerationController::class, 'newPrint'])->name('newPrint');
    });


    Route::prefix('report')->name('report.')->middleware([])->group(function () {
        Route::get('excelReport', [App\Http\Controllers\ReportController::class, 'excelReport'])->name('excelReport');
        Route::post('print', [App\Http\Controllers\ReportController::class, 'excelreportprint'])->name('print');
    });

    Route::post('/startTracking', [App\Http\Controllers\EnumerationController::class, 'start'])->name('startTracking');
    Route::post('/updateTracking', [App\Http\Controllers\EnumerationController::class, 'update'])->name('updateTracking');
    Route::post('/deleted_at_serial_number_store', [App\Http\Controllers\HomeController::class, 'deleted_at_serial_number_store'])->name('deleted_at_serial_number_store');

    Route::prefix('calculation')->name('calculation.')->middleware([])->group(function () {
        Route::get('/', [App\Http\Controllers\CalculationController::class, 'index'])->name('index');
        Route::get('categories', [App\Http\Controllers\CalculationController::class, 'categories'])->name('categories');
        Route::get('seller', [App\Http\Controllers\CalculationController::class, 'seller'])->name('seller');
        Route::get('staff', [App\Http\Controllers\CalculationController::class, 'staff'])->name('staff');
        Route::get('accounting', [App\Http\Controllers\CalculationController::class, 'accounting'])->name('accounting');
        Route::post('saveSeller', [App\Http\Controllers\CalculationController::class, 'saveSeller'])->name('saveSeller');
        Route::post('saveStaff', [App\Http\Controllers\CalculationController::class, 'saveStaff'])->name('saveStaff');
        Route::get('getPerson', [App\Http\Controllers\CalculationController::class, 'getPerson'])->name('getPerson');
        Route::post('process_store', [App\Http\Controllers\CalculationController::class, 'process_store'])->name('process_store');
        Route::get('selected', [App\Http\Controllers\CalculationController::class, 'selected'])->name('selected');
        Route::get('getCategories', [App\Http\Controllers\CalculationController::class, 'getCategories'])->name('getCategories');

    });


});
/**  Custom **/


Route::get('/cost_update', [App\Http\Controllers\CustomController::class, 'cost_update'])->name('cost_update');


Route::get('/get_cities', [App\Http\Controllers\CustomController::class, 'get_cities'])->name('get_cities');
Route::post('/custom_customerstore', [App\Http\Controllers\CustomController::class, 'customerstore'])->name('custom_customerstore');
Route::post('/custom_customerget', [App\Http\Controllers\CustomController::class, 'customerget'])->name('custom_customerget');
Route::post('/getStock', [App\Http\Controllers\CustomController::class, 'getStock'])->name('getStock');
Route::get('/searchStockCard', [App\Http\Controllers\CustomController::class, 'searchStockCard'])->name('searchStockCard');
Route::get('/get_version', [App\Http\Controllers\CustomController::class, 'get_version'])->name('get_version');
Route::get('/getStockCard', [App\Http\Controllers\CustomController::class, 'getStockCard'])->name('getStockCard');
Route::get('/customers', [App\Http\Controllers\CustomController::class, 'customers'])->name('customers');
Route::get('/transferList', [App\Http\Controllers\CustomController::class, 'transferList'])->name('transferList');
Route::post('/stockSearch', [App\Http\Controllers\CustomController::class, 'stockSearch'])->name('stockSearch');
Route::get('/serialcheck', [App\Http\Controllers\CustomController::class, 'serialcheck'])->name('serialcheck');
Route::get('/getStockCardCategory', [App\Http\Controllers\CustomController::class, 'getStockCardCategory'])->name('getStockCardCategory');
Route::get('/getStockSeller', [App\Http\Controllers\CustomController::class, 'getStockSeller'])->name('getStockSeller');
Route::post('/custom_editItem', [App\Http\Controllers\CustomController::class, 'custom_editItem'])->name('custom_editItem');
Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report');
Route::get('/newReport', [App\Http\Controllers\ReportController::class, 'newReport'])->name('newReport');
Route::get('/technicalReport', [App\Http\Controllers\ReportController::class, 'technicalReport'])->name('technicalReport');
Route::get('/technicalCustomReport', [App\Http\Controllers\ReportController::class, 'technicalCustomReport'])->name('technicalCustomReport');
Route::get('/personelsellerreport', [App\Http\Controllers\ReportController::class, 'personelsellerreport'])->name('personelsellerreport');
Route::get('/personelsellernewreport', [App\Http\Controllers\ReportController::class, 'personelsellernewreport'])->name('personelsellernewreport');
Route::post('/stocktakingcheck', [App\Http\Controllers\StocktakingController::class, 'stocktakingcheck'])->name('stocktakingcheck');
Route::get('/stocktakingserialcheck', [App\Http\Controllers\StocktakingController::class, 'stocktakingserialcheck'])->name('stocktakingserialcheck');
Route::get('/getTransferSerialCheck', [App\Http\Controllers\CustomController::class, 'getTransferSerialCheck'])->name('getTransferSerialCheck');
Route::get('/getTransferBarcodeCheck', [App\Http\Controllers\CustomController::class, 'getTransferBarcodeCheck'])->name('getTransferBarcodeCheck');



Route::get('/getStockMovementList', [App\Http\Controllers\StockCardController::class, 'getStockMovementList'])->name('getStockMovementList');
Route::get('/getStockquantity', [App\Http\Controllers\StockCardController::class, 'getStockquantity'])->name('getStockquantity');


// Laravel Logger routes removed - controller not available
Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Cache is cleared";
})->name('clear.cache');

// StockCard movements route
Route::get('/stockcard/movements', [App\Http\Controllers\StockCardController::class, 'getMovements'])
    ->middleware(['auth', 'companies'])
    ->name('stockcard.movements');

// API routes that need session-based authentication (moved from routes/api.php)
Route::middleware(['auth', 'companies'])->prefix('api')->group(function () {
    
    // Stock Check API - Quick sale
    Route::get('/stock/check', [App\Http\Controllers\HomeController::class, 'checkStock'])->name('api.stock.check');
    
    // Stock Price API
    Route::get('/stock-price/{id}', [App\Http\Controllers\StockCardController::class, 'getStockPriceApi'])->name('api.stock.price');
    
    // Customers API
    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'getCustomersApi'])->name('api.customers');
    
    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('sales-by-staff', [App\Http\Controllers\HomeController::class, 'getSalesByStaff'])->name('api.dashboard.sales-by-staff');
        Route::get('stock-turnover', [App\Http\Controllers\HomeController::class, 'getStockTurnover'])->name('api.dashboard.stock-turnover');
        Route::get('stock-turnover-ai', [App\Http\Controllers\HomeController::class, 'getStockTurnoverAI'])->name('api.dashboard.stock-turnover-ai');
        
        // AI Report Export routes
        Route::get('ai-analysis-export-pdf', [App\Http\Controllers\HomeController::class, 'exportAIAnalysisPDF'])->name('api.dashboard.ai-export-pdf');
        Route::get('ai-analysis-export-excel', [App\Http\Controllers\HomeController::class, 'exportAIAnalysisExcel'])->name('api.dashboard.ai-export-excel');
        Route::get('ai-analysis-export-json', [App\Http\Controllers\HomeController::class, 'exportAIAnalysisJSON'])->name('api.dashboard.ai-export-json');
    });
    
    // Common Data API - Cached and optimized
    Route::prefix('common')->group(function () {
        Route::get('/sellers', [App\Http\Controllers\Api\CommonDataController::class, 'getSellers'])->name('api.common.sellers');
        Route::get('/categories', [App\Http\Controllers\Api\CommonDataController::class, 'getCategories'])->name('api.common.categories');
        Route::get('/warehouses', [App\Http\Controllers\Api\CommonDataController::class, 'getWarehouses'])->name('api.common.warehouses');
        Route::get('/colors', [App\Http\Controllers\Api\CommonDataController::class, 'getColors'])->name('api.common.colors');
        Route::get('/brands', [App\Http\Controllers\Api\CommonDataController::class, 'getBrands'])->name('api.common.brands');
        Route::get('/versions', [App\Http\Controllers\Api\CommonDataController::class, 'getVersions'])->name('api.common.versions');
        Route::get('/reasons', [App\Http\Controllers\Api\CommonDataController::class, 'getReasons'])->name('api.common.reasons');
        Route::get('/customers', [App\Http\Controllers\Api\CommonDataController::class, 'getCustomers'])->name('api.common.customers');
        Route::get('/cities', [App\Http\Controllers\Api\CommonDataController::class, 'getCities'])->name('api.common.cities');
        Route::get('/towns', [App\Http\Controllers\Api\CommonDataController::class, 'getTowns'])->name('api.common.towns');
        Route::get('/currencies', [App\Http\Controllers\Api\CommonDataController::class, 'getCurrencies'])->name('api.common.currencies');
        Route::get('/safes', [App\Http\Controllers\Api\CommonDataController::class, 'getSafes'])->name('api.common.safes');
        Route::get('/users', [App\Http\Controllers\Api\CommonDataController::class, 'getUsers'])->name('api.common.users');
        
        // Bulk data endpoint
        Route::get('/all', [App\Http\Controllers\Api\CommonDataController::class, 'getAllCommonData'])->name('api.common.all');
        
        // Cache management
        Route::post('/clear-cache', [App\Http\Controllers\Api\CommonDataController::class, 'clearCache'])->name('api.common.clear-cache');
    });
});

// Demo routes
Route::get('/demo/daterangepicker', function () {
    return view('demo.daterangepicker');
})->name('demo.daterangepicker');
