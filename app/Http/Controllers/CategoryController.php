<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Tage;
use App\Repositories\Interfaces\ICategoryRepository;

class CategoryController extends Controller
{
    protected $iCategoryRepository;
    public function __construct(ICategoryRepository $iCategoryRepository){
        $this->iCategoryRepository = $iCategoryRepository;
    }
    public function index(){
        $categories = $this->iCategoryRepository->index();
        return response()->json($categories);
    }
    public function getAllCategory(){
        $Categorys = $this->iCategoryRepository->getAllCategory();
        return response()->json([
            'Categorys' => $Categorys,
        ]);
    }
    public function deleteCategory(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->iCategoryRepository->deleteCategory($request);
        return response()->json(['message' => 'Tage deleted']);
    }
    public function addNewCategory(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'name' => 'required|unique:categorys',
        ]);
        $this->iCategoryRepository->addNewCategory($request);
        return response()->json(['message' => 'Category added']);
    }
    public function updateCategory(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'name' => 'required|unique:categorys,name,'.$request->id,
            'id' => 'required',
        ]);
        $this->iCategoryRepository->updateCategory($request);
        return response()->json(['message' => 'category updated']);
    }
    public function getAllTagesCategory(){
        $Categorys = $this->iCategoryRepository->getAllCategory();
        $tages = $this->iCategoryRepository->getTages();

        $dataCategory=[];
        $dataTage=[];
        foreach($Categorys as $Category){
            $dataCategory[]=[
                'id'=>$Category->id,
                'name'=>$Category->name,
            ];
        }
        foreach($tages as $tage){
            $dataTage[]=[
                'id'=>$tage->id,
                'text'=>$tage->name,
            ];
        }
        return response()->json([
            'Categorys' => $dataCategory,
            'Tages' => $dataTage,
        ]);
    }
}
