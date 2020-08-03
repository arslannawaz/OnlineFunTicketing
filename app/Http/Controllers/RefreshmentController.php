<?php

namespace App\Http\Controllers;

use App\Event;
use App\Refreshment;
use App\RefreshmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefreshmentController extends Controller
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
        $refreshment=Refreshment::with('refreshmentDetail')->where('user_id',$userid)->get();
        return response()->json($refreshment,200);
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
            'item_name' => 'required',
            'size' => 'required',
            'price' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $refreshment=Refreshment::create([
            'user_id' => $userid,
            'item_name' => $request->item_name,
            'status' => 0,
        ]);

        $refreshment_size = explode(',',$request->size);
        $refreshment_price = explode(',',$request->price);
        for ($i=0; $i<count($refreshment_size); $i++){
            RefreshmentDetail::create([
                'refreshment_id' => $refreshment->id,
                'size' => $refreshment_size[$i],
                'price' => $refreshment_price[$i],
            ]);
        }
        return response()->json(['message'=>'Refreshment has been added.'],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $refreshment=Refreshment::find($id);
        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }

        $refreshment->refreshmentDetail;
        return response()->json($refreshment,200);
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
        $refreshment=Refreshment::find($id);
        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }

        $this->validate($request, [
            'price' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $refreshment->update([
            'status' => 0,
        ]);

        $refreshmentdetail = RefreshmentDetail::where('refreshment_id',$refreshment->id)->get();
        $refreshment_price = explode(',', $request->price);
            for ($i = 0; $i < count($refreshmentdetail); $i++) {
                $refreshmentdetail[$i]->update([
                    'price' => $refreshment_price[$i],
                ]);
            }

        return response()->json(['message'=>'Prices updated successfully'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $refreshment=Refreshment::find($id);
        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }

        RefreshmentDetail::where('refreshment_id',$refreshment->id)->delete();
        $refreshment->delete();
        return response()->json('Deleted successfully',201);
    }

    public function pendingRefreshment()
    {
        $refreshment=Refreshment::with('refreshmentDetail')->where('status','=',0)->get();
        return response()->json($refreshment,200);
    }

    public function approveRefreshment(Request $request, $id)
    {
        $refreshment=Refreshment::find($id);

        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }

        $this->validate($request, [
            'status' => 'required',
        ]);

        $refreshment->update($request->all());
        return response()->json(['message'=>'Approved successfully'],201);
    }

    public function deleteRefreshment($id)
    {
        $refreshment=Refreshment::find($id);
        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }

        RefreshmentDetail::where('refreshment_id',$refreshment->id)->delete();
        $refreshment->delete();
        return response()->json('Deleted successfully',201);
    }

    public function getAllRefreshments()
    {
        $refreshment=Refreshment::with('refreshmentDetail')->get();
        return response()->json($refreshment,200);
    }

    public function findRefreshmentById($id)
    {
        $refreshment=Refreshment::find($id);
        if(!isset($refreshment)){
            return response()->json("No data found",404);
        }
        $refreshment->refreshmentDetail;
        return response()->json($refreshment,200);
    }

    public function getAllRefereshmentsByCommercialUser($id)
    {

        $event=Event::find($id);

        if(!isset($event)){
            return response()->json("event not found",404);
        }

        $refreshment=Refreshment::with('refreshmentDetail')->where([
            'status' => 1,
            'user_id' => $event->user_id,
        ])->get();


        if($refreshment->isEmpty()){
            return response()->json(['message'=>'No Data Found'],200);
        }
        return response()->json($refreshment,200);
    }
}
