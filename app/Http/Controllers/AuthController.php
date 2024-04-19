<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\IUserRepository;

class AuthController extends Controller
{
    protected $IUserRepository;

    public function __construct(IUserRepository $IUserRepository){
        $this->IUserRepository = $IUserRepository;
    }
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = $this->IUserRepository->login($request);
        if($user === null)
        return response()->json(['message' => "Email not correct!",'error'=>"email"], 401);
        if($user === false)
            return response()->json(['message' => "Password not correct!",'error'=>"password"], 401);
        if($user === true)
            return response()->json(['message' => "You Are Banned"],401);
        $token = $user->createToken('API Token')->plainTextToken;
        $dataUser = [
            'firstName'=>$user->firstname,
            'lastName'=>$user->lastname,
            'username'=>$user->name,
            'badge'=>AnswerController::getBadge($user->points),
            'points'=>$user->points,
            'role_id'=>$user->role_id,
            'avatar'=>asset('uploads/'.$user->avatar),
            'coverImage'=>asset('uploads/'.$user->coverImage),
        ];
        return response()->json(['user' => $dataUser, 'token' => $token, "user_id"=>$user->id],200);
    }

    public function register(Request $request){
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role_id' => 'required',
        ]);
        
        $user = $this->IUserRepository->register($request);
        $permissions = $this->IUserRepository->getPermessionVue_Role();
        $this->IUserRepository-> Permession_vues_users_create($permissions,$user->id);
        return response()->json([
            'user' => $user,
        ]);
    }

    public function logout(Request $request){
        cookie('authToken', "", time() - (60 * 60 * 24 * 365));
        cookie('authUser', "", time() - (60 * 60 * 24 * 365));
        return response()->json(['message' => 'Successfully logged out'],200);
    }

    public function CheckPermission(){
        return response()->json(['message' => 'You have permission to access this route!'],200);
    }

    public function PermissionVueJs(Request $request){
        $this->IUserRepository->PermissionVueJs($request);
        return response()->json(['test' => $request->router[2]],200);
    }
}