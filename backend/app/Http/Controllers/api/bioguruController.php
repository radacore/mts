<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\bioguru;
use Illuminate\Http\Request;

class bioguruController extends Controller
{
    public function guruBio()
    {
        $data=bioguru::where('user_id', Auth()->User()->id)->first();

        return response()->json($data);
    }
    public function guruBioUpdate(Request $request)
    {
        $cek=bioguru::where('user_id', Auth()->User()->id)->first();
        if(empty($cek->id)){
            $data=bioguru::create([
                'user_id'=>Auth()->User()->id,
                'nip'=>$request->nip,
                'hp'=>$request->hp
            ]);
        }else{
            $data=bioguru::updateOrCreate(['id'=>$request->id],[
                'nip'=>$request->nip,
                'hp'=>$request->hp,
            ]);
        }
        return response()->json($data);
        
    }
}
