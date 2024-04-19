<?php 

namespace App\Repositories\Interfaces; 

interface ICategoryRepository 
{ 
    public function index();
    public function getAllCategory();
    public function deleteCategory($request);
    public function addNewCategory($request);
    public function updateCategory($request);
    public function getTages();
}