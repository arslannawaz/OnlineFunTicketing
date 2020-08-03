<?php

namespace App\Http\Controllers;
use App\PartnerUs;
use Illuminate\Http\Request;

class PartnerWithUsController extends Controller
{
    public function index()
    {
        $partnerwithus=PartnerUs::with('businessType')->orderBy('id','DESC')->get();
        return response()->json($partnerwithus,200);
    }

    public function submitForm(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:partner_uses|unique:users',
            'business_name' => 'required|string',
            'category_id' => 'required',
            'phone' => 'required',
            'contact_person' => 'required|string',
            'address' => 'required',
            'city' => 'required|string',
            'file' => 'required',
            'description' => 'required',
        ]);

        $partnerwithus=PartnerUs::create($request->all());
        if($file=$request->file('file')) {
            $filename = time() . $file->getClientOriginalName();
            $file->move(public_path('/images/'),$filename);
            PartnerUs::find($partnerwithus->id)->update([
                'file'=>'images/'.$filename
            ]);
        }
        return response()->json(['message'=>'Form has been submitted successfully'],201);
    }

    public function show($id)
    {
        $partnerwithus=PartnerUs::find($id);
        if(!isset($partnerwithus)){
            return response()->json("No data found",404);
        }

        $partnerwithus->businessType;
        return response()->json($partnerwithus,200);
    }
}
