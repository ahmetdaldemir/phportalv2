<?php

namespace App\Providers;

use App\Models\FinansTransaction;
use App\Observers\FinansTransactionObserver;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Service bindings
        $this->app->bind(\App\Services\Invoice\InvoiceService::class, \App\Services\Invoice\InvoiceServiceImplement::class);
        $this->app->bind(\App\Services\Customer\CustomerService::class, \App\Services\Customer\CustomerServiceImplement::class);
        $this->app->bind(\App\Services\User\UserService::class, \App\Services\User\UserServiceImplement::class);
        $this->app->bind(\App\Services\Seller\SellerService::class, \App\Services\Seller\SellerServiceImplement::class);
        $this->app->bind(\App\Services\StockCard\StockCardService::class, \App\Services\StockCard\StockCardServiceImplement::class);
        $this->app->bind(\App\Services\Transfer\TransferService::class, \App\Services\Transfer\TransferServiceImplement::class);
        $this->app->bind(\App\Services\Warehouse\WarehouseService::class, \App\Services\Warehouse\WarehouseServiceImplement::class);
        $this->app->bind(\App\Services\Brand\BrandService::class, \App\Services\Brand\BrandServiceImplement::class);
        $this->app->bind(\App\Services\Category\CategoryService::class, \App\Services\Category\CategoryServiceImplement::class);
        $this->app->bind(\App\Services\Color\ColorService::class, \App\Services\Color\ColorServiceImplement::class);
        $this->app->bind(\App\Services\Version\VersionService::class, \App\Services\Version\VersionServiceImplement::class);
        $this->app->bind(\App\Services\Reason\ReasonService::class, \App\Services\Reason\ReasonServiceImplement::class);
        $this->app->bind(\App\Services\Safe\SafeService::class, \App\Services\Safe\SafeServiceImplement::class);
        $this->app->bind(\App\Services\Bank\BankService::class, \App\Services\Bank\BankServiceImplement::class);
        $this->app->bind(\App\Services\AccountingCategory\AccountingCategoryService::class, \App\Services\AccountingCategory\AccountingCategoryServiceImplement::class);
        $this->app->bind(\App\Services\Company\CompanyService::class, \App\Services\Company\CompanyServiceImplement::class);
        $this->app->bind(\App\Services\Technical\TechnicalService::class, \App\Services\Technical\TechnicalServiceImplement::class);
        $this->app->bind(\App\Services\Role\RoleService::class, \App\Services\Role\RoleServiceImplement::class);
        $this->app->bind(\App\Services\Permission\PermissionService::class, \App\Services\Permission\PermissionServiceImplement::class);
        $this->app->bind(\App\Services\Refund\RefundService::class, \App\Services\Refund\RefundServiceImplement::class);
        $this->app->bind(\App\Services\Demand\DemandService::class, \App\Services\Demand\DemandServiceImplement::class);
        $this->app->bind(\App\Services\FakeProduct\FakeProductService::class, \App\Services\FakeProduct\FakeProductServiceImplement::class);
        $this->app->bind(\App\Services\Blog\BlogService::class, \App\Services\Blog\BlogServiceImplement::class);
        $this->app->bind(\App\Services\Accounting\AccountingService::class, \App\Services\Accounting\AccountingServiceImplement::class);

        // Repository bindings
        $this->app->bind(\App\Repositories\Customer\CustomerRepository::class, \App\Repositories\Customer\CustomerRepositoryImplement::class);
        $this->app->bind(\App\Repositories\User\UserRepository::class, \App\Repositories\User\UserRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Seller\SellerRepository::class, \App\Repositories\Seller\SellerRepositoryImplement::class);
        $this->app->bind(\App\Repositories\StockCard\StockCardRepository::class, \App\Repositories\StockCard\StockCardRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Transfer\TransferRepository::class, \App\Repositories\Transfer\TransferRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Warehouse\WarehouseRepository::class, \App\Repositories\Warehouse\WarehouseRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Brand\BrandRepository::class, \App\Repositories\Brand\BrandRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Category\CategoryRepository::class, \App\Repositories\Category\CategoryRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Color\ColorRepository::class, \App\Repositories\Color\ColorRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Version\VersionRepository::class, \App\Repositories\Version\VersionRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Reason\ReasonRepository::class, \App\Repositories\Reason\ReasonRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Safe\SafeRepository::class, \App\Repositories\Safe\SafeRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Bank\BankRepository::class, \App\Repositories\Bank\BankRepositoryImplement::class);
        $this->app->bind(\App\Repositories\AccountingCategory\AccountingCategoryRepository::class, \App\Repositories\AccountingCategory\AccountingCategoryRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Company\CompanyRepository::class, \App\Repositories\Company\CompanyRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Technical\TechnicalRepository::class, \App\Repositories\Technical\TechnicalRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Role\RoleRepository::class, \App\Repositories\Role\RoleRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Permission\PermissionRepository::class, \App\Repositories\Permission\PermissionRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Refund\RefundRepository::class, \App\Repositories\Refund\RefundRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Demand\DemandRepository::class, \App\Repositories\Demand\DemandRepositoryImplement::class);
        $this->app->bind(\App\Repositories\FakeProduct\FakeProductRepository::class, \App\Repositories\FakeProduct\FakeProductRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Blog\BlogRepository::class, \App\Repositories\Blog\BlogRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Invoice\InvoiceRepository::class, \App\Repositories\Invoice\InvoiceRepositoryImplement::class);
        $this->app->bind(\App\Repositories\Accounting\AccountingRepository::class, \App\Repositories\Accounting\AccountingRepositoryImplement::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale(config('app.locale'));
        Paginator::useBootstrap();
        FinansTransaction::observe(FinansTransactionObserver::class);

    }
}
