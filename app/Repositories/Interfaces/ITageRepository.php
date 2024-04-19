<?php 

namespace App\Repositories\Interfaces; 

interface ITageRepository 
{ 
    public function index();
    public function getAllTages();
    public function deleteTage($request);
    public function addNewTage($request);
    public function updateTage($request);
    public function getAllTagesAdmin($take,$skip);
}