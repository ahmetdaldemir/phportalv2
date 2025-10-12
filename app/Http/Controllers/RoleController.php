<?php

namespace App\Http\Controllers;

use App\Services\Role\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    protected function index()
    {
        $data['roles'] = $this->roleService->get();
        return view('module.role.index',$data);
    }

    protected function create()
    {
        return view('module.role.form');
    }
    protected function edit(Request $request)
    {
        $data['roles'] = $this->roleService->find($request->id);
        return view('module.role.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->roleService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name);
        if(empty($request->id))
        {
            $this->roleService->create($data);
        }else{
            $this->roleService->update($request->id,$data);
        }

        return redirect()->route('role.index');
    }

    public function permisson(Request $request)
    {
        $data['permissions'] = $this->roleService->find($request->id);
        return view('module.role.permission',$data);
    }
}
