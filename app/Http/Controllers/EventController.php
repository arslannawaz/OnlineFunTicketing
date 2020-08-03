<?php

namespace App\Http\Controllers;

use App\CommercialUser;
use Illuminate\Http\Request;
use App\Event;
use App\EventCategory;
use App\Seat;
use App\EventTime;
use App\EventScreen;
use App\ScreenType;
use Illuminate\Support\Facades\Auth;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes','eventreviews')->orderBy('id','DESC')->get();
        return response()->json($event,200);
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event=Event::find($id);
        if(!isset($event)){
            return response()->json("event not found",404);
        }

        $event->eventcategory;
        $event->eventseats;
        $event->eventtiming;
        $event->screentypes;

        return response()->json($event,200);
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
        $event=Event::find($id);

        if(!isset($event)){
            return response()->json("event not found",404);
        }


        $this->validate($request, [
            'description' => 'required',
            'datetime' => 'required',
            'totalseats' => 'required',
            'screentype_id' => 'required',
            'price' => 'required',
        ]);

        $event->update($request->all());
        if($file=$request->file('image')) {
            $filename = time() . $file->getClientOriginalName();
            $file->move(public_path('/images/'),$filename);
            $eventid=Event::find($event->id)->update([
                'image'=>'images/'.$filename,
            ]);
        }

        Seat::where('event_id',$event->id)->update([
            'totalseats' => $request->totalseats,
        ]);

        if($request->datetime) {
            EventTime::where('event_id', $event->id)->delete();
            $eventtime = explode(',',$request->datetime);
            for ($i=0; $i<count($eventtime); $i++){
                $eventtime11 = explode('T',$eventtime[$i]);
                EventTime::create([
                    'event_id' => $event->id,
                    'date' => $eventtime11[0],
                    'time' => $eventtime11[1],
                ]);
            }
        }

        if($request->screentype_id) {
            EventScreen::where('event_id', $event->id)->delete();
            $eventscreens = explode(',',$request->screentype_id);
            $price = explode(',',$request->price);
            for ($i=0; $i<count($eventscreens); $i++){
                EventScreen::create([
                    'event_id' => $event->id,
                    'screentype_id' => $eventscreens[$i],
                    'price' => $price[$i],
                ]);
            }
        }

        return response()->json($event,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event=Event::find($id);

        if(!isset($event)){
            return response()->json("event not found",404);
        }

        unlink(public_path()."/".$event->image);
        $event->eventseats->delete();
        EventTime::where('event_id',$event->id)->delete();
        EventScreen::where('event_id',$event->id)->delete();
        $event->delete();
        return response()->json('event deleted',201);
    }

    public function pendingEvents()
    {
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where('status','=',0)->get();

        if($event->isEmpty()){
            return response()->json('No pending event', 200);
        }
        return response()->json($event, 200);
    }

    public function approveEvents(Request $request, $id)
    {
        $event=Event::find($id);

        if(!isset($event)){
            return response()->json("event not found",404);
        }

        $event->update($request->all());
        return response()->json($event,201);
    }

    public function approvedEvents()
    {
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where('status',1)->get();
        return response()->json($event,200);
    }

    public function selectEvent(Request $request){

        $this->validate($request, [
            'location' => 'required',
            'category_id' => 'required',
        ]);

        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')
            ->where([
            'events.status'=>1,
            'events.category_id'=>$request->category_id,
            'events.location'=>$request->location
        ])->get();

        return response()->json($event,200);
    }

    public function getPublicEventByID($id)
    {
        $event=Event::find($id);
        if(!isset($event)){
            return response()->json("event not found",404);
        }

        $event->eventcategory;
        $event->eventseats;
        $event->eventtiming;
        $event->screentypes;
        return response()->json($event,200);
    }

    public function getEventByCategory(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
        ]);

        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where([
            'status'=>1,
            'category_id'=>$request->category_id,
        ])->get();

        return response()->json($event,200);
    }

    public function getAllLocations(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
        ]);

        $commercialuser = CommercialUser::where(['category_id' => $request->category_id])->select('business_name','address')->get();

        return response()->json($commercialuser,200);
    }
}
