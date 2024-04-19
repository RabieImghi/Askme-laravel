<?php 

namespace App\Repositories\Interfaces; 

interface IAnswerRepository 
{ 
    public function getReating($id, $table,$champ);
    public function getReatingStatics($id, $table,$champ);
    public function getIdUserVoted($table,$id,$champ);
    public function getPostAnswers($id);
    public function getPost($id);
    public function countAnswer($id);
    public function addAnswer($request);
    public function addPointUser($id);
    public function deleteAnswer($id);
    public function updateAnswer($request);
    public function verfyAnswer($request);
}