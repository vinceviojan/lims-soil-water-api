<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Services\MslTestResultService;
use App\Models\msl_test_result;
use Illuminate\Support\Facades\Cache;

class QualiInsertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //  public $queue = 'qualitative';
    
    public function __construct(public int $mslId)
    {
        //
    }

    /** 
     * Execute the job.
     */
    public function handle(MslTestResultService $service): void
    {
        // Log::info('Qualitative Update job started', ['id' => $this->mslId]);

        Cache::put('cancel_jobs', true);


        $msl = msl_test_result::find($this->mslId);

        if (!$msl) return;

        if (empty($msl->n_qual) ) {
            if(empty($msl->om)){
                Log::info('no om', ['id' => $this->mslId]);
                return;
            }
            else {
                $n_qual = $service->getInterpretation('om', $msl->om);
                Log::info('om value', ['id' => $this->mslId, 'n_qual' => $n_qual]);
                $msl->n_qual = $n_qual;
            }
             Log::info('n_qual', ['n_qual' => $msl->n_qual]);

        }

        if(empty($msl->p_qual)) {
           if (isset($msl->p_bray)) {
                $key = 'p_bray';
                $value = $msl->p_bray;
            } elseif (isset($msl->p_olsen)) {
                $key = 'p_olsen';
                $value = $msl->p_olsen;
            } else {
                return; 
            }

            $p_qual = $service->getInterpretation($key, $value);
            $msl->p_qual = $p_qual;
            Log::info('p_qual', ['_qual' => $msl->p_qual]);

        }

        if (empty($msl->k_qual)) {
            if (empty($msl->k)) return;
            else {
                $kValue = is_numeric($msl->k) ? ((float)$msl->k * 391) : null;
                $k_qual = $service->getInterpretation('k', $kValue);
                $msl->k_qual = $k_qual;
            }
            Log::info('k_qual', ['k_qual' => $msl->k_qual]);
        }

        $msl->save();
        // Log::info('Qualitative Update job completed', ['id' => $this->mslId]);
    }
}
