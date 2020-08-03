<?php

namespace App\Http\Controllers;

use App\ScreenType;
use Illuminate\Http\Request;

class ScreenTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ScreenType::get(),200);

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
            'screenname' => 'required|unique:screen_types',
        ]);

        $screentype=ScreenType::create($request->all());
        return response()->json($screentype,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $screentype=ScreenType::find($id);

        if(!isset($screentype)){
            return response()->json("screentype not found",404);
        }

        return response()->json($screentype,200);
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
        $screentype=ScreenType::find($id);

        if(!isset($screentype)){
            return response()->json("screentype not found",404);
        }

        $this->validate($request, [
            'screenname' => 'required|unique:screen_types',
        ]);

        $screentype->update($request->all());
        return response()->json($screentype,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $screentype=ScreenType::find($id);

        if(!isset($screentype)){
            return response()->json("screentype not found",404);
        }
        $screentype->delete();
        return response()->json('screentype deleted',201);
    }

    public function getScreenType()
    {
        return response()->json(ScreenType::get(),200);

    }
}
