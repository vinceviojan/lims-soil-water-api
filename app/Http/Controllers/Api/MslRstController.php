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

            $bara = $getBara->barangay_name; 
            $msl->where('barangay', 'LIKE', "%{$bara}%");
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
                if(!empty($row->ph) && !empty($row->om) && (!empty($row->p_bray) || !empty($row->p_olsen)) && !empty($row->k)) {
                    if (empty($row->latitude) || empty($row->longitude)) {
                        GeocodeMslAddressJob::dispatch($row->id);
                    }

                    $kValue = is_numeric($row->k) ? ((float)$row->k * 391) : "";
                    $pValue = is_numeric($row->p_bray) ? (float)$row->p_bray
                        : (is_numeric($row->p_olsen) ? (float)$row->p_olsen : "");
                    $pSymbol = is_numeric($row->p_bray) ? 'p_bray'
                        : (is_numeric($row->p_olsen) ? 'p_olsen' : '');

                    QualiInsertJob::dispatch($row->id);

                    $data[] = [
                        'id' => $row->id,
                        'latitude' => $row->latitude,
                        'longitude' => $row->longitude,
                        'farm_area' => $row->farm_area, 
                        'ph' => $row->ph,
                        'n' => $service->getInterpretation('om', (float)$row->om),
                        'p' => $service->getInterpretation($pSymbol, $pValue),
                        'k' => $service->getInterpretation('k', $kValue),
                        'n_value' => $row->om,
                        'p_value' => empty($row->p_bray) ? $row->p_olsen
                            : $row->p_bray,
                        'k_value' => $kValue,
                        'barangay' => $row->barangay,
                        'municipality' => $row->municipality,
                        'province' => $row->province,
                    ];
                }
            }
        });

        if (empty($data)) {
            return $this->failed(null, 'No record found');
        }

        return $this->success($data, 'Retrieved successfully');


        // $msl = msl_test_result::where('status', 1);

        // if(isset($request["provi"]) && !empty($request["provi"])){
        //     $getPro = DB::table("table_province")
        //         ->select("province_name")
        //         ->where("province_id", $request["provi"])
        //         ->first();

        //     $provi = $getPro->province_name;
        //     $msl->where('province', 'LIKE', "%{$provi}%");
        // }

        // if(isset($request["muni"]) && !empty($request["muni"])){
        //     $getMuni = DB::table("table_municipality")  
        //         ->select("municipality_name")
        //         ->where("municipality_id", $request["muni"])
        //         ->first();

        //     $muni = "";

        //     if($getMuni->municipality_name == "City of Tarlac (Capital)"){
        //         $muni = "Tarlac City";
        //     }
        //     else{   
        //         $muni = $getMuni->municipality_name; 
        //     }
            
        //     $msl->where('municipality', 'LIKE', "%{$muni}%");
        // }

        // if(isset($request["bara"]) && !empty($request["bara"])){
        //     $getBara = DB::table("table_barangay")  
        //         ->select("barangay_name")
        //         ->where("barangay_id", $request["bara"])
        //         ->first();

        //     $bara = $getBara->barangay_name; 
        //     $msl->where('barangay', 'LIKE', "%{$bara}%");
        // }
        

        // $msl = $msl->get();
        
        // $data = [];

        // foreach($msl as $key => $value){
        //     $om_interpretation = $this->getInterpretation('om', $value->om);
        //     $p_interpretation = empty($value->p_bray) ? $this->getInterpretation('p_olsen', $value->p_olsen) : $this->getInterpretation('p_bray', $value->p_bray);
            
        //     $value_k = (int)$value->k * 391;
            
        //     $k_interpretation = $this->getInterpretation('k', $value_k);

        //     $address = $value->barangay . ', ' . $value->municipality . ', ' . $value->province;

        //     $cacheKey = 'osm_geocode_' . md5($address);

        //     $result = Cache::remember($cacheKey, 86400, function () use ($address) {

        //         $response = Http::withHeaders([
        //                 // REQUIRED by Nominatim policy
        //                 'User-Agent' => 'YourLaravelApp/1.0 (your@email.com)',
        //             ])
        //             ->get('https://nominatim.openstreetmap.org/search', [
        //                 'q' => $address,
        //                 'format' => 'json',
        //                 'limit' => 1,
        //             ]);

        //         if (!$response->successful()) {
        //             return null;
        //         }

        //         return $response->json();
        //     });

        //     if (!empty($result)) {

        //         $data[] = [
        //             'id' => $value->id,
        //             'longitude' => $result[0]['lon'],
        //             'latitude' => $result[0]['lat'],
        //             'farm_area' => $value->farm_area,
        //             'ph' => $value->ph,
        //             'n' => $om_interpretation,
        //             'p' => $p_interpretation,
        //             'k' => $k_interpretation,
        //             'n_value' => $value->om,
        //             'p_value' => empty($value->p_bray) ? $value->p_olsen : $value->p_bray,
        //             'k_value' => $value->k,
        //             'shc_number' => $value->shc_number,
        //             'soil_texture' => $value->soil_texture,
        //             'soil_ph_interpretation' => $value->soil_ph_interpretation,
        //             'year_of_sampling' => $value->year_of_sampling,
        //             'barangay' => $value->barangay,
        //             'municipality' => $value->municipality,
        //             'province' => $value->province,
        //         ];
        //     }
            
        // }

        // if ($data) {
        //     return $this->failed("", "No record found");
        // }

        // return $this->success($msl, "Retrieved successfully");

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
