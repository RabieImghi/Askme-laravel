<?php 

namespace App\Repositories\Interfaces; 

interface IPermissionRepository 
{
    public function getRolePemissions($request);
    public function getRolePemissionsUsers($skip);
    public function ChangeStatusPermissionsUser($request);
    public function getPemissionsAndRole($request);
    public function getPemissionsAndRolePer($request);
    public function addNewPermissions($request,$data);
    public function PermessionVue_Role($request,$data);
    public function deleteNewPermissions($request);
    public function CheckPermission($request);
    public function CheckPermissionUserRole($role_id);
    public function CheckPermissionUser($user_id);
}