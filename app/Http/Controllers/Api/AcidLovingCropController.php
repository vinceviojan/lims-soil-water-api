<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\acid_loving_crop;
use Illuminate\Http\Request;
use app\Models\AcidLovingCrop;

class AcidLovingCropController extends Controller
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
        $acidLoving_crop = acid_loving_crop::all();
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
        // dd($request->all());
         $validated = $request->validate([
            '*.crops' => 'required|string|max:100',
            '*.category_code' => 'required|string|max:100',
            '*.category' => 'required|string|max:200',
            '*.min_ph' => 'nullable|numeric',
            '*.max_ph' => 'nullable|numeric',
            '*.created_at' => 'nullable',
        ]);

        $now = now();
        foreach ($validated as &$record) {
            $record['created_at'] = $now;
            $record['updated_at'] = $now;
        }

        if( acid_loving_crop::insert($validated) ){
            return $this->success(count($validated), "Acid Loving Crop records added successfully");
        }
        else{
            return $this->failed("", "Failed to add Acid Loving Crop records");
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
