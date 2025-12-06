<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\msl_rst;
use App\Models\crops;
use App\Models\crops_fert_right;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\DB;

class MslRstController extends Controller
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

    public function index(Request $request)
    {
        $msl = msl_rst::whereNotNull('latitude')
            ->whereNotNull('longitude');

        // if (isset($request["filter"]) && !empty($request["filter"])) {
        //     $filter = $request["filter"]; // define filter

        //     $msl->where(function ($query) use ($filter) {
        //         $query->where('barangay', 'LIKE', "%{$filter}%")
        //             ->orWhere('municipality', 'LIKE', "%{$filter}%")
        //             ->orWhere('province', 'LIKE', "%{$filter}%");
        //     });
        // }
        if(isset($request["muni"]) && !empty($request["muni"])){
            $provi = $request["provi"]; 
            $msl->where('province', 'LIKE', "%{$provi}%");
        }

        if(isset($request["muni"]) && !empty($request["muni"])){
            $muni = $request["muni"]; 
            $msl->where('municipality', 'LIKE', "%{$muni}%");
        }
        
        $msl = $msl->get();

        if ($msl->isEmpty()) {
            return $this->failed("", "No record found");
        }

        return $this->success($msl, "Retrieved successfully");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       

        $validated = $request->validate([
            '*.farm_area' => 'nullable|numeric',
            '*.longitude' => 'nullable|string|max:100',
            '*.latitude' => 'nullable|string|max:200',
            '*.soil_texture' => 'nullable|string',
            '*.ph' => 'nullable|numeric',
            '*.soil_ph_interpretation' => 'nullable|string',
            '*.n' => 'required|in:Low,Medium,High',
            '*.p' => 'required|in:Low,Medium,High',
            '*.k' => 'required|in:Low,Medium,High',
            '*.shc_number' => 'required|string|max:100',
            '*.sampling_date' => 'nullable|string|max:100',
            '*.municipality' => 'required|string|max:100',
            '*.barangay' => 'nullable|string',
            '*.province' => 'required|string',
            '*.crops' => 'nullable|string',
        ]);



        foreach ($validated as &$record) {
            $date = DateTime::createFromFormat('M, d, Y', $record['sampling_date']);
            $record['sampling_date'] = isset($record['sampling_date']) ? $date->format('Y-m-d   '): null;
        }

        try{
             
            $result = DB::transaction(function () use ($validated) {
                $cnt = 0;
                foreach ($validated as &$record) {
                    if(isset($record['crops'])){
                        $crops = explode(',', $record['crops']); 
                        foreach ($crops as $key => $value) {
                            $cropsRecord = DB::table('crops_fert_right')
                                ->join('crops','crops_fert_right.crop_type','=','crops.id')
                                ->select('crops_fert_right.*', 'crops.type')
                                ->where('crops.type','=', $value)
                                ->where('crops_fert_right.shc_number', $record['shc_number'])
                                ->get();

                            $crop = crops::where('type', '=', $value)
                                ->orWhere("code",'=',$value)
                                ->first();
                            if($cropsRecord->isEmpty()) {
                                
                                
                                crops_fert_right::create([
                                    'shc_number' => $record['shc_number'],
                                    'crop_type' => $crop->id,
                                ]);
                            }

                        }
                        
                    }

                    unset($record['crops']); 
                    
                    $dups = msl_rst::where('shc_number', '=', $record['shc_number'])
                        ->exists();
                        
                    if (!$dups) {
                        msl_rst::insert($record);
                        $cnt++;
                    }
                }
                return $this->success($cnt, "MSL RST records added successfully");
                
            });
            return $result; 
        }catch(\Exception $e){
            return $this->failed('', $e->getMessage());
        }

    }

    public function getYearList()
    {
        $msl = msl_rst::select(DB::raw('DISTINCT YEAR(sampling_date) as year'))
                ->whereNotNull('sampling_date')
                ->orderBy('year', 'desc')
                ->get();
            
        if($msl->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($msl, "Retrieved successfully");
        }
    }

    public function getProvince(){
        $msl = msl_rst::select('province')
            ->distinct()
            ->get();

        if($msl->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($msl, "Retrieved successfully");
        }
    }

    public function getMunicipality(Request $request){
        $value = $request->all();
        $msl = msl_rst::select('municipality')
            ->distinct()
            ->where('province', $value["province"])
            ->get();

            if($msl->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($msl, "Retrieved successfully");
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
