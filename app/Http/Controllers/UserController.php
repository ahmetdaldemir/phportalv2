<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Seller;
use App\Models\User;
use App\Services\Company\CompanyService;
use App\Services\Seller\SellerService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private UserService $userService;
    private SellerService $sellerService;
    private CompanyService $companyService;


    public function __construct(UserService $userService, SellerService $sellerService,CompanyService $companyService)
    {
        $this->userService = $userService;
        $this->sellerService = $sellerService;
        $this->companyService = $companyService;
    }

    protected function index()
    {
        $data['users'] = User::all();

        if(\auth()->user()->company_id != 1)
        {
            $data['users'] = $this->userService->get();
        }

        $data['roles'] = Role::all();
        return view('module.users.index', $data);
    }

    protected function create()
    {
        $data['sellers'] = Seller::all();
        if ((!Auth::user()->hasRole('super-admin') || !Auth::user()->hasRole('Depo Sorumlusu')) && Auth::user()->company_id != 1) // HAsarlı Sorgusu
        {
            $data['companys'] = Company::where('id',Auth::user()->company_id)->get();
        }else{
            $data['companys'] = $this->companyService->all();
        }
        $data['roles'] = Role::all();
        $data['edit'] = 0;

        return view('module.users.form', $data);
    }

    protected function edit(Request $request)
    {
        $data['users'] = $this->userService->find($request->id);
        $data['roles'] = Role::all();
        $data['sellers'] = $this->sellerService->get();
        if ((!Auth::user()->hasRole('super-admin') || !Auth::user()->hasRole('Depo Sorumlusu')) && Auth::user()->company_id != 1) // HAsarlı Sorgusu
        {
            $data['companys'] = Company::where('id',Auth::user()->company_id)->get();
        }else{
            $data['companys'] = $this->companyService->all();
        }

        $data['edit'] = 1;
        return view('module.users.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->userService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        if($request->filled('password'))
        {
            $data = array(
                'name' => $request->name, 'email' => $request->email,
                'company_id' => $request->company_id,
                'user_id' => Auth::user()->id,
                'is_status' => 1,
                'password' => bcrypt($request->password));
        }else{
            $data = array(
                'name' => $request->name, 'email' => $request->email,
                'company_id' => $request->company_id,
                'user_id' => Auth::user()->id,
                'is_status' => 1
            );
        }

        if (!$request->filled('id')) {
           $data['seller_id'] = $request->seller_id;
            $this->userService->create($data);
            $user_id = $this->userService->lastInsertId();
        } else {
            $this->userService->update($request->id, $data);
            $user_id = $request->id;
        }

        $this->userService->role($user_id, $request->role);
        return redirect()->route('user.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->userService->update($request->id, $data);
    }


}
