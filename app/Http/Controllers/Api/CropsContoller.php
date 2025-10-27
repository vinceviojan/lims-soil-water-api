<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\crops;
use Illuminate\Http\Request;

class CropsContoller extends Controller
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
        $crops = crops::all();
        if($crops->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($crops, "Retrieved successfully");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            '*.type' => 'required|string|max:200',
        ]);

        foreach ($validated as &$record) {
            $record['code'] = strtolower(str_replace(' ', '_', $record['type']));
        }

        if(crops::insert($validated)){
            return $this->success(count($validated), "Crops records added successfully");
        }
        else{
            return $this->failed("", "Failed to add Crops records");
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
