<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoilImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\msl_test_result;
use App\Services\MslTestResultService;

class ExcelUploadController extends Controller
{
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

    public function upload(Request $request, MslTestResultService $service)
    {
         $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'name' => 'required|string|max:255',
        ]);
        
        $import = new SoilImport;
        Excel::import($import, $request->file('file'));

        $data = $import->rows;
        $data = array_values(array_filter($data, function ($row) {
    // keep row only if at least ONE of these fields has a value
            return !empty($row['year_of_sampling'])
                || !empty($row['ph'])
                || !empty($row['om'])
                || !empty($row['p_bray'])
                || !empty($row['p_olsen'])
                || !empty($row['k'])
                || !empty($row['barangay'])
                || !empty($row['municipality'])
                || !empty($row['province']);
        }));
        
        if (empty($data)) {
            return $this->failed(null, 'Excel file contains no data.');
        }

        $validator = Validator::make($data, [
            '*.longitude' => 'nullable|max:100',
            '*.latitude' => 'nullable|max:200',
            '*.farm_area' => 'nullable|numeric',
            '*.ph' => 'nullable|numeric',
            '*.om' => 'nullable|numeric',
            '*.p_bray' => 'nullable|numeric',
            '*.p_olsen' => 'nullable|numeric',
            '*.k' => 'nullable|numeric',
            '*.shc_number' => 'nullable|string|max:100',
            '*.soil_texture' => 'nullable|string',
            '*.soil_ph_interpretation' => 'nullable|string',
            '*.year_of_sampling' => 'nullable|integer',
            '*.barangay' => 'nullable|string',
            '*.municipality' => 'nullable|string|max:100',
            '*.province' => 'nullable|string',
            '*.n_qual' => 'nullable|string',
            '*.p_qual' => 'nullable|string',
            '*.k_qual' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors(), 'Failed to upload Excel file: ' );
        }


        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            foreach ($validated as $data) {
                $value = ""    ;
                $kValue = is_numeric($data['k']) ? ((float)$data['k'] * 391) : null;

                if(empty($data['om'])){
                    if(empty($data['n_qual'])){
                        $data['n_qual'] = null;
                    }
                    else{
                        $data['n_qual'] = trim($data['n_qual']);
                    }
                }
                else {
                    $n_qual = $service->getInterpretation('om', $data['om']);
                    $data['n_qual'] = $n_qual;
                }

                if (isset($data['p_bray'])) {
                    $key = 'p_bray';
                    $value = $data['p_bray'];
                } elseif (isset($data['p_olsen'])) {
                    $key = 'p_olsen';
                    $value = $data['p_olsen'];
                } 

                if(empty($value)){
                    if(empty($data['p_qual'])){
                        $data['p_qual'] = null;
                    }
                    else{
                        $data['p_qual'] = trim($data['p_qual']);
                    }
                }
                else{
                    $p_qual = $service->getInterpretation($key, $value);
                    $data['p_qual'] = $p_qual;
                }

                if (empty($msl->k)) {
                    if(empty($data['k_qual'])){
                        $data['k_qual'] = null;
                    }
                    else{
                        $data['k_qual'] = trim($data['k_qual']);
                    }
                }
                else {
                    $k_qual = $service->getInterpretation('k', $kValue);
                    $data['k_qual'] = $k_qual;
                }

                $data['batch_code'] = $request->name;
                $pro = DB::table('table_province')
                    ->where('province_name', 'LIKE',  '%'.$data['province'].'%')
                    ->first();

                if($pro){
                    $pid = $pro->province_id;

                    $muni = DB::table('table_municipality')
                        ->where('municipality_name', 'LIKE',  '%'.$data['municipality'].'%')
                        ->where('province_id', $pid)
                        ->first();
                    
                    if($muni){
                        $mid = $muni->municipality_id;

                        $bara = DB::table('table_barangay')
                            ->where('barangay_name', 'LIKE',  '%'.$data['barangay'].'%')
                            ->where('municipality_id', $mid)
                            ->first();
                        if($bara){}
                        else{
                            $data['status'] = 0;
                        }
                    }
                    else{
                        $data['status'] = 0;
                    }
                }
                else{
                    $data['status'] = 0;
                }

                msl_test_result::insert($data);
            }
            DB::commit();
            return $this->success(null, 'Excel file uploaded and data saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failed(null, 'Failed to upload Excel file: ' . $e->getMessage());
        }

    }
}
