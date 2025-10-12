<?php

namespace App\Http\Controllers;

use App\Services\Permission\PermissionService;
use App\Services\Role\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    private PermissionService $permissionService;
    private RoleService $roleService;

    public function __construct(PermissionService $permissionService,RoleService $roleService)
    {
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    protected function index()
    {
        $data['permissions'] = $this->permissionService->get();
        return view('module.permission.index',$data);
    }

    protected function create()
    {
        $data['roles'] = $this->roleService->get();

        return view('module.permission.form',$data);
    }
    protected function edit(Request $request)
    {
        $data['permissions'] = $this->permissionService->find($request->id);
        $data['roles'] = $this->roleService->get();

        return view('module.permission.form',$data);
    }

    protected function delete(Request $request)
    {

        $this->permissionService->delete($request->id);

         return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'title' => $request->title,'roles',$request->roles);

        if($request->id == 0)
        {
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->company_id = Auth::user()->company_id;
            $permission->title = $request->title;
            $permission->save();
        }else{
            $permission =   Permission::find($request->id);
        }


        foreach ($request->roles as $key => $item)
        {
            $permission->assignRole($item);
        }
        return redirect()->route('permission.index');
    }

    public function permisson(Request $request)
    {
        $data['permissions'] = $this->permissionService->find($request->id);
        return view('module.permission.permission',$data);
    }
}
