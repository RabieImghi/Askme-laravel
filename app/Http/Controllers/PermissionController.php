<?php

namespace App\Http\Controllers;

use App\Models\PermessionVue_Role;
use Illuminate\Http\Request;
use App\Models\permession_vues_users;
use App\Models\PermessionVue;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\IPermissionRepository;

class PermissionController extends Controller
{
    protected  $permissionRepository;
    public function __construct(IPermissionRepository $permissionRepository){
        $this->permissionRepository = $permissionRepository;
    }
    public function getRolePemissions(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $permissions = $this->permissionRepository->getRolePemissions($request);
        $Data = [];
        foreach($permissions as $permission){
            $Data[$permission->role->name][] = [
                "name"=>$permission->permessionVue->name,
                "id"=>$permission->id,
            ];
        }
        return response()->json(['permissions'=>$Data],200);
    } 
    public function getRolePemissionsUsers(Request $request,$skip){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $permissions = $this->permissionRepository->getRolePemissionsUsers($skip);
        $Data = [];
        foreach($permissions as $permission){
            if ($permission->user && $permission->permessionVue) {
                $Data[$permission->user->name][] = [
                    "route" => $permission->permessionVue->name, 
                    "isActive" => $permission->is_active,
                    'id' => $permission->id,
                ];
            }
        }
        return response()->json(['permissions'=>$Data, 'count'=>User::count()],200);
    } 
    public function ChangeStatusPermissionsUser(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->permissionRepository->ChangeStatusPermissionsUser($request);
        return response()->json(['message'=>'Permission Updated'],200);
    }
    public function getPemissionsAndRole(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $roles = $this->permissionRepository->getPemissionsAndRole($request);
        $rolesData = [];
        foreach($roles as $role){
            $rolesData[]=[
                'id'=>$role->id,
                'text'=>$role->name
            ];
        }
        $permissions = $this->permissionRepository->getPemissionsAndRolePer($request);
        $permissionData=[];
        foreach($permissions as $permission){
            $permissionData[]=[
                'id'=>$permission->id,
                'text'=>$permission->name
            ];
        }
        return response()->json(['roles'=>$rolesData,'permsissions'=>$permissionData],200);
        
    }

    public function addNewPermissions(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        foreach($request->formData['permissions_id'] as $data){
            $res = $this->permissionRepository->PermessionVue_Role($request,$data);
            if($res == 0){
                $this->permissionRepository->addNewPermissions($request,$data);
            }
        }
        return response()->json(['data'=>$request->formData],200);
    }
    public function deleteNewPermissions(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->permissionRepository->deleteNewPermissions($request);
        return response()->json(['data'=>$request->id],200);
    }
    public function CheckPermission(Request $request){         
        $uri = $request->uri;
        $role_id = $request->role_id;
        $allowedRoutes = $this->permissionRepository->CheckPermission($request);
        $allowedRouteTable =[];
        foreach($allowedRoutes as $allowed){
            $allowedRouteTable[]= $allowed->permessionVue->name;
        }
        if (in_array($uri, $allowedRouteTable)) return response()->json(['uri'=>$uri]);
        else return response()->json(['errors'=>"Not Auth ouriside"], 401);
    }
    public function CheckPermissionUser(Request $request){
        $uri = $request->dataUser['uri'];
        $role_id = $request->dataUser['role_id'];
        $user_id = $request->dataUser['user_id'];
        $message = "";
        if($user_id != null){
            $allowedRoutesRole = $this->permissionRepository->CheckPermissionUserRole($role_id);
            $allowedRouteRoleTable =[];
            foreach($allowedRoutesRole as $allowed){
                $allowedRouteRoleTable[]= $allowed->permessionVue->name;
            }
            if (in_array($uri, $allowedRouteRoleTable)){
                $allowedRoutes = $this->permissionRepository->CheckPermissionUser($user_id);
                $allowedRouteTable =[];
                foreach($allowedRoutes as $allowed){
                    $allowedRouteTable['route'][]= $allowed->permessionVue->name;
                    $allowedRouteTable['isActive'][]= $allowed->is_active;
                }
                $index = array_search($uri, $allowedRouteTable['route']);
                if($index !== FALSE){
                    if( $allowedRouteTable['isActive'][$index] == 1){
                        $message = "Auth";
                    }else $message = "notAuth1";
                }else $message = "notAuth2";
            }else $message = "notAuth3";
        }else{
            $allowedRoutesRole = $this->permissionRepository->CheckPermissionUserRole($role_id);
            $allowedRouteRoleTable =[];
            foreach($allowedRoutesRole as $allowed){
                $allowedRouteRoleTable[]= $allowed->permessionVue->name;
            }
            if (in_array($uri, $allowedRouteRoleTable)){
                $message = "Auth";
            }else $message= "notAuth4";
        }
        return response()->json($message);
    }
}
