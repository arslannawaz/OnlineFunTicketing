<?php

namespace App\Http\Controllers;

use App\Event;
use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $review = Review::where([
            'user_id' => $userid,
            'event_id' => $request->event_id
        ])->first();

        if(!isset($review)) {
            $userreview = Review::create([
                'event_id' => $request->event_id,
                'user_id' => $userid,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            return response()->json(['message' => 'Your review needs an admin approval.'], 201);
        }
        else{
            return response()->json(['message' => 'Review already submitted.'], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userreview=Review::find($id);

        if(!isset($userreview)){
            return response()->json("Review not found",404);
        }

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $this->validate($request, [
            'rating' => 'required',
            'comment' => 'required',
        ]);

        $userreview->update([
            'user_id' => $userid,
            'rating' =>$request->rating,
            'comment' =>$request->comment,
            'status' => 0,
        ]);
        return response()->json(['message'=>'Your review needs an admin approval.'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usereview=Review::find($id);

        if(!isset($usereview)){
            return response()->json("Review not found",404);
        }
        $usereview->delete();
        return response()->json('Review has been deleted',201);
    }

    public function pendingReviews()
    {
        $userreview=Review::with('event','user')->where('status','=',0);
        return response()->json($userreview->get(), 200);
    }

    public function approveReview(Request $request, $id)
    {
        $userreview=Review::find($id);

        if(!isset($userreview)){
            return response()->json("Review not found",404);
        }

        $userreview->update($request->all());
        return response()->json($userreview,201);
    }

    public function deleteByAdmin($id)
    {
        $usereview=Review::find($id);

        if(!isset($usereview)){
            return response()->json("Review not found",404);
        }
        $usereview->delete();
        return response()->json('Review has been deleted',201);
    }

    public function publicReviewsByEvent($id)
    {

        $usereview=Review::where([
            'event_id'=>$id,
            'status'=> 1,
        ])->get();

        if($usereview->isEmpty()){
            return response()->json('No review found',201);
        }
        return response()->json($usereview,201);
    }

}
