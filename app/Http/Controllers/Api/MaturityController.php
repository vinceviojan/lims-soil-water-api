<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\maturity;

class MaturityController extends Controller
{
    /**
     * Display a listing of the resource.
     */

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

    public function index()
    {
        $maturity = maturity::all();
        if($maturity->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($maturity, "Acid Loving Crop records retrieved successfully");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            '*.name' => 'required|string|max:100',
            '*.code' => 'required|string|max:100',
            '*.date_range' => 'required|string|max:100',
        ]);

        if( maturity::insert($validated) ){
            return $this->success(count($validated), "Maturity records added successfully");
        }
        else{
            return $this->failed("", "Failed to add Maturity records");
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
