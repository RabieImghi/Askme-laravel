<?php 

namespace App\Repositories;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\PermessionVue_Role;
use App\Models\PermessionVue;
use App\Models\Follower;
use App\Models\Tage;
use App\Models\Category;
use App\Models\permession_vues_users;
use App\Models\SocialLink;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\IUserRepository; 

class UserRepository implements IUserRepository 
{ 
    public function login($request){
        $user = User::where('email', $request->email)->first();
        if (!$user) 
            return null;
        if (!Hash::check($request->password, $user->password)) 
            return false;
        if($user->isBanne == "1")
            return true;
        return $user;
    }
    public function register($request){
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'points' => 20,
            'role_id' => 2,
        ]);
        return $user;
        
    }
    public function Permession_vues_users_create($permissions,$user_id){
        foreach($permissions as $permission){
            permession_vues_users::create([
                'user_id' => $user_id,
                'permession_vue_id' => $permission,
                'is_active'=>1,
                'is_deleted'=>0,
            ]);
        }

    }
    public function getPermessionVue_Role(){
        return PermessionVue_Role::where('role_id',2)->pluck('permession_vue_id')->toArray();
    }
    public function PermissionVueJs($request){
        PermessionVue::truncate();
        foreach($request->router as $router){
            if($router == '/user' || $router == '/admin' || $router == null || $router == '/user/Error404' || $router == '/user/auth') continue;
            PermessionVue::create([
                'name' => $router,
            ]);
        }
    }
    public function getReatingStatics($id, $table,$champ){
        return DB::table($table)
        ->select(DB::raw('SUM(case when type = "+" then 1 when type = "-" then 1 else 0 end) as reating'))
        ->where($champ, $id)
        ->first()
        ->reating ?? 0;
    }
    public function getStatisicsCount(){
        $Statistique=  [
            'users' => User::count(),
            'questions' => Post::count(),
            'answers' => Answer::count(),
            'views' => Post::sum('views'),
        ];
        return $Statistique;
    }
    public function getStatisicsTage(){
        return Tage::orderBy('id', 'desc')->take(4)->get();
    }
    public function getStatisicsUser(){
        return User::with('posts')->orderBy('points', 'desc')->take(4)->get();
    }
    public function uploadImageFindUser($id){
        return User::find($id);
    }
    public function uploadImage($request,$type,$user){
        if($type == 'Profil'){
            $lastImage = $user->avatar;
            if($lastImage != 'default.png'){
                $file_path = public_path('uploads/'.$lastImage);
                if(file_exists($file_path)) unlink($file_path);
            }
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('uploads'), $imageName);
            $user->avatar = $imageName;
            $user->save();
            return $imageName;
        }
        else if($type == 'Cover') {
            $lastImage = $user->coverImage;
            if($lastImage != 'default2.jpg'){
                $file_path = public_path('uploads/'.$lastImage);
                if(file_exists($file_path)) unlink($file_path);
            }
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('uploads'), $imageName);
            $user->coverImage = $imageName;
            $user->save();
            return $imageName;
        }
    }
    public function getUserInfo($id){
        return User::with('socialLink')->where('id',$id)->first();
    }
    public function updateUserInfo($request){
        $user = User::find($request->id);
        $user->update([
            'name' => $request->name,
            'firstname' => $request->firstName,
            'lastname' => $request->lastName,
            'country' => $request->country,
            'phone' => $request->phone,
            'about'=>$request->about
        ]);
        $donnationLink = $user->donnationLink;
        if(!$donnationLink && $request->donnationLink!= ""){
            $user->donnationLink = $request->donnationLink;
            $user->save();
        }else{
            if($request->donnationLink != ""){
                $user->donnationLink =$request->donnationLink;
            }else{
                $user->donnationLink = null;
            }
            $user->save(); 
        }
    }
    public function updateUserInfoSocialLink($request){
        $socialLink = SocialLink::where('user_id', $request->id)->get();
        if($socialLink->count() == 0)
            SocialLink::create([
                'user_id' => $request->id,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'linkedin' => $request->linkedin,
                'Github' => $request->Github,
                'instagram' => $request->instagram,
                'WebSite' => $request->WebSite,
            ]);
        else{
            $id = $socialLink[0]->id;
            $socialLink = SocialLink::find($id);
            $socialLink->update([
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'linkedin' => $request->linkedin,
                'Github' => $request->Github,
                'instagram' => $request->instagram,
                'WebSite' => $request->WebSite,
            ]);
        }
    }
    public function follow($request){
        return Follower::where('user_id',$request->user_id)->where('follower_id',$request->follower_id)->first();
    }
    public function followCreate($request){
        Follower::create([
            'user_id' => $request->user_id,
            'follower_id' => $request->follower_id,
        ]);
    }
    public function getusers($skip){
        return User::with('role')->skip($skip)->take(12)->get();
    }
    public function searchUser($search){
        return User::where('name', 'like', '%'.$search.'%')->get();
    }
    public function deleteUser($request){
        $user = User::find($request->id);
        if(!$user) return response()->json(['message'=> 'User not found !!']);
        $user->delete();
    }
    public function banneUser($request){
        $user = User::find($request->id);
        if($user->isBanne == "0") $user->update(['isBanne'=>"1"]);
        else $user->update(['isBanne'=>"0"]);
    }
    public function changeUser($request){
        $user = User::find($request->id);
        permession_vues_users::where('user_id',$request->id)->delete();
        return $user;
    }
    public function changeUserGetPermissionRole($roleId,$user){
        $permissions = PermessionVue_Role::where('role_id',$roleId)->pluck('permession_vue_id')->toArray();
        $user->update(['role_id'=>$roleId]);
        return $permissions; 
    }
    public function changeUserCretaeNewRoles($request,$permission){
        permession_vues_users::create([
            'user_id' => $request->id,
            'permession_vue_id' => $permission,
            'is_active'=>1,
            'is_deleted'=>0,
        ]);
    }
    
    public function statiqueAdminPost(){
        $posts = Post::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
        ->get();
        return $posts;
    }
    public function statiqueAdminUser(){
        return User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
        ->get();
        
    }
    public function getDataStatics(){
        return  $statistics=[
            'users'=>User::count(),
            'posts'=>Post::count(),
            'answers'=>Answer::count(),
            'categories'=>Category::count(),
        ];
    }
}