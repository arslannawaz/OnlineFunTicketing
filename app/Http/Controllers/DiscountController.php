<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }
        $discount=Discount::with('discountTime','discountScreen','discountEvent')->where('commercial_user_id',$userid)->orderby('id','DESC')->get();
        return response()->json($discount,200);
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
            'event_id' => 'required|unique:discounts',
            'screen_id' => 'required',
            'time_id' => 'required',
            'discount' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $event=Event::find($request->event_id);
        if($event->status==1) {
            Discount::create([
                'event_id' => $request->event_id,
                'screen_id' => $request->screen_id,
                'time_id' => $request->time_id,
                'discount' => $request->discount,
                'commercial_user_id' => $userid,
            ]);
            return response()->json(['message' => 'Deal and Discount Has Been Added.'], 201);
        }
        else{
            return response()->json(['message' => 'Event has not been approved yet.'], 201);
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

        $discount=Discount::find($id);

        if(!isset($discount)){
            return response()->json("No Deal found",404);
        }
        $discount->discountTime;
        $discount->discountScreen;
        $discount->discountEvent;
        return response()->json($discount,200);
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
        $discount=Discount::find($id);
        if(!isset($discount)){
            return response()->json("No Deal found",404);
        }

        $this->validate($request, [
            'discount' => 'required'
        ]);

        $discount->update($request->all());
        return response()->json(['message'=>'Updated Successfully'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discount=Discount::find($id);

        if(!isset($discount)){
            return response()->json("No Deal found",404);
        }
        $discount->delete();
        return response()->json(['message'=>'Deleted Successfully'],200);
    }

    public function getDiscountByEvent($id)
    {
        $discount = Discount::join('event_times','event_times.id','=', 'discounts.time_id')
            ->join('screen_types','screen_types.id','=', 'discounts.screen_id')
            ->where([
                'discounts.event_id' => $id,
            ])
            ->select('discounts.*', 'event_times.*','screen_types.*')
            ->get();

        return response()->json($discount,201);
    }
}
