<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\crops;
use App\Models\msl_rst;
use App\Models\acid_loving_crop;

class FertRightResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function success($resp, $message)
    {
        return response()->json([
            'isSuccess' => true,
            'message' => $message,
            'data' => $resp
        ], 200);
    }

    public function failed($resp, $message)
    {
        return response()->json([
            'isSuccess' => false,
            'message' => $message,
            'data' => $resp
        ], 400);
    }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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

            if ($data["crop_type"] == "corn") {
            } else {
                $crop = crops::where('type', $data["crop_type"])->first();
            }

            if ($crop) {
                $data["crop_type"] = $crop["code"];
                if ($name != $data["crop_type"]) {
                    $name = $data["crop_type"];
                    $tableName = $this->createDynamicTable($data);
                }

                $insert = $this->insertJsonIntoDynamic($tableName, $data);
                $dataCnt++;
                $getData = $insert->getData();
                if ($getData->isSuccess == true) {
                    $successCnt++;
                } else {
                    $failCnt++;
                    $failedMessage[] = $getData->message . " Cnt #" . $dataCnt;
                }
            } else {
                $failCnt++;
                $failedMessage[] = "Crop type " . $data["crop_type"] . " does not exist. Cnt #" . $dataCnt;
            }
        }

        if ($successCnt > 0) {
            return $this->success(
                [
                    'successful_inserts' => $successCnt,
                    'failed_inserts' => $failCnt,
                    'failed_messages' => $failedMessage
                ],
                "Data inserted successfully."
            );
        } else {
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


    public function createDynamicTable($request): string
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
        } else {
            Schema::dropIfExists($tableName);

            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('crop_type', 100)->nullable();
                $table->string('om', 5)->nullable();
                $table->string('p', 5)->nullable();
                $table->string('k', 5)->nullable();
                $table->string('nitrogen')->nullable();
                $table->string('phosphorus')->nullable();
                $table->string('potassium')->nullable();
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

            if (!$recordExists) {
                DB::table($tableName)->insert($data);
                return $this->success('', "Data inserted successfully into {$tableName}.");
            } else {
                return $this->failed('', " Failed to insert data into {$tableName}. Duplicate record exists.");
            }
            //  return $this->success($recordExists, "Data inserted successfully into {$tableName}.");

        } catch (\Exception $e) {
            return $this->failed('', $e->getMessage());
        }
    }

    public function getVariety(Request $request)
    {
        $crop = crops::where('code', $request["crop"])->first();

        $tableName = strtolower($request["crop"] . '_fert_right');
        $recordExists = DB::table($tableName)
            ->select('variety')
            ->where('variety', '<>', 'N/A')
            ->distinct()
            ->get();

        if ($recordExists->isNotEmpty()) {
            return $this->success($recordExists, $crop["category"]);
        } else
            return $this->failed('', " no data.");
    }

    public function getLandscape(Request $request)
    {
        $landscapesArray = [];
        $crop = crops::where('code', $request["crop"])->first();

        $tableName = strtolower($request["crop"] . '_fert_right');
        $recordExists = DB::table($tableName)
            ->select('landscape')
            ->where('landscape', '<>', 'N/A')
            ->distinct()
            ->get();

        

        if( $recordExists->isNotEmpty() ) {
            $recordExists->each(function ($item) use (&$landscapesArray) {
                $clean = str_replace(' ', '', $item->landscape);
                $parts = explode(',', $clean);
                foreach ($parts as $part) {
                    $landscapesArray[] = ['landscape' => $part];
                }
            });
            return $this->success($landscapesArray, $crop["category"]);
        }
        else
            return $this->failed('', " no data.");
    }

    public function getAge(Request $request)
    {
        $landscapesArray = [];
        $crop = crops::where('code', $request["crop"])->first();

        $tableName = strtolower($request["crop"] . '_fert_right');
        $recordExists = DB::table($tableName)
            ->select('age')
            ->where('age', '<>', 'N/A')
            ->distinct()
            ->get();

        if ($recordExists->isNotEmpty()) {
            return $this->success($recordExists, $crop["category"]);
        } else
            return $this->failed('', " no data.");
    }
    public function getFertRightResult(Request $request)
    {
        $value = $request->all();
        $tableName = strtolower($value["crop"] . '_fert_right');
        $msl = msl_rst::where('id', $value['id'])->first();
        $crop = crops::where('type', '=', $value['crop'])
            ->orWhere("code",'=',$value['crop'])
            ->first();
        
        $result = [];
        $cropsList = ["apple", "pepper"];
        $acidLovingCrops = null;
        $isSevenBelow = $msl->ph <= 7 ? "YES" : "NO";

        $clean = preg_replace('/\s*\(.*?\)/', '', $crop->type);
        $clean = str_replace(' ', '_', $clean);
        if(!in_array(strtolower($clean), $cropsList)){
            $acidLovingCrops = acid_loving_crop::where('crops', 'LIKE', '%' . $clean . '%')
                ->orWhere('crops',  $crop->code)
                ->first();
        }


        $query = DB::table($tableName)->select('*');

        if(isset($value['variety']) && !empty($value['variety'])){
            $query->where('variety', $value['variety']);
        }

        if(isset($value['landscape']) && !empty($value['landscape'])){
            $query->where('landscape', 'like', '%' . $value['landscape'] . '%');
        }

        if(isset($value['age']) && !empty($value['age'])){
            $query->where('age', $value['age']);
        }

        $recordExists = $query
            ->where('om',  strtoupper(substr($msl->n, 0, 1)))
            ->where('p', strtoupper(substr($msl->p, 0, 1)))
            ->where('k', strtoupper(substr($msl->k, 0, 1)))
            // ->where('is_7andBelow_ph', $isSevenBelow)
            ->get();

        if( $recordExists->isNotEmpty() ) {
            $recordExists = $recordExists->toArray(); 
            foreach($recordExists as &$record){
                if(isset($record->result) && !empty($record->result) && $record->result != "N/A"){
                    $result [] = ["result" => $record->result];
                }
            }
            
            $fertilizer_rate = "";
            if(floatval($recordExists[0]->nitrogen) > 1 && floatval($recordExists[0]->phosphorus) > 1 && floatval($recordExists[0]->potassium) > 1){
                $fertilizer_rate = intval($recordExists[0]->nitrogen)  . " - " . intval($recordExists[0]->phosphorus) . " - " . intval($recordExists[0]->potassium);
            }
            else{
                $fertilizer_rate = $recordExists[0]->nitrogen  . " - " . $recordExists[0]->phosphorus . " - " . $recordExists[0]->potassium;
            }

            
            $data = [
                'crop' => strtoupper($crop->type),
                'ph' => $msl->ph,
                'nitro' => $msl->n,
                'phosphor' => $msl->p,
                'potass' => $msl->k,
                'fertilizer_rate' => $fertilizer_rate,
                'mode_of_application' => $recordExists[0]->mode_of_application,
                'results' => $result,
            ];

            if(isset($value['variety']) && !empty($value['variety'])){
                $data['variety'] = $value['variety'];
            }

            if(isset($value['landscape']) && !empty($value['landscape'])){
                $data['landscape'] = $value['landscape'];
            }

            if(isset($value['age']) && !empty($value['age'])){
                $data['age'] = $value['age'];
            }
            
            if($acidLovingCrops){
                $data['acid_loving_crops_title'] = $acidLovingCrops->category;
                $text = "Preferred soil pH between " . $acidLovingCrops->min_ph . " and " . $acidLovingCrops->max_ph . ". ";
                if($acidLovingCrops->category_code === "ALC"){
                    $text .= "Apply organic materials or biofertilizers instead of lime. ";
                }
                else{
                    if(floatval($msl->ph) <= 5){
                        $text .= "Apply 1 tons of lime yearly until soil pH reaches 5.5 to 6.5. Apply lime and mix to the soil at least 1 month before planting. ";
                    }
                    if(floatval($msl->ph) >= 5.1 && floatval($msl->ph) <= 5.5){
                        $text .= "Increase the amount of organic materials to 1-2 tons/ha. Organic materials enhance soil chemical, physical, and biological properties, thus, enhancing soil health. ";
                    }
                    if(floatval($msl->ph) <= 7){
                        $text .= "Use Urea (46-0-0) as a source of N. ";
                    }
                    if(floatval($msl->ph) > 7){
                        $text .= "Use Ammonium sulfate (21-0-0). 1 bag of Urea is approximately equivalent to 2 bags of Ammonium Sulfate. ";
                    }
                    $text .= "Do not mix lime with organic or inorganic fertilizers. ";
                }
                $data['acid_loving_crops_text'] = $text;
            }
            return $this->success($data, "Data retrieved successfully.");
        }
        else{
            return $this->failed('', " no data.");
        }
    }
    
}
