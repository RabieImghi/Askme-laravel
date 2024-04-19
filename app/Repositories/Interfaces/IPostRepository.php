<?php 

namespace App\Repositories\Interfaces; 

interface IPostRepository 
{
    public function getReating($id, $table,$champ); 
    public function getIdUserVoted($table,$id,$champ);
    public function allPost($page);
    public function allPostCount();
    public function allPostSearchByTage($tageId,$page);
    public function allPostSearchByContent($value,$page);
    public function allPostSearchByCategory($value,$page);
    public function allPostTages($post_id);
    public function allPostAnswersCount($post_id);
    public function AddQuestions($request,$filename);
    public function postAttachTage($post, $tage);
    public function MyPost($id,$page);
    public function MyPostTgae($id);
    public function UpdateQuestionsAttacheTage($post,$tage);
    public function UpdateQuestions($request);
    public function DeletePost($id) ;

    public function ChangeReating($table,$id,$userId);
    public function ChangeReatingUpdate($table,$request,$type);
    public function InsertReating($table,$id,$userId,$type);
    public function DeleteReating($table,$id);

    public function addViewsPost($id);
    public function getPostManage($request,$skip);
    public function changeStatusPost($request);
}