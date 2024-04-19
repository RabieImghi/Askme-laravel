<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;
use App\Models\PermessionVue_Role;
use Illuminate\Support\Facades\Cookie;

class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->input('uri');
        $role_id = $request->input('role_id');
        $allowedRoutes = PermessionVue_Role::with('permessionVue')->where('role_id', $role_id)->get();
        $allowedRouteTable =[];
        foreach($allowedRoutes as $allowed){
            $allowedRouteTable[]= $allowed->permessionVue->name;
        }
        if (in_array($uri, $allowedRouteTable)) return $next($request);
        else return response()->json(['errorss'=>$role_id], 401);
    }
}
