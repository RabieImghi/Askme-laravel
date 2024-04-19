<?php 

namespace App\Repositories; 

use App\Models\Tage; 

use App\Repositories\Interfaces\ITageRepository; 

class TageRepository implements ITageRepository 
{ 
    public function index(){
        return Tage::with('posts')->get();
    }
    public function getAllTages(){
        return Tage::get();
    }
    public function getAllTagesAdmin($take,$skip){
        return Tage::skip($skip)->take($take)->get();
    }
    public function deleteTage($request){
        $tage = Tage::find($request->id);
        $tage->delete();
    }
    public function addNewTage($request){
        Tage::create([
            'name' => $request->name,
            'descriprtion' => $request->descriprtion,
        ]);
    }
    public function updateTage($request){
        $tage = Tage::find($request->id);
        $tage->name = $request->name;
        $tage->descriprtion = $request->descriprtion;
        $tage->save();
    }
}