<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\msl_rst;
use App\Models\crops;
use App\Models\crops_fert_right;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Models\msl_test_result;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\MslTestResultService;
use App\Jobs\GeocodeMslAddressJob;
use Illuminate\Support\Facades\Log;
use App\Jobs\QualiInsertJob;

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

        if(isset($request["province"]) && !empty($request["province"])){
            $getPro = DB::table("table_province")
                ->select("province_name")
                ->where("province_id", $request["provi"])
                ->get();

            $provi = $getPro->province_name;
            $msl->where('province', 'LIKE', "%{$provi}%");
        }

        if(isset($request["muni"]) && !empty($request["muni"])){
            $getMuni = DB::table("table_municipality")  
                ->select("municipality_name")
                ->where("municipality_id", $request["muni"])
                ->first();

            $muni = "";

            if($getMuni->municipality_name == "City of Tarlac (Capital)"){
                $muni = "Tarlac City";
            }
            else if($getMuni->municipality_name == "Balibago I" || $getMuni->municipality_name == "Balibago II"){
                $muni = "Balibago";
            }
            else if($getMuni->municipality_name == "Nilasin 1st" || $getMuni->municipality_name == "Nilasin 2nd"){
                $muni = "Nilasin";
            }
            else{   
                $muni = $getMuni->municipality_name; 
            }
            
            $msl->where('municipality', 'LIKE', "%{$muni}%");
        }
        
        if(isset($request["bara"]) && !empty($request["bara"])){
            $getBara = DB::table("table_barangay")  
                ->select("barangay_name")
                ->where("barangay_id", $request["bara"])
                ->first();
            
            $bara ="";

            if($getBara->barangay_name == "Luna (Pob.)"){
                $bara = "Luna";
            }
            else if($getBara->barangay_name == "La Paz (Pob.)"){
                $bara = "La Paz";
            }
            else if($getBara->barangay_name == "San Roque (Pob.)"){
                $bara = "San Roque";
            }
            else if($getBara->barangay_name == "Tampo (Pob.)"){
                $bara = "Tampo";
            }
            else{
                $bara = $getBara->barangay_name; 
            }

            $msl->where('barangay', 'LIKE', "%{$bara}%");
        }
        $data [] = [
            'province' => $provi,
            'municipality' => $muni,
            'barangay' => $bara,
        ];

        $msl = $msl->get();

        if ($msl->isEmpty()) {
            return $this->failed($data, "No record found");
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
        $msl = DB::table("table_province")
            ->select('province_id', DB::raw('province_name AS province'))
            ->where("status", "1")
            ->orderBy('province_name', 'ASC')
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
        $msl = DB::table("table_municipality")
            ->select('municipality_id', DB::raw('municipality_name AS municipality'))
            ->where("province_id", $value["province"])
            ->orderBy('municipality_name', 'ASC')
            ->get();

        if($msl->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($msl, "Retrieved successfully");
        }
    }

    public function getBaranggay(Request $request){
        $value = $request->all();
         $msl = DB::table("table_barangay")
            ->select('barangay_id', DB::raw('barangay_name AS baranggay'))
            ->where("municipality_id", $value["muni"])
            ->orderBy('barangay_name', 'ASC')
            ->get();

        if($msl->isEmpty()){
            return $this->failed("", "No record found");
        }
        else{
            return $this->success($msl, "Retrieved successfully");
        }
    }

    public function mslTestResult(Request $request, MslTestResultService $service)
    {
        $query = $service->fetchResults($request);
        $data = [];

        $query->chunk(200, function ($rows) use (&$data, $service) {
            foreach ($rows as $row) {
                // Dispatch geocoding only if missing
                if (empty($row->latitude) || empty($row->longitude)) {
                    GeocodeMslAddressJob::dispatch($row->id)->onQueue('geocode');
                    // GeocodeMslAddressJob::dispatch($row->id);
                }

                $kValue = is_numeric($row->k) ? ((float)$row->k * 391) : "";
                $pValue = is_numeric($row->p_bray) ? (float)$row->p_bray
                    : (is_numeric($row->p_olsen) ? (float)$row->p_olsen : "");
                $pSymbol = is_numeric($row->p_bray) ? 'p_bray'
                    : (is_numeric($row->p_olsen) ? 'p_olsen' : '');

                QualiInsertJob::dispatch($row->id)->onQueue('qualitative');
                // QualiInsertJob::dispatch($row->id);

                $data[] = [
                    'id' => $row->id,
                    'latitude' => $row->latitude,
                    'longitude' => $row->longitude,
                    'farm_area' => $row->farm_area, 
                    'ph' => $row->ph,
                    'n' => $row->om ? $service->getInterpretation('om', (float)$row->om) : ($row->n_qual ? $row->n_qual : ''),
                    'p' => $pValue ? $service->getInterpretation($pSymbol, $pValue) : ($row->p_qual ? $row->p_qual : ''),
                    'k' => $kValue ? $service->getInterpretation('k', $kValue) : ($row->k_qual ? $row->k_qual : ''),
                    'n_value' => $row->om,
                    'p_value' => empty($row->p_bray) ? $row->p_olsen
                        : $row->p_bray,
                    'k_value' => $kValue,
                    'barangay' => $row->barangay,
                    'municipality' => $row->municipality,
                    'province' => $row->province,
                ];
            }
        });

        if (empty($data)) {
            return $this->failed(null, 'No record found');
        }

        return $this->success($data, 'Retrieved successfully');

    }


    public function getInterpretation($symbol, $value)
    {
        try {
            $interpretation = DB::table('soil_interpretation')
                ->where('symbol', 'om')
                ->where('min', '<=', $value)
                ->where(function ($q) use ($value) {
                    $q->where('max', '>=', $value)
                    ->orWhereNull('max');
                })
                ->value('interpretation');

            return $interpretation ?? '';
        } catch (\Throwable $th) {
            //throw $th;
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
