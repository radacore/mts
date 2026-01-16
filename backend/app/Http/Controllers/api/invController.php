<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\inventaris;
use Illuminate\Http\Request;

class invController extends Controller
{
    public function index()
    {
        $data=inventaris::latest()->get();
        return response()->json($data);
    }
    public function inventarisPost(Request $request)
    {
        $request->validate([
            'noreg'=>'required',
            'katalog'=>'required',
            'nabar'=>'required',
            'satuan'=>'required',
            'vol'=>'required|numeric',
            'thn_masuk'=>'required',
            'thn_pakai'=>'required',
            'jml'=>'required|numeric',
            'lokasi'=>'required',
            'spec'=>'required',
            'konbaik'=>'required|numeric',
            'konrusak'=>'required|numeric',
        ]);
        $data=inventaris::updateOrCreate(['id'=>$request->id],[
            'noreg'=>$request->noreg,
            'katalog'=>$request->katalog,
            'nabar'=>$request->nabar,
            'satuan'=>$request->satuan,
            'vol'=>$request->vol,
            'merek'=>$request->merek,
            'tipe'=>$request->tipe,
            'produsen'=>$request->produsen,
            'asal'=>$request->asal,
            'thn_masuk'=>$request->thn_masuk,
            'thn_pakai'=>$request->thn_pakai,
            'jml'=>$request->jml,
            'kondisi'=>$request->jml,
            'konbaik'=>$request->konbaik,
            'konrusak'=>$request->konrusak,
            'lokasi'=>$request->lokasi,
            'spec'=>$request->spec,
        ]);
        return response()->json($data);
    }
    public function inventarisEdit($id)
    {
        $data=inventaris::where('id', $id)->first();
        return response()->json($data);
    }
    public function inventarisHapus($id)
    {
        if($id){
            $hapus=inventaris::find($id);
            $hapus->delete();
        }
    }
    public function inventarisFoto(Request $request)
    {
        $request->validate([
            'foto'=>'required|image|mimes:jpeg,png,jpg,gif,svg,'
        ]);
        if($request->id){
            $upload=inventaris::find($request->id);
            $foto=$request->file('foto')->store('inventaris','public');
            $upload->update([
                'foto'=>$foto
            ]);
        }
        return response()->json($upload);
    }
}
