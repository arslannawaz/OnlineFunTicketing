<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventScreen;
use App\EventTime;
use App\Seat;
use App\CommercialUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialEventController extends Controller
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
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where('user_id',$userid)->orderBy('id','DESC')->get();
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
        $this->validate($request, [
            'name' => 'required|unique:events',
            'description' => 'required',
            'datetime' => 'required',
            'totalseats' => 'required',
            'screentype_id' => 'required',
            'image' => 'required',
            'price' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $commercialuser = CommercialUser::where(['user_id' => $userid])->first();
        $businessname=$commercialuser->business_name;
        $address=$commercialuser->address;
        $location = $businessname." ".$address;
        $category=$commercialuser->category_id;

        if($file=$request->file('image')) {
            $filename = time() . $file->getClientOriginalName();
            $file->move(public_path('/images/'),$filename);
            $events=Event::create([
                'name' => $request->name,
                'description' =>$request->description,
                'user_id'=> $userid,
                'location' => $location,
                'category_id' => $category,
                'image'=>'images/'.$filename
            ]);
        }

        Seat::create([
            'event_id' => $events->id,
            'totalseats' => $request->totalseats,
        ]);

        $eventtime = explode(',',$request->datetime);
        for ($i=0; $i<count($eventtime); $i++){
            $eventtime11 = explode('T',$eventtime[$i]);
                 EventTime::create([
                      'event_id' => $events->id,
                      'date' => $eventtime11[0],
                      'time' => $eventtime11[1],
                 ]);
        }

        $eventscreens = explode(',',$request->screentype_id);
        $price = explode(',',$request->price);
        for ($i=0; $i<count($eventscreens); $i++){
            EventScreen::create([
                'event_id' => $events->id,
                'screentype_id' => $eventscreens[$i],
                'price' => $price[$i],
            ]);
        }

        return response()->json($events,201);
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

    public function myPendingEvents(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where(['status'=>0,'user_id'=>$userid]);
        return response()->json($event->get(), 200);
    }

    public function myApprovedEvents(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }
        $event=Event::with('eventcategory','eventseats','eventtiming','screentypes')->where(['status'=>1,'user_id'=>$userid])->get();
        return response()->json($event,200);
    }

}
