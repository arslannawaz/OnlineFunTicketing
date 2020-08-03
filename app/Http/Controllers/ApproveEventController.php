<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class ApproveEventController extends Controller
{
    public function pendingEvents()
    {
        $event=Event::with('eventcategory','eventseats','eventtiming','eventscreens')->where('status',0)->get();
        return response()->json($event,200);
    }
}
