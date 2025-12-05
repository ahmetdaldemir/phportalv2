<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Base Module Controller
 * 
 * Tüm CRUD modülleri için ortak işlevleri sağlar.
 * Bu abstract class'ı extend eden controller'lar standart CRUD işlemlerini otomatik olarak alır.
 */
abstract class BaseModuleController extends Controller
{
    /**
     * View path prefix - child class'larda override edilebilir
     * Örnek: 'module.brand' -> 'module/brand/index.blade.php'
     */
    protected ?string $viewPath = null;

    /**
     * Route name prefix - child class'larda override edilebilir
     * Örnek: 'brand' -> route('brand.index')
     */
    protected ?string $routePrefix = null;

    /**
     * Model name for data array - child class'larda override edilebilir
     * Örnek: 'brands' -> $data['brands']
     */
    protected ?string $dataKey = null;

    /**
     * BaseModuleController constructor
     * Child class'larda service injection yapılmalı
     */
    public function __construct()
    {
        // Child class'larda service set edilmeli
    }

    /**
     * Get service instance - child class'larda override edilmeli
     * 
     * @return mixed
     */
    abstract protected function getService();

    /**
     * Index - Liste sayfası
     * 
     * @return \Illuminate\Contracts\View\View
     */
    protected function index()
    {
        $service = $this->getService();
        $data[$this->getDataKey()] = $service->get();
        return view($this->getViewPath() . '.index', $data);
    }

    /**
     * Create - Yeni kayıt formu
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    protected function create(Request $request = null)
    {
        $data = $this->getCreateData($request);
        return view($this->getViewPath() . '.form', $data);
    }

    /**
     * Edit - Düzenleme formu
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    protected function edit(Request $request)
    {
        $service = $this->getService();
        $data = $this->getEditData($request);
        $data[$this->getDataKey(true)] = $service->find($request->id);
        return view($this->getViewPath() . '.form', $data);
    }

    /**
     * Store - Kaydet/Güncelle
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function store(Request $request)
    {
        $service = $this->getService();
        $data = $this->prepareStoreData($request);
        
        if (empty($request->id)) {
            $service->create($data);
        } else {
            $service->update($request->id, $data);
        }

        return redirect()->route($this->getRoutePrefix() . '.index');
    }

    /**
     * Update - Status güncelleme (AJAX)
     * 
     * @param Request $request
     * @return mixed
     */
    protected function update(Request $request)
    {
        $service = $this->getService();
        $data = ['is_status' => $request->is_status];
        return $service->update($request->id, $data);
    }

    /**
     * Delete - Silme
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function delete(Request $request)
    {
        $service = $this->getService();
        $service->delete($request->id);
        return redirect()->back();
    }

    /**
     * Prepare store data - Child class'larda override edilebilir
     * 
     * @param Request $request
     * @return array
     */
    protected function prepareStoreData(Request $request): array
    {
        // Base implementation - child class'larda override edilmeli
        return [
            'name' => $request->name,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id()
        ];
    }

    /**
     * Get create page additional data - Child class'larda override edilebilir
     * 
     * @param Request|null $request
     * @return array
     */
    protected function getCreateData(Request $request = null): array
    {
        return [];
    }

    /**
     * Get edit page additional data - Child class'larda override edilebilir
     * 
     * @param Request $request
     * @return array
     */
    protected function getEditData(Request $request): array
    {
        return [];
    }

    /**
     * Get view path
     * 
     * @return string
     */
    protected function getViewPath(): string
    {
        if (isset($this->viewPath)) {
            return $this->viewPath;
        }

        // Auto-detect from class name
        $className = class_basename($this);
        $moduleName = str_replace('Controller', '', $className);
        $moduleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $moduleName));
        
        return 'module.' . $moduleName;
    }

    /**
     * Get route prefix
     * 
     * @return string
     */
    protected function getRoutePrefix(): string
    {
        if (isset($this->routePrefix)) {
            return $this->routePrefix;
        }

        // Auto-detect from class name
        $className = class_basename($this);
        $moduleName = str_replace('Controller', '', $className);
        $moduleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $moduleName));
        
        return $moduleName;
    }

    /**
     * Get data key for view
     * 
     * @param bool $singular Singular form (for edit)
     * @return string
     */
    protected function getDataKey(bool $singular = false): string
    {
        if (isset($this->dataKey)) {
            return $singular ? $this->getSingularForm($this->dataKey) : $this->dataKey;
        }

        // Auto-detect from class name
        $className = class_basename($this);
        $moduleName = str_replace('Controller', '', $className);
        $moduleName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $moduleName));
        
        return $singular ? $moduleName : $moduleName . 's';
    }

    /**
     * Get singular form of a word
     * 
     * @param string $word
     * @return string
     */
    protected function getSingularForm(string $word): string
    {
        // Simple plural to singular conversion
        $length = strlen($word);
        if ($length >= 3 && substr($word, -3) === 'ies') {
            return substr($word, 0, -3) . 'y';
        }
        if ($length >= 2 && substr($word, -2) === 'es') {
            return substr($word, 0, -2);
        }
        if ($length >= 1 && substr($word, -1) === 's') {
            return substr($word, 0, -1);
        }
        return $word;
    }
}

