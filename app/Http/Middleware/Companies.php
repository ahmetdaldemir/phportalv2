<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Companies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Companies middleware called', [
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => Auth::id()
        ]);
        
        if(!Auth::check())
        {
            Log::warning('Companies middleware: User not authenticated');
            return redirect()->to('logout');
        }
        $company = Auth::user()->company;
        
        if (!$company || $company->is_status == 0) {
            Log::warning('Companies middleware: Invalid company', [
                'user_id' => Auth::id(),
                'company_id' => $company ? $company->id : null,
                'company_status' => $company ? $company->is_status : null
            ]);
            return redirect()->to('logout');
        }
        
        Log::info('Companies middleware: Access granted');
        return $next($request);
    }
}
