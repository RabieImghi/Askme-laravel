<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Follower;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\IUserRepository;



class UserController extends Controller
{
    protected $iUserRepository;
    public function __construct(IUserRepository $iUserRepository){
        $this->iUserRepository = $iUserRepository;
    }
    public function getStatisics(){
        $tages = $this->iUserRepository->getStatisicsTage();
        $users = $this->iUserRepository->getStatisicsUser();
        $userIndfo = [];
        $lastTages = [];
        foreach($tages as $tage){
            $lastTages[] = [ 'id' => $tage->id, 'name' => $tage->name, ];
        }
        foreach($users as $user){
            $userIndfo[] = [
                'id' => $user->id,
                'name' => $user->name,
                'level' => AnswerController::getBadge($user->points),
                'question' => $user->posts->count(),
                'avatar'=> asset('uploads/'.$user->avatar),
            ];
        }
        $Statistique= $this->iUserRepository->getStatisicsCount();
        return response()->json(['Statistiques' => $Statistique, 'TopUsers' => $userIndfo, 'TopTages' => $lastTages]);
        
    }
    public function uploadImage(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type'=>'required|string',
            'id'=>'required|integer',
        ]);
        $user = $this->iUserRepository->uploadImageFindUser($request->id);
        $imageName = $this->iUserRepository->uploadImage($request,$request->type,$user);
        return response()->json(['image' => asset('uploads/'.$imageName)],200);
    }
    public function getReatingStatics($id, $table,$champ) {
        return $this->iUserRepository->getReatingStatics($id, $table,$champ);
    }
    public function getUserInfo($id,$followerId){
        $user = $this->iUserRepository->getUserInfo($id);
        if(!$user){return response()->json(['message'=>'errore']);}
        $userData = [
            'id'=>$user->id,
            'name'=>$user->name,
            'firstName'=>$user->firstname,
            'lastName'=>$user->lastname,
            'email'=>$user->email,
            'about'=>$user->about ?? null,
            'badge'=> AnswerController::getBadge($user->points),
            'country'=>$user->country ?? null,
            'phone'=>$user->phone ?? null,
            'facebook'=> $user->socialLink->facebook ?? null,
            'twitter'=> $user->socialLink->twitter ?? null,
            'linkedin'=> $user->socialLink->linkedin ?? null,
            'Github'=> $user->socialLink->Github ?? null,
            'instagram'=> $user->socialLink->instagram ?? null,
            'WebSite'=> $user->socialLink->WebSite ?? null,
            'donnationLink'=>$user->donnationLink,
            'imageProfile'=>asset('uploads/'.$user->avatar),
            'imageCover'=>asset('uploads/'.$user->coverImage),
            'countQuesions' => Post::where('user_id',$id)->count(),
            'countReponse' => Answer::where('user_id',$id)->count(),
            'Point'=>$user->points,
            'followers' => User::find($id)->followers->count(),
            'following' => Follower::where('follower_id',$id)->count(),
            'isFollowed' => Follower::where('user_id',$id)->where('follower_id',$followerId)->count(),
            'Review' => $this->iUserRepository->getReatingStatics($id,'post_reatings','user_id') + $this->iUserRepository->getReatingStatics($id,'answer_reatings','user_id'),
        ];
        return response()->json(['user'=>$userData]);
    }
    public function updateUserInfo(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'country' => 'nullable',
            'about' => 'nullable',
            'phone' => 'nullable',
            'facebook' => 'nullable',
            'whatsapp' => 'nullable',
            'linkedin' => 'nullable',
            'Github' => 'nullable',
            'emailSosial' => 'nullable|email',
            'WebSite' => 'nullable',
            'donnationLink'=>'nullable',

        ]);
        $this->iUserRepository->updateUserInfo($request);
        $this->iUserRepository->updateUserInfoSocialLink($request);
        return response()->json(['message'=>'User info updated successfully!']);
    }
    public function follow(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'user_id' => 'required|integer',
            'follower_id' => 'required|integer',
        ]);
        $follower = $this->iUserRepository->follow($request);
        if($follower == null){
            $this->iUserRepository->followCreate($request);
            return response()->json(['message'=>'Followed successfully!']);
        }else{
            $follower->delete();
            return response()->json(['message'=>'Unfollowed successfully!']);
        }
    }
    public function getusers($skip){
        $users = $this->iUserRepository->getusers($skip);
        $usersData = [];
        foreach($users as $user){
            $usersData[] = [
                'id'=>$user->id,
                'name'=>$user->name,
                'firstName'=>$user->firstname,
                'lastName'=>$user->lastname,
                'about'=>$user->about ?? null,
                'country'=>$user->country,
                'email' => $user->email,
                'role'=> $user->role->name,
                'isBanne'=> $user->isBanne,
                'phone'=>$user->phone ?? null,
                'followers' => User::find($user->id)->followers->count(),
                'following' => Follower::where('follower_id',$user->id)->count(),
                'avatar'=>asset('uploads/'.$user->avatar),
                'coverImage'=>asset('uploads/'.$user->coverImage),
                'Level'=> AnswerController::getBadge($user->points),
                
            ];
        }
        return response()->json(['users'=>$usersData,'userCount'=> User::count(),]);
    }
    public function searchUser( $search){
        $users = $this->iUserRepository->searchUser($search);
        $usersData = [];
        foreach($users as $user){
            $usersData[] = [
                'id'=>$user->id,
                'name'=>$user->name,
                'country'=>$user->country,
                'followers' => User::find($user->id)->followers->count(),
                'following' => Follower::where('follower_id',$user->id)->count(),
                'avatar'=>asset('uploads/'.$user->avatar),
                'coverImage'=>asset('uploads/'.$user->coverImage),
                'Level'=> AnswerController::getBadge($user->points),
                
            ];
        }
        return response()->json(['users'=>$usersData,'userCount'=> $users->count(),]);
    }
    public function deleteUser(Request $request){
        if(!$request->user() && $request->user()->role_id != 1) return response()->json(['message'=>'Unauthenticated'],401);
        $this->iUserRepository->deleteUser($request);
        return response()->json(['message', 'user deleted successfully']);
    }
    public function banneUser(Request $request){
        if(!$request->user() && $request->user()->role_id != 1) return response()->json(['message'=>'Unauthenticated'],401);
        $this->iUserRepository->banneUser($request);
        return response()->json(['message'=>"ok"]);
    }
    public function changeUser(Request $request){
        if(!$request->user() && $request->user()->role_id != 1) return response()->json(['message'=>'Unauthenticated'],401);
        $user = $this->iUserRepository->changeUser($request);
        if($user->role_id == 1){
            $permissions = $this->iUserRepository->changeUserGetPermissionRole(2,$user);
        }else{
            $permissions = $this->iUserRepository->changeUserGetPermissionRole(1,$user);
        }
        foreach($permissions as $permission){
            $this->iUserRepository->changeUserCretaeNewRoles($request,$permission);
        }
        return response()->json(['message'=>"ok"]);
    }
    public function statiqueAdmin(Request $request){
        if(!$request->user() && $request->user()->role_id != 1) return response()->json(['message'=>'Unauthenticated'],401);
        $posts = Post::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
        ->get();
        $users = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
        ->get();

        $statisticsPost = array_fill(0, 12, 0);
        $statisticsUser = array_fill(0, 12, 0);

        foreach ($posts as $post) {
            $statisticsPost[$post->month - 1] = $post->count;
        }
        foreach ($users as $user) {
            $statisticsUser[$user->month - 1] = $user->count;
        }

        return response()->json(['statisticsPost' => $statisticsPost, 'statisticsUser' => $statisticsUser]);

    }
    public function getDataStatics(Request $request){
        if(!$request->user() && $request->user()->role_id != 1) return response()->json(['message'=>'Unauthenticated'],401);
        $statistics= $this->iUserRepository->getDataStatics();
        return response()->json(['statistics'=>$statistics]);
    }
    
}
