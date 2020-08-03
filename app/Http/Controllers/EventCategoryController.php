<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventCategory;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(EventCategory::get(),200);
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
            'categoryname' => 'required|unique:event_categories',
        ]);
        $eventcategory=EventCategory::create($request->all());
        return response()->json($eventcategory,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $eventcategory=EventCategory::find($id);

        if(!isset($eventcategory)){
            return response()->json("event category not found",404);
        }

        return response()->json($eventcategory,200);
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
        $eventcategory=EventCategory::find($id);

        if(!isset($eventcategory)){
            return response()->json("event category not found",404);
        }

        $this->validate($request, [
            'categoryname' => 'required|unique:event_categories',
        ]);

        $eventcategory->update($request->all());
        return response()->json($eventcategory,201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $eventcategory=EventCategory::find($id);

        if(!isset($eventcategory)){
            return response()->json("event category not found",404);
        }
        $eventcategory->delete();
        return response()->json('event category has been deleted successfully',201);
    }

    public function getPublicEventCategories()
    {
        return response()->json(EventCategory::get(),200);
    }
}
