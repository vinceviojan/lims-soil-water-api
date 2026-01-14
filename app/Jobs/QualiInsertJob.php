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


class QualiInsertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $mslId)
    {
        //
    }

    /** 
     * Execute the job.
     */
    public function handle(MslTestResultService $service): void
    {
        Log::info('Qualitative Update job started', ['id' => $this->mslId]);
        $msl = msl_test_result::find($this->mslId);

        if (!$msl) return;

        if (empty($msl->n_qual) ) {
            if(empty($msl->om)) return;
            else {
                $n_qual = $service->getInterpretation('om', $msl->om);
                $msl->n_qual = $n_qual;
            }
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

        }

        if (empty($msl->k_qual)) {
            if (empty($msl->k)) return;
            else {
                $k_qual = $service->getInterpretation('k', $msl->k);
                $msl->k_qual = $k_qual;
            }
        }

        $msl->save();
        Log::info('Qualitative Update job completed', ['id' => $this->mslId]);
    }
}
