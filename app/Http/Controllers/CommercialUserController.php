<?php

namespace App\Http\Controllers;

use App\CommercialUser;
use App\PartnerUs;
use App\Roles;
use App\User;
use App\UserRole;
use http\Cookie;
use Illuminate\Http\Request;

class CommercialUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commercialuser=CommercialUser::with('businessType','commercialUser')->get();
//        $commercialuser = CommercialUser::join('users', 'users.id', '=', 'commercial_users.user_id')
//            ->join('event_categories', 'event_categories.id', '=', 'commercial_users.category_id')
//            ->select('users.*', 'commercial_users.*', 'event_categories.*')
//            ->get();
        return response()->json($commercialuser,200);
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
        $this->validate($request,[
            'business_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|',
            'role'=>'required',
            'category_id' => 'required',
            'phone' => 'required',
            'contact_person' => 'required|string',
            'address' => 'required',
            'city' => 'required|string',
            'description' => 'required',
        ]);

        $user = new User([
            'name' => $request->contact_person,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->role,
        ]);

        CommercialUser::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'category_id' => $request->category_id,
            'phone' => $request->phone,
            'contact_person' => $request->contact_person,
            'address' => $request->address,
            'city' => $request->city,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Successfully created commercial user!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $commercialuser=CommercialUser::find($id);

        if(!isset($commercialuser)){
            return response()->json("commercial user not found",404);
        }

        $commercialuser->businessType;
        $commercialuser->commercialUser;
        return response()->json($commercialuser,200);
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
        $commercialuser=CommercialUser::find($id);

        if(!isset($commercialuser)){
            return response()->json("commercial user not found",404);
        }

        $commercialuser->commercialUser->delete();
        UserRole::where('user_id',$commercialuser->user_id)->delete();
        $commercialuser->delete();
        return response()->json('commercial user deleted',201);
    }
}
