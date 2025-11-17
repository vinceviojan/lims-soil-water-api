<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\acid_loving_crop;
use Illuminate\Http\Request;
use app\Models\AcidLovingCrop;
use App\Models\crops;

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
         $validated = $request->validate([
            '*.crops' => 'required|string|max:100',
            '*.category_code' => 'required|string|max:100',
            '*.category' => 'required|string|max:200',
            '*.min_ph' => 'nullable|numeric',
            '*.max_ph' => 'nullable|numeric',
            '*.created_at' => 'nullable',
        ]);

        $now = now();

        $successCnt = 0;
        $failCnt = 0;
        $dataCnt = 0;
        $failedMessage = [];
        foreach ($validated as &$data) {
            $data['created_at'] = $now;
            $data['updated_at'] = $now;

            if($data["crops"] == "corn"){
                
            }
            else{
                $crop = crops::where('type', strtolower($data["crops"]))->first();
            }
                
            if($crop){
                $data["crops"] = $crop["code"];
                $resp = $this->insertData($data);
                $dataCnt++;
                $getData = $resp->getData();
                if($getData->isSuccess == true){
                    $successCnt++;
                }
                else{
                    $failCnt++;
                    $failedMessage[] = $getData->message . " Cnt #" . $dataCnt;
                }
            }
            else{
                $failCnt++;
                $failedMessage[] = "Crop type " . $data["crops"] . " does not exist. Cnt #" . $dataCnt;
            }
        }

        if($successCnt > 0){
            return $this->success(
                [
                    'successful_inserts' => $successCnt,
                    'failed_inserts' => $failCnt,
                    'failed_messages' => $failedMessage
                ], 
                "Data inserted successfully."
            );
        }
        else{
            return $this->failed($failedMessage, "Failed to insert data.");
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

    public function insertData($request)
    {
        $recordExists = acid_loving_crop::where('crops', $request['crops'])
                        ->where('category_code', $request['category_code'])
                        ->where('min_ph', $request['min_ph'])
                        ->where('max_ph', $request['max_ph'])
                        ->exists();

        if( !$recordExists ) {
            if( acid_loving_crop::insert($request) ){
                return $this->success("", "Acid Loving Crop records added successfully");
            }
            else{
                return $this->failed("", "Failed to add Acid Loving Crop records");
            }
        }
        else {
            return $this->failed("", "Duplicate Acid Loving Crop record found");
        }
    }

}
