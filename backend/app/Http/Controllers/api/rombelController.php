<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\kelas;
use Illuminate\Http\Request;

class rombelController extends Controller
{
    public function index()
    {
        $data=kelas::latest()->get();
        return response()->json($data);
    }
    public function rombelPost(Request $request)
    {
        $request->validate([
            'kelas'=>'required'
        ]);
        $data=kelas::updateOrCreate(['id'=>$request->id],[
            'kelas'=>$request->kelas
        ]);
        return response()->json($data);
    }
    public function rombelEdit($id)
    {
        $data=kelas::where('id', $id)->first();
        return response()->json($data);
    }
    public function rombelHapus($id)
    {
        if($id){
            $hapus=kelas::find($id);
            $hapus->delete();
        }
        return response()->json($hapus);
    }
}
