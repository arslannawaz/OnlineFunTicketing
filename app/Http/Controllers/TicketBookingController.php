<?php

namespace App\Http\Controllers;

use App\BookingPayment;
use App\BookingRefreshment;
use App\BookingSeat;
use App\Discount;
use App\EventScreen;
use App\Ewallet;
use Illuminate\Http\Request;
use App\TicketBooking;
use App\User;
use Illuminate\Support\Facades\Auth;
use phpseclib\System\SSH\Agent\Identity;

class TicketBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingUser','bookingEvent','bookingRefreshment','bookingPayment')->orderby('id','DESC')->get();
        return response()->json($ticketbooking,200);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $booking=TicketBooking::find($id);
        if(!isset($booking)){
            return response()->json("booking not found",404);
        }

        $booking->bookingTime;
        $booking->seatNumber;
        $booking->bookingScreen;
        $booking->bookingUser;
        $booking->bookingEvent;
        $booking->bookingRefreshment;
        $booking->bookingPayment;
        return response()->json($booking,201);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticketingbook=TicketBooking::find($id);
        if(!isset($ticketingbook)){
            return response()->json("booking not found",404);
        }

        $ticketingbook->bookingPayment->delete();
        BookingSeat::where('booking_id',$ticketingbook->id)->delete();
        BookingRefreshment::where('booking_id',$ticketingbook->id)->delete();
        $ticketingbook->delete();
        return response()->json('booking deleted',201);
    }

    public function myBooking(Request $request)
    {
        if (Auth::guard('api')->check())
        {
            $id = $request->user()->id;
        }
        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingUser','bookingEvent','bookingRefreshment','bookingPayment')->where('user_id',$id)->orderby('id','DESC')->get();
        return response()->json($ticketbooking,200);
    }

    public function makeBooking(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
            'screen_id' => 'required',
            'time_id' => 'required',
            'totaltickets' => 'required',
            'seatnumber' => 'required'
        ]);

        if (Auth::guard('api')->check())
        {
            $id = $request->user()->id;
        }

        $eventscreen=EventScreen::where([
            'event_id' => $request->event_id,
            'screentype_id' => $request->screen_id,
        ])->first();

        $price=$eventscreen->price;

        $discount=Discount::where([
            'event_id' => $request->event_id,
            'screen_id' => $request->screen_id,
            'time_id' => $request->time_id,
        ])->first();

        if(!isset($discount)){
            $ticketbooking = new TicketBooking([
                'event_id' => $request->event_id,
                'screen_id' => $request->screen_id,
                'time_id' => $request->time_id,
                'user_id' => $id,
                'totaltickets' => $request->totaltickets,
            ]);
            $ticketbooking->save();

            $seatnumber = explode(',', $request->seatnumber);
            for ($i = 0; $i < count($seatnumber); $i++) {
                BookingSeat::create([
                    'booking_id' => $ticketbooking->id,
                    'seatnumber' => $seatnumber[$i],
                ]);
            }

            if($request->item){
                $item = explode(',', $request->item);
                $size = explode(',', $request->size);
                $quantiy = explode(',', $request->quantity);
                $refprice = explode(',', $request->price);

                for ($i = 0; $i < count($item); $i++) {
                    BookingRefreshment::create([
                        'booking_id' => $ticketbooking->id,
                        'item' => $item[$i],
                        'size' => $size[$i],
                        'quantity' => $quantiy[$i],
                        'price' => $refprice[$i]*$quantiy[$i],
                    ]);
                }
            }

            $totalrefprice=0;
            $bookingrefreshment = BookingRefreshment::where('booking_id',$ticketbooking->id)->get();
            foreach ($bookingrefreshment as $bref){
                $totalrefprice=$totalrefprice+$bref->price;
            }

            $totalticketprice=($price*$request->totaltickets);
            $totalprice=$totalticketprice+$totalrefprice;
            BookingPayment::create([
                'booking_id' => $ticketbooking->id,
                'price' => $totalprice,
            ]);

            $booking=TicketBooking::find($ticketbooking->id);
            $booking->bookingTime;
            $booking->seatNumber;
            $booking->bookingScreen;
            $booking->bookingUser;
            $booking->bookingEvent;
            $booking->bookingRefreshment;
            $booking->bookingPayment;
            return response()->json($booking, 201);
        }
        else {
            $ticketbooking = new TicketBooking([
                'event_id' => $request->event_id,
                'screen_id' => $request->screen_id,
                'time_id' => $request->time_id,
                'user_id' => $id,
                'totaltickets' => $request->totaltickets,
            ]);
            $ticketbooking->save();

            $seatnumber = explode(',', $request->seatnumber);
            for ($i = 0; $i < count($seatnumber); $i++) {
                BookingSeat::create([
                    'booking_id' => $ticketbooking->id,
                    'seatnumber' => $seatnumber[$i],
                ]);
            }

            if($request->item){
                $item = explode(',', $request->item);
                $size = explode(',', $request->size);
                $quantiy = explode(',', $request->quantity);
                $refprice = explode(',', $request->price);

                for ($i = 0; $i < count($item); $i++) {
                    BookingRefreshment::create([
                        'booking_id' => $ticketbooking->id,
                        'item' => $item[$i],
                        'size' => $size[$i],
                        'quantity' => $quantiy[$i],
                        'price' => $refprice[$i]*$quantiy[$i],
                    ]);
                }
            }

            $totalrefprice=0;
            $bookingrefreshment = BookingRefreshment::where('booking_id',$ticketbooking->id)->get();
            foreach ($bookingrefreshment as $bref){
                $totalrefprice=$totalrefprice+$bref->price;
            }

            $totalticketprice=($price*$request->totaltickets)*((100-$discount->discount)/100);
            $totalprice=$totalticketprice+$totalrefprice;
            BookingPayment::create([
                'booking_id' => $ticketbooking->id,
                'price' => $totalprice,
            ]);
            $booking=TicketBooking::find($ticketbooking->id);
            $booking->bookingTime;
            $booking->seatNumber;
            $booking->bookingScreen;
            $booking->bookingUser;
            $booking->bookingEvent;
            $booking->bookingRefreshment;
            $booking->bookingPayment;
            return response()->json($booking, 201);
        }
    }

    public function getBookedSeats(Request $request){

        $this->validate($request, [
            'event_id' => 'required',
            'screen_id' => 'required',
            'time_id' => 'required',
        ]);

        $bookedseat = TicketBooking::join('booking_seats','booking_seats.booking_id','=', 'ticket_bookings.id')
            ->where([
                'event_id' => $request->event_id,
                'screen_id' => $request->screen_id,
                'time_id' => $request->time_id,
            ])
            ->select('ticket_bookings.*', 'booking_seats.*')
            ->get();

        return response()->json($bookedseat,200);
    }

    public function getBookingByEvent(Request $request){

        $this->validate($request, [
            'event_id' => 'required',
            'screen_id' => 'required',
            'time_id' => 'required',
        ]);

//        $bookedseat = TicketBooking::join('booking_seats','booking_seats.booking_id','=', 'ticket_bookings.id')
//            ->join('users','users.id','=', 'ticket_bookings.user_id')
//            ->join('event_times','event_times.id','=', 'ticket_bookings.time_id')
//            ->join('screen_types','screen_types.id','=', 'ticket_bookings.screen_id')
//            ->join('booking_payments','booking_payments.booking_id','=', 'ticket_bookings.id')
//            ->where([
//                'ticket_bookings.event_id' => $request->event_id,
//                'ticket_bookings.screen_id' => $request->screen_id,
//                'ticket_bookings.time_id' => $request->time_id,
//            ])
//            ->select('ticket_bookings.*', 'booking_seats.*','users.name','event_times.*','screen_types.*','booking_payments.*')
//            ->orderby('ticket_bookings.id','DESC')
//            ->get();

        $ticketbooking=TicketBooking::with('bookingTime','seatNumber','bookingScreen','bookingUser','bookingEvent','bookingRefreshment','bookingPayment')
            ->where([
                'event_id' => $request->event_id,
                'screen_id' => $request->screen_id,
                'time_id' => $request->time_id,
            ])->orderby('id','DESC')->get();
        return response()->json($ticketbooking,200);
    }

    public function deleteBooking($id){
        $ticketingbook=TicketBooking::find($id);

        if(!isset($ticketingbook)){
            return response()->json("booking not found",404);
        }

        $ticketingbook->bookingPayment->delete();
        BookingSeat::where('booking_id',$ticketingbook->id)->delete();
        BookingRefreshment::where('booking_id',$ticketingbook->id)->delete();
        $ticketingbook->delete();
        return response()->json('booking deleted',201);
    }

    public function findMyBooking($id){

        $booking=TicketBooking::find($id);
        if(!isset($booking)){
            return response()->json("booking not found",404);
        }

        $booking->bookingTime;
        $booking->seatNumber;
        $booking->bookingScreen;
        $booking->bookingUser;
        $booking->bookingEvent;
        $booking->bookingRefreshment;
        $booking->bookingPayment;
        return response()->json($booking,201);
    }

    public function findCommercialBooking($id){

        $booking=TicketBooking::find($id);
        if(!isset($booking)){
            return response()->json("booking not found",404);
        }

        $booking->bookingTime;
        $booking->seatNumber;
        $booking->bookingScreen;
        $booking->bookingUser;
        $booking->bookingEvent;
        $booking->bookingRefreshment;
        $booking->bookingPayment;
        return response()->json($booking,201);
    }

    //confirm ticket
    public function confirmTicket(Request $request)
    {

        $this->validate($request, [
            'booking_id' => 'required',
        ]);


        $bookingpayment=BookingPayment::where(['booking_id'=>$request->booking_id])->first();
        if(!isset($bookingpayment)){
            return response()->json('No booking found',404);
        }
        else {

            $bookingpayment->update([
                'status' => 1
            ]);

            $ticketbooking=TicketBooking::find($request->booking_id);


            $ewallet = Ewallet::where(['user_id' => $ticketbooking->user_id])->first();
            if (!$ewallet) {
                return response()->json('No Wallet found', 404);
            }
            $points = $ewallet->points + 100;
            $ewallet->update([
                'points' => $points
            ]);
            return response()->json(['message'=>'Payment has been made'], 201);
        }
    }
}
