<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landscape;

class LandscapeController extends Controller
{
    public function success($resp, $message){
        return response()->json([
            'isSuccess' => true, 
            'message' => $message, 
            'data' => $resp], 200);
    }

    public function failed($resp, $message){
        return response()->json([
            'isSuccess' => false, 
            'message' => $message, 
            'data' => $resp], 400);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $acidLoving_crop = landscape::all();
        if($acidLoving_crop->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($acidLoving_crop, "Acid Loving Crop records retrieved successfully");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            '*.type' => 'required|string|max:100',
        ]);

        if( landscape::insert($validated) ){
            return $this->success(count($validated), "Landscape records added successfully");
        }
        else{
            return $this->failed("", "Failed to add Landscape records");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
