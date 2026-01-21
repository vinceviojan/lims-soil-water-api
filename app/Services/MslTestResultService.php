<?php

namespace App\Services;

use App\Models\msl_test_result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MslTestResultService
{
    protected $interpretations;

    public function __construct()
    {
        // Load interpretation rules ONCE
        $this->interpretations = DB::table('msl_interpretations')
            ->orderBy('min')
            ->get()
            ->groupBy('symbol');
    }

    public function getInterpretation($symbol, $value)
    {
        try {
            if (!is_numeric($value)) {
                return null;
            }

            foreach ($this->interpretations[$symbol] ?? [] as $row) {
                $value = (float) $value;
                $min   = (float) $row->min;
                $max   = ($row->max === null || $row->max === '') ? null : (float) $row->max;

                if ($min <= $value && ($max === null || $max >= $value)) {
                    return $row->interpretation;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in getInterpretation', ['symbol' => $symbol, 'value' => $value, 'error' => $e->getMessage()]);
            return null;

        }

    }

    public function fetchResults($request)
    {
        $query = msl_test_result::where('status', 1);

        if (!empty($request->provi)) {
            $province = DB::table('table_province')
                ->where('province_id', $request->provi)
                ->value('province_name');

            if ($province) {
                $query->where('province', 'LIKE', "%{$province}%");
            }
        }

        if (!empty($request->muni)) {
            $muni = DB::table('table_municipality')
                ->where('municipality_id', $request->muni)
                ->value('municipality_name');

            Log::info('Geocode job started', ['muni' => $muni]);

            if (str_contains('City of Tarlac', $muni) or $muni ==='City of Tarlac') {
                $muni = 'Tarlac City';
            }
            if (str_contains($muni, 'Baler' )) {
                $muni = 'Baler';
                
            }
            
            if($muni === 'City of Valencia'){
                $muni = 'Valencia City';
            }
            if(str_contains('City of Palayan', $muni) or $muni ==='City of Palayan'){
                $muni = 'Palayan City';
            }
            if(str_contains('City of Balanga', $muni) or $muni ==='City of Balanga'){
                $muni = 'Palayan City';
            }

            if($muni === 'City of Cabanatuan'){
                $muni = 'Cabanatuan City';
            }

            if($muni === 'City of Gapan'){
                $muni = 'Gapan';
            }

            if(str_contains('Iba', $muni)){
                $muni = 'Iba';
            }

            if ($muni) {
                $query->where('municipality', 'LIKE', "%{$muni}%");
            }
        }

        if (!empty($request->bara)) {
            $barangay = DB::table('table_barangay')
                ->where('barangay_id', $request->bara)
                ->value('barangay_name');
            $bara ="";

            if(str_contains('Luna', $barangay) or $barangay == 'Luna (Pob.)'){
                $bara = "Luna";
            }
            else if(str_contains('La Paz', $barangay) or $barangay == "La Paz (Pob.)"){
                $bara = "La Paz";
            }
            else if(str_contains('San Roque', $barangay) or $barangay == "San Roque (Pob.)"){
                $bara = "San Roque";
            }
            else if(str_contains('Tampo', $barangay) or $barangay == "Tampo (Pob.)"){
                $bara = "Tampo";
            }
            else{
                $bara = $barangay; 
            }

            if ($barangay) {
                $query->where('barangay', 'LIKE', "%{$bara}%");
            }
        }

        return $query;
    }
}
