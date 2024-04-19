<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tage;
use App\Repositories\Interfaces\ITageRepository;

class TageController extends Controller
{
    protected $tageRepository;
    public function __construct(ITageRepository $tageRepository){
        $this->tageRepository = $tageRepository;
    }
    public function index() {
        $tages = $this->tageRepository->index();
        return response()->json($tages);
    }
    public function getAllTages(){
        $tages =$this->tageRepository->getAllTages();
        $dataTage=[];
        foreach($tages as $tage){
            $dataTage[]=[
                'id'=>$tage->id,
                'name'=>$tage->name,
                'descriprtion'=>$tage->descriprtion,
            ];
        }
        return response()->json([
            'tages' => $dataTage,
        ]);
    }
    public function getAllTagesAdmin(Request $request,$skip){
        $take=6;
        if($request->type && $request->type=="user" ){
           $take=9; 
        }
        $tages = $this->tageRepository->getAllTagesAdmin($take,$skip);
        $dataTage=[];
        foreach($tages as $tage){
            $dataTage[]=[
                'id'=>$tage->id,
                'name'=>$tage->name,
                'descriprtion'=>$tage->descriprtion,
            ];
        }
        return response()->json([
            'tages' => $dataTage,
            'count' => Tage::count(),
        ]);
    }
    public function deleteTage(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $this->tageRepository->deleteTage($request);
        return response()->json(['message' => 'Tage deleted']);
    }
    public function addNewTage(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'name' => 'required|unique:tages',
            'descriprtion' => 'required',
        ]);
        $this->tageRepository->addNewTage($request);
        return response()->json(['message' => 'Tage added']);
    }
    public function updateTage(Request $request){
        if(!$request->user()) return response()->json(['message'=>'Unauthenticated'],401);
        $request->validate([
            'name' =>  'required|unique:tages,name,' . $request->id,
            'descriprtion'=>'required',
            'id' => 'required',
        ]);
        $this->tageRepository->updateTage($request);
        return response()->json(['message' => 'Tage updated']);
    }
}
