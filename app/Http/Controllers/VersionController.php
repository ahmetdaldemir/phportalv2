<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Models\VersionChild;
use App\Services\Version\VersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VersionController extends Controller
{
    private VersionService $versionService;

    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    protected function index()
    {
        $data['versions'] = $this->versionService->get();
        return view('module.version.index', $data);
    }

    protected function create(Request $request)
    {
        $data['brand_id'] = $request->id;
        $data['versionlist'] = Version::where('brand_id', $request->id)->get();
        return view('module.version.form', $data);
    }

    protected function edit(Request $request)
    {
        $data['versions'] = $this->versionService->find($request->id);
        $data['versionlist'] = Version::where('brand_id', $data['versions']->brand_id)->get();
        return view('module.version.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->versionService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name, 'brand_id' => $request->brand_id, 'image' => $request->file('image'), 'company_id' => Auth::user()->company_id, 'user_id' => Auth::id());
        if (empty($request->id)) {
          $this->versionService->create($data);
            $version =  Version::latest()->first();

            $versionChild = new VersionChild();
            $versionChild->version_id = $version->id;
            $versionChild->name = $version->name;
            $versionChild->save();

        } else {
            $this->versionService->update($request->id, $data);

            $versionChild = VersionChild::where('version_id',$request->id)->where('name',$request->name)->first();
            if($versionChild)
            {
                $versionChild->name = $data['name'];
                $versionChild->save();
            }else{
                $versionChild = new VersionChild();
                $versionChild->version_id = $request->id;
                $versionChild->name = $data['name'];
                $versionChild->save();
            }

        }

        return redirect()->route('version.create', ['id' => $request->brand_id]);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->versionService->update($request->id, $data);
    }

    protected function technical(Request $request)
    {

        $brand = Version::find($request->id);
        $brand->technical = $request->technical;
        $brand->save();
    }
}
