<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\data_katalog;
use App\Models\katalog;
use Illuminate\Http\Request;

class katalogController extends Controller
{
    public function index()
    {
        $data=katalog::latest()->get();
        return response()->json($data);
    }
    public function katalogPost(Request $request)
    {
        $request->validate([
            'topik'=>'required'
        ]);
        $data=katalog::updateOrCreate(['id'=>$request->id],[
            'topik'=>$request->topik
        ]);
        return response()->json($data);
    }
    public function katalogEdit($id)
    {
        $data=katalog::where('id', $id)->first();
        return response()->json($data);
    }
    public function katalogHapus($id)
    {
        if($id){
            $hapus=katalog::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
    public function pilihInv($id, $pilihan)
    {
        $single_id = explode(',' , $pilihan);
        $katalog_id=$id;
        foreach($single_id as $id){
            data_katalog::create([
                'inventaris_id' => $id,
                'katalog_id' => $katalog_id 
            ]);
        }
        return response()->json();
    }
    public function katalogData($id)
    {
        $data=data_katalog::with('inventaris')->where('katalog_id', $id)->latest()->get();
        return response()->json($data);
    }
    public function katalogDataHapus($id)
    {
        if($id){
            $hapus=data_katalog::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
}
