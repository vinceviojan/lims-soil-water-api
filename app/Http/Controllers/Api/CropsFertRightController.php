<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\crops_fert_right;
use Illuminate\Http\Request;

class CropsFertRightController extends Controller
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
        $fertRight = crops_fert_right::all();
        if($fertRight->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($fertRight, "Retrieved successfully");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
