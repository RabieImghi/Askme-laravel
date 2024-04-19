<?php 

namespace App\Repositories; 

use App\Models\Permission; 
use App\Models\PermessionVue_Role;
use App\Models\permession_vues_users;
use App\Models\Role;
use App\Models\PermessionVue;

use App\Repositories\Interfaces\IPermissionRepository; 

class PermissionRepository implements IPermissionRepository 
{
    public function getRolePemissions($request){
        return PermessionVue_Role::with('permessionVue')->with('role')->get();;
    }
    public function getRolePemissionsUsers($skip){
        return permession_vues_users::with('permessionVue')
        ->with(['user' => function ($query) use ($skip)  {
            $query->skip($skip)->take(6);
        }])->get();
    }
    public function ChangeStatusPermissionsUser($request){
        $permission = permession_vues_users::find($request->id);
        $permission->is_active = $request->is_active;
        $permission->save();
    }
    public function getPemissionsAndRolePer($request){
        return PermessionVue::all();
    }
    public function getPemissionsAndRole($request){
        return Role::all();
    }
    public function PermessionVue_Role($request,$data){
        return PermessionVue_Role::where('role_id',$request->formData["role_id"])->where('permession_vue_id',$data)->count();
    }
    public function addNewPermissions($request,$data){
        $permission = new PermessionVue_Role();
                $permission->role_id = $request->formData['role_id'];
                $permission->permession_vue_id = $data;
                $permission->save();
    }
    public function deleteNewPermissions($request){
        PermessionVue_Role::where('id',$request->id)->delete();
    }
    public function CheckPermission($request){
        return PermessionVue_Role::with('permessionVue')->where('role_id',  $request->role_id)->get();
    }
    public function CheckPermissionUserRole($role_id){
        return PermessionVue_Role::with('permessionVue')->where('role_id', $role_id)->get();
    }
    public function CheckPermissionUser($user_id){
        return permession_vues_users::with('permessionVue')->where('user_id', $user_id)->get();
    }
}