<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tage;
use App\Models\User;
use App\Repositories\Interfaces\IPostRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    protected $postRepository;
    public function __construct(IPostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getReating($id, $table,$champ){
        return $this->postRepository->getReating($id, $table,$champ);
    }
    public function getIdUserVoted($table,$id,$champ){
        return $this->postRepository->getIdUserVoted($table,$id,$champ);
    }
    public function allPost(Request $request){
        $data = [];
        $page = $request->query('page', 1);
        if($request->tageId){
            $tageId = $request->tageId;
            $posts = $this->postRepository->allPostSearchByTage($tageId,$page);
        }else if($request->serchType){
            $type =$request->serchType;
            $value = $request->searchQuery;
            if($type=='Post')
                $posts = $this->postRepository->allPostSearchByContent($value,$page);
            else if($type == "Category")
                $posts = $this->postRepository->allPostSearchByCategory($value,$page);
        }
        else{
            $posts = $this->postRepository->allPost($page);
        }
        $count = $this->postRepository->allPostCount();
        foreach ($posts as $post) {
            $dataTage =[];
            $tages= $this->postRepository->allPostTages($post->id);
            $answers=$this->postRepository->allPostAnswersCount($post->id);
            foreach ($tages as $tage) {
                $tage = Tage::find($tage->tage_id)  ;
                $dataTage[] = $tage->name;
            }

            $data[] = [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'views' => $post->views,
                'answers' => $answers,
                'badge' => AnswerController::getBadge($post->user->points),
                'name' => $post->user->name,
                'user_id' => $post->user->id,
                'imageUser' => asset('uploads/'.$post->user->avatar),
                'category' => $post->category->name,
                'created_at' => Carbon::parse($post->created_at)->format('F j, Y'),
                'tages' => $dataTage,
                'listIdUserVoted'=> $this->getIdUserVoted('post_reatings',$post->id,'post_id'),
                'reating' =>$this->getReating($post->id, 'post_reatings', 'post_id'),
            ];
        }
        return response()->json([ 'data' => $data, 'count' => $count, ]);
    }
    
    public function AddQuestions(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'tages' => 'required',
        ]);
        $filename = "";
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
        }
        $post = $this->postRepository->AddQuestions($request,$filename);
        $tages = explode(',', $request->tages) ;
        foreach ($tages as $tage) {
            $this->postRepository->postAttachTage($post, $tage);
        }
        $this->AddUserPoints($request->user_id);
        return response()->json([
            'message' => 'Post created successfully',
            'data' => $filename,
        ]);
    }
    public function AddUserPoints($id){
        $user = User::find($id);
        $user->points = $user->points + 10;
        $user->save();
    }
    public function MyPost(Request $request,$id){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $data = [];
        $page = $request->query('page', 1);
        $posts = $this->postRepository->MyPost($id,$page);
        $count = $this->postRepository->allPostCount();
        foreach ($posts as $post) {
            $dataTage =[];
            $dataTageId =[];
            $tages = $this->postRepository->allPostTages($post->id);
            foreach ($tages as $tage) {
                $tage = $this->postRepository->MyPostTgae($tage->tage_id);
                $dataTage[] = $tage->name;
                $dataTageId[] = $tage->id;
            }
            $data[] = [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'views' => $post->views,
                'badge' =>  AnswerController::getBadge($post->user->points),
                'name' => $post->user->name,
                'user_id' => $post->user->id,
                'category_id' => $post->category->id,
                'category' => $post->category->name,
                'created_at' => Carbon::parse($post->created_at)->format('F j, Y'),
                'tages' => $dataTage,
                'imageUser' => asset('uploads/'.$post->user->avatar),
                'tages_id' => $dataTageId,
                'listIdUserVoted'=> $this->getIdUserVoted('post_reatings',$post->id,'post_id'),
                'reating' =>$this->getReating($post->id, 'post_reatings', 'post_id'),
            ];
        }
        return response()->json([ 'data' => $data, 'count' => $count, ]);
    }
    public function UpdateQuestions(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'tages' => 'required',
        ]);
        $post = $this->postRepository->UpdateQuestions($request);
        $tages = explode(',', $request->tages) ;
        $post->tages()->detach();
        foreach ($tages as $tage) {
            $this->postRepository->postAttachTage($post, $tage);
        }
        return response()->json([
            'message' => 'Post updated successfully',
        ]);
    }
    public function DeletePost(Request $request,$id) {
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->postRepository->DeletePost($id);
        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
    public function ChangeReating(Request $request,$id,$userId,$type){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $reating = DB::table('post_reatings')->select('*')->where('post_id', $id)->where('user_id', $userId)->first();
        if($reating){
            if($type == '+'){
                if($reating->type == '-'){
                    $this->postRepository->ChangeReatingUpdate('post_reatings',$reating->id,'+');
                    return response()->json([
                        'message' => 'Reating updated successfully',
                    ]);
                }else{
                    $this->postRepository->DeleteReating('post_reatings',$reating->id);
                    return response()->json([
                        'message' => 'Reating already exists' ,
                    ]);
                }
            }else{
                if($reating->type == '+'){
                    $this->postRepository->ChangeReatingUpdate('post_reatings',$reating->id,'-');
                    return response()->json([
                        'message' => 'Reating updated successfully',
                    ]);
                }else{
                    $this->postRepository->DeleteReating('post_reatings',$reating->id);
                    return response()->json([
                        'message' => 'Reating already exists' ,
                    ]);
                }
            }
        }else{
            if($type=='+'){
                $this->postRepository->InsertReating('post_reatings',$id,$userId,'+');
                return response()->json([
                    'message' => 'Reating created successfully',
                ]);
            }else{
                $this->postRepository->InsertReating('post_reatings',$id,$userId,'-');
                return response()->json([
                    'message' => 'Reating created successfully',
                ]);
            }
        }
    }
    public function ChangeReatingAnswer(Request $request,$id,$userId,$type){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $reating = DB::table('answer_reatings')->select('*')->where('answer_id', $id)->where('user_id', $userId)->first();
        if($reating){
            if($type == '+'){
                if($reating->type == '-'){
                    DB::table('answer_reatings')->where('id', $reating->id)->update(['type' => '+']);
                    return response()->json([
                        'message' => 'Reating updated successfully',
                    ]);
                }else{
                    DB::table('answer_reatings')->where('id', $reating->id)->delete();
                    return response()->json([
                        'message' => 'Reating already exists' ,
                    ]);
                }
            }else{
                if($reating->type == '+'){
                    DB::table('answer_reatings')->where('id', $reating->id)->update(['type' => '-']);
                    return response()->json([
                        'message' => 'Reating updated successfully',
                    ]);
                }else{
                    DB::table('answer_reatings')->where('id', $reating->id)->delete();
                    return response()->json([
                        'message' => 'Reating already exists' ,
                    ]);
                }
            }
        }else{
            if($type=='+'){
                $reating = DB::table('answer_reatings')->insert(['answer_id' => $id, 'user_id' => $userId, 'type' => '+']);
                return response()->json([
                    'message' => 'Reating created successfully',
                ]);
            }else{
                $reating = DB::table('answer_reatings')->insert(['answer_id' => $id, 'user_id' => $userId, 'type' => '-']);
                return response()->json([
                    'message' => 'Reating created successfully',
                ]);
            }
        }
    }
    public function addViewsPost($id){
        $this->postRepository->addViewsPost($id);
        return response()->json([
            'message' => 'Views added successfully',
        ]);
    }
    public function getPostManage(Request $request,$skip){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $posts = $this->postRepository->getPostManage($request,$skip);
        $data=[];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->id,
                'username'=>$post->user->name,
                'category'=>$post->category->name,
                'title' => $post->title,
                'isArchive'=>$post->isArchive,
                'content' => $post->content,
                'category' => $post->category->name,
                'created_at' => Carbon::parse($post->created_at)->format('F j, Y'),
            ];
        }
        return response()->json(['posts' => $data, 'count'=>Post::count()]);
    }
    public function changeStatusPost(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->postRepository->changeStatusPost($request);
        return response()->json(['message'=>"updated status secsufully"]);

    }
}
         