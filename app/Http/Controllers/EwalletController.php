<?php

namespace App\Http\Controllers;

use App\Ewallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EwalletController extends Controller
{


    public function viewMyEwallet(Request $request)
    {

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ewallet=Ewallet::where(['user_id'=>$userid])->get();

        if($ewallet->isEmpty()){
            return response()->json('No data found',201);
        }
        return response()->json($ewallet,201);
    }

}
