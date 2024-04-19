<?php 

namespace App\Repositories; 

use App\Models\Answer; 
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Repositories\Interfaces\IAnswerRepository; 

class AnswerRepository implements IAnswerRepository 
{
    public function getReating($id, $table,$champ){
        return DB::table($table)
            ->select(DB::raw('SUM(case when type = "+" then 1 when type = "-" then -1 else 0 end) as reating'))
            ->where($champ, $id)
            ->first()
            ->reating ?? 0;
    }
    public function getReatingStatics($id, $table,$champ){
        return DB::table($table)
        ->select(DB::raw('SUM(case when type = "+" then 1 when type = "-" then 1 else 0 end) as reating'))
        ->where($champ, $id)
        ->first()
        ->reating ?? 0;
    }
    public function getIdUserVoted($table,$id,$champ){
        return DB::table($table)->select('user_id','type')->where($champ, $id)->get();
    }
    public function getPostAnswers($id){
        return Answer::with('user', 'post')->where('post_id', $id)->orderBy('isVerfy','asc')->orderBy('id','desc')->get();
    }
    public function getPost($id){
        return Post::with('user', 'category')->where('id',$id)->first();
    }
    public function countAnswer($id){
        return Answer::with('user', 'post')->where('post_id', $id)->count();
    }
    public function addAnswer($request){
        $answer = new Answer();
        $answer->content = $request->answerDetails;
        $answer->user_id = $request->user_id;
        $answer->post_id = $request->post_id;
        $answer->save();
    }
    public function addPointUser($id){
        $user = User::find($id);
        $user->points = $user->points + 5;
        $user->save();
    }
    public function deleteAnswer($id){
        $answer = Answer::find($id);
        $answer->delete();
    }
    public function updateAnswer($request){
        $answer = Answer::find($request->answerId);
        $answer->content = $request->answerDetails;
        $answer->save();
    }
    public function verfyAnswer($request){
        $answer = Answer::find($request->answerId);
        $answer->isVerfy ='verfy';
        $answer->save();
    } 
}