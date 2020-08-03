<?php

namespace App\Http\Controllers;

use App\BookingPayment;
use App\BookingRefreshment;
use App\BookingSeat;
use App\Discount;
use App\EventScreen;
use Illuminate\Http\Request;
use App\TicketBooking;
use App\User;
use Illuminate\Support\Facades\Auth;
use phpseclib\System\SSH\Agent\Identity;

class ReportingController extends Controller
{
    public function reportByEvent(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
        ]);

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingEvent','bookingPayment')->where('event_id',$request->event_id)->orderBy('id','DESC')->get();

        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function reportByEventCat(Request $request)
    {
        $this->validate($request, [
            'eventcat_id' => 'required',
        ]);

        $ticketbooking = TicketBooking::join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.category_id' => $request->eventcat_id,
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->orderby('booking_payments.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function reportByEventAndDate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'event_id' => 'required',
        ]);

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingEvent','bookingPayment')->where('event_id',$request->event_id)->whereDate('created_at','=',$request->date)->orderBy('id','DESC')->get();

        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function reportByAllEventAndDate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingEvent','bookingPayment')->whereDate('created_at','=',$request->date)->orderBy('id','DESC')->get();

        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function reportByEventAndMonth(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
            'event_id' => 'required',
        ]);

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingEvent','bookingPayment')
            ->where('event_id',$request->event_id)
            ->whereDate('created_at','>=',$request->from_date)
            ->whereDate('created_at','<=',$request->to_date)
            ->orderBy('id','DESC')->get();

        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function reportByAllEventAndMonth(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingEvent','bookingPayment')
            ->whereDate('created_at','>=',$request->from_date)
            ->whereDate('created_at','<=',$request->to_date)
            ->orderBy('id','DESC')->get();

        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }


    //Commercial Reports

    public function myReportByEvent(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.id' => $request->event_id,
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }


    public function myReportByEventCat(Request $request)
    {
        $this->validate($request, [
            'event_cat' => 'required',
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.category_id' => $request->event_cat,
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }


    public function myReportByEventDate(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
            'date' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.id' => $request->event_id,
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->whereDate('ticket_bookings.created_at','=',$request->date)
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }


    public function myReportByAllEventDate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->whereDate('ticket_bookings.created_at','=',$request->date)
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }


    public function myReportByEventMonth(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.id' => $request->event_id,
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->whereDate('ticket_bookings.created_at','>=',$request->from_date)
            ->whereDate('ticket_bookings.created_at','<=',$request->to_date)
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

    public function myReportByAllEventMonth(Request $request)
    {
        $this->validate($request, [
            'from_date' => 'required',
            'to_date' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $userid = $request->user()->id;
        }

        $ticketbooking = TicketBooking::
        join('event_times','event_times.id','=', 'ticket_bookings.time_id')
            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
            ->join('events','events.id','=', 'ticket_bookings.event_id')
            ->where([
                'events.user_id'=>$userid
            ])
            ->select('ticket_bookings.*','event_times.*','events.*','screen_types.*','booking_payments.*')
            ->whereDate('ticket_bookings.created_at','>=',$request->from_date)
            ->whereDate('ticket_bookings.created_at','<=',$request->to_date)
            ->orderby('ticket_bookings.id','DESC')
            ->get();


        if($ticketbooking->isEmpty()){
            return response()->json("booking not found",404);
        }

        return response()->json($ticketbooking,200);
    }

}
