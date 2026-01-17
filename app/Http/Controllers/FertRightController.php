<?php

namespace App\Http\Controllers;

use App\Models\crops;
use App\Models\msl_rst;
use App\Models\acid_loving_crop;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\msl_test_result;
use App\Services\MslTestResultService;

class FertRightController extends Controller
{
    public function generate(Request $request, MslTestResultService $service)
    {
        $value = $request->all();
        $tableName = strtolower($value["crop"] . '_fert_right');
        // $msl = msl_rst::where('id', $value['id'])->first();
        $msl = msl_test_result::where('id', $value['id'])
            ->whereNotNull('ph')
            ->where(function ($q) {
                    $q->whereNotNull('om')
                    ->orWhereNotNull('n_qual');
                })
            ->where(function ($q) {
                    $q->whereNotNull('p_bray')
                    ->orWhereNotNull('p_olsen')
                    ->orWhereNotNull('p_qual');
                })
            ->where(function ($q) {
                    $q->whereNotNull('k_qual')
                    ->orWhereNotNull('k');
                })
            ->first();

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
            $query->where('variety', operator: $value['variety']);
        }

        if(isset($value['landscape']) && !empty($value['landscape'])){
            $query->where('landscape', 'like', '%' . $value['landscape'] . '%');
        }

        if(isset($value['age']) && !empty($value['age'])){
            $query->where('age', $value['age']);
        }

        if(isset($value["soil_type"]) && !empty($value["soil_type"])){
            $query->where('soil_type', $value['soil_type']);
        }

        if(isset($value["crop_season"]) && !empty($value["crop_season"])){
            $query->where('cropping_season', $value['crop_season']);
        }

        $omValue = is_numeric($msl->om) ? (float)$msl->om : "";
        $kValue = is_numeric($msl->k) ? ((float)$msl->k * 391) : "";
        $pValue = is_numeric($msl->p_bray) ? (float)$msl->p_bray
            : (is_numeric($msl->p_olsen) ? (float)$msl->p_olsen : "");
        $pSymbol = is_numeric($msl->p_bray) ? 'p_bray'
            : (is_numeric($msl->p_olsen) ? 'p_olsen' : '');

        $n = $omValue ? $service->getInterpretation('om', (float)$omValue) : ($msl->n_qual ? $msl->n_qual : '');
        $p = $pValue ?  $service->getInterpretation($pSymbol, $pValue) : ($msl->p_qual ? $msl->p_qual : '');
        $k = $kValue ? $service->getInterpretation('k', $kValue) : ($msl->k_qual ? $msl->k_qual : '');

        $recordExists = $query
            ->where('om',  strtoupper(substr($n, 0, 1)))
            ->where('p', strtoupper(substr($p, 0, 1)))
            ->where('k', strtoupper(substr($k, 0, 1)))
            ->where('is_7andBelow_ph', $isSevenBelow)
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
                'location' => (isset($msl->barangay) && !empty($msl->barangay) ? $msl->barangay : "") . ", " . $msl->municipality . ", " . $msl->province ,
                'recordExists' => count($recordExists)
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
            
            if(isset($value['soil_type']) && !empty($value['soil_type'])){
                $data['soil_type'] = $value['soil_type'];
            }

            if(isset($value['crop_season']) && !empty($value['crop_season'])){
                $data['crop_season'] = $value['crop_season'];
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

            // if($value["crop"] == "rice"){
            //     $pdf = PDF::loadView("fert-rice", $data);
            //     return $pdf->stream();
            // }
            // else{
            //     $pdf = PDF::loadView("fert", $data);
            //     return $pdf->stream();
            // }
            $pdf = PDF::loadView("fert", $data);
                return $pdf->stream();
        }
        else{
            return view("no_fert");
        }

       
        // dd($data);
        
        
    }
}
