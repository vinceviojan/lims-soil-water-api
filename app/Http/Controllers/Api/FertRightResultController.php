<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\crops;

class FertRightResultController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $successCnt = 0;
        $failCnt = 0;
        $dataCnt = 0;
        $failedMessage = [];
        $name = "";
        $dataList = $request->all();
        foreach ($dataList as $data) {
            
            if($data["crop_type"] == "corn"){
                
            }
            else{
                $crop = crops::where('type', $data["crop_type"])->first();
            }
                
            if($crop){
                $data["crop_type"] = $crop["code"];
                if($name != $data["crop_type"]){
                    $name = $data["crop_type"];
                    $tableName = $this->createDynamicTable($data);
                }
                
                $insert = $this->insertJsonIntoDynamic($tableName, $data);
                $dataCnt++;
                $getData = $insert->getData();
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
                $failedMessage[] = "Crop type " . $data["crop_type"] . " does not exist. Cnt #" . $dataCnt;
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


    public function createDynamicTable  ($request): string
    {
        $tableName = strtolower($request["crop_type"] . '_fert_right');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('crop_type', 100)->nullable();
                $table->string('om', 5)->nullable();
                $table->string('p', 5)->nullable();
                $table->string('k', 5)->nullable();
                $table->decimal('nitrogen', 10, 2)->nullable();
                $table->decimal('phosphorus', 10, 2)->nullable();
                $table->decimal('potassium', 10, 2)->nullable();
                $table->string('age', 200)->nullable();
                $table->string('variety', 200)->nullable();
                $table->string('cropping_season', 200)->nullable();
                $table->string('soil_type', 200)->nullable();
                $table->text('landscape')->nullable();
                $table->string('fertilizer_type')->nullable();
                $table->text('result')->nullable();
                $table->text('mode_of_application')->nullable();
                $table->string('is_7andBelow_ph')->nullable();
                $table->text('lime_application')->nullable();
                $table->string('tag')->nullable();
            });
        }
        else{
            Schema::dropIfExists($tableName);

            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('crop_type', 100)->nullable();
                $table->string('om', 5)->nullable();
                $table->string('p', 5)->nullable();
                $table->string('k', 5)->nullable();
                $table->decimal('nitrogen', 10, 2)->nullable();
                $table->decimal('phosphorus', 10, 2)->nullable();
                $table->decimal('potassium', 10, 2)->nullable();
                $table->string('age', 200)->nullable();
                $table->string('variety', 200)->nullable();
                $table->string('cropping_season', 200)->nullable();
                $table->string('soil_type', 200)->nullable();
                $table->text('landscape')->nullable();
                $table->string('fertilizer_type')->nullable();
                $table->text('result')->nullable();
                $table->text('mode_of_application')->nullable();
                $table->string('is_7andBelow_ph')->nullable();
                $table->text('lime_application')->nullable();
                $table->string('tag')->nullable();
            });
        }
        
        return $tableName;
    }

    public function insertJsonIntoDynamic($tableName, $data)
    {
        if (!Schema::hasTable($tableName)) {
            return $this->failed('', "Table {$tableName} does not exist.");
        }

        try {
            $recordExists = DB::table($tableName)
                ->where("crop_type", $data["crop_type"])
                ->where('om', $data['om'])
                ->where('p', $data['p'])
                ->where('k', $data['k'])
                ->where('nitrogen', $data['nitrogen'])
                ->where('phosphorus', $data['phosphorus'])
                ->where('potassium', $data['potassium'])
                ->where('age', $data['age'])
                ->where('variety', $data['variety'])
                ->where('cropping_season', $data['cropping_season'])
                ->where('soil_type', $data['soil_type'])
                ->where('landscape', $data['landscape'])
                ->where('fertilizer_type', $data['fertilizer_type'])
                ->where('is_7andBelow_ph', $data['is_7andBelow_ph'])
                ->where('tag', $data['tag'])
                ->exists();

            if( !$recordExists ) {
                DB::table($tableName)->insert($data);
                return $this->success('', "Data inserted successfully into {$tableName}.");
            }
            else{
                return $this->failed('', " Failed to insert data into {$tableName}. Duplicate record exists.");
            }
            //  return $this->success($recordExists, "Data inserted successfully into {$tableName}.");

        } catch (\Exception $e) {
            return $this->failed('', $e->getMessage());
        }
    }

}
