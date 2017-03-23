<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParkingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $parkingspots = \App\ParkingSpot::all();

        return view('parkinglot', compact('parkingspots'));
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
        // Check if car is already in here
        $parkingspot = \App\ParkingSpot::where('licenseplate', $request->licenseplate)->first();
        if($parkingspot){
            abort(403, 'You can\'t be in here twice, mate');   
        }

        // Check if full
        $parkingspot = \App\ParkingSpot::where('occupied', 0)->first();

        if(! $parkingspot){
            abort(403, 'There\'s motha f-ing snakes in this motha f-ing parkin lot');
        }

        // Generate a unique ticket key
        $uniquekey = md5(uniqid(rand(), true));

        // Update record
        $parkingspot->licenseplate = $request->licenseplate;
        $parkingspot->occupied = true;
        $parkingspot->ticket_id =$uniquekey;
        $parkingspot->updated_at = \Carbon\Carbon::now(); // Used for payment tracking
        $parkingspot->save();

        $data = array('id' => $parkingspot->id, 'ticket_id' => $parkingspot->ticket_id);

        // Tell the user their parking spot # and their unique parking ticket key
        return view('parkingticket')->with($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
