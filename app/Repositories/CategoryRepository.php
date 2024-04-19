<?php 

namespace App\Repositories; 

use App\Models\Category; 
use App\Models\Tage; 

use App\Repositories\Interfaces\ICategoryRepository; 

class CategoryRepository implements ICategoryRepository 
{ 
    public function index(){
        return Category::with('posts')->get();
    }
    public function getAllCategory(){
        return Category::get();
    }
    public function deleteCategory($request){
        $Category = Category::find($request->id);
        $Category->delete();
    }
    public function addNewCategory($request){
        Category::create([
            'name' => $request->name,
        ]);
    }
    public function updateCategory($request){
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->save();
    }
    public function getTages(){
        return Tage::get();
    }
}