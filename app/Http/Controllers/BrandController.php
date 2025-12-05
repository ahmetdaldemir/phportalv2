<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\Brand\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends BaseModuleController
{
    protected BrandService $service;
    protected ?string $viewPath = 'module.brand';
    protected ?string $routePrefix = 'brand';
    protected ?string $dataKey = 'brands';

    public function __construct(BrandService $brandService)
    {
        parent::__construct();
        $this->service = $brandService;
    }

    /**
     * Get service instance
     *
     * @return BrandService
     */
    protected function getService(): BrandService
    {
        return $this->service;
    }

    /**
     * Prepare store data
     *
     * @param Request $request
     * @return array
     */
    protected function prepareStoreData(Request $request): array
    {
        return [
            'name' => $request->name,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id()
        ];
    }

    /**
     * Technical service update
     *
     * @param Request $request
     * @return void
     */
    protected function technical(Request $request)
    {
        $brand = Brand::find($request->id);
        $brand->technical = $request->technical;
        $brand->save();
    }
}