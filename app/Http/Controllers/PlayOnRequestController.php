<?php

namespace App\Http\Controllers;

use App\PlayOnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class PlayOnRequestController extends Controller
{
    public function submitRequest(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $this->validate($request, [
            'movie_name' => 'required',
        ]);

        $checkrequest = PlayOnRequest::where([
            'user_id' => $userid,
            'movie_name' => $request->movie_name
        ])->first();

        if(!isset($checkrequest)) {
            PlayOnRequest::create([
                'user_id' => $userid,
                'movie_name' => $request->movie_name,
            ]);
            return response()->json(['message' => 'Your request has been submitted!'], 201);
        }
        return response()->json(['message' => 'Request already submitted'], 201);

    }

    public function getMyRequest(Request $request)
    {

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $playonrequest=PlayOnRequest::where('user_id',$userid)->get();
        if($playonrequest->isNotEmpty()){
            return response()->json($playonrequest, 201);
        }
        return response()->json('No request available',201);

    }

    public function getAllRequest()
    {

        $playonrequest = DB::table('play_on_requests')
            ->select(DB::raw('movie_name, count(*) as requested'))
            ->groupBy('movie_name')
            ->get();

        if($playonrequest){
            return response()->json($playonrequest, 201);
        }
        return response()->json('No request available',201);

    }
}
