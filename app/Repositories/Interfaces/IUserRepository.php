<?php 

namespace App\Repositories\Interfaces; 

interface IUserRepository 
{ 
    public function login($request);
    public function register($request);
    public function Permession_vues_users_create($permissions,$user_id);
    public function getPermessionVue_Role();
    public function PermissionVueJs($request);
    public function getStatisicsCount();
    public function getStatisicsTage();
    public function getStatisicsUser();
    public function uploadImage($request,$type,$user);
    public function getUserInfo($id);
    public function updateUserInfo($request);
    public function updateUserInfoSocialLink($request);
    public function uploadImageFindUser($id);
    public function follow($request);
    public function followCreate($request);
    public function getusers($skip);
    public function searchUser($search);
    public function deleteUser($request);
    public function banneUser($request);
    public function changeUser($request);
    public function changeUserGetPermissionRole($roleId,$user);
    public function changeUserCretaeNewRoles($request,$permission);
    public function getReatingStatics($id, $table,$champ);
    public function statiqueAdminPost();
    public function statiqueAdminUser();
    public function getDataStatics();
}