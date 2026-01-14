<?php

namespace App\Jobs;

use App\Models\msl_test_result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeocodeMslAddressJob implements ShouldQueue
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
    public function handle(): void
    {
        //Log::info('Geocode job started', ['id' => $this->mslId]);

        $msl = msl_test_result::find($this->mslId);
        if (!$msl) return;

        if (!$msl->municipality || !$msl->province) {
            Log::error('Incomplete address', ['id' => $this->mslId]);
            return;
        }

        $address = "{$msl->municipality}, {$msl->province}, Philippines";

        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => 'YourLaravelApp/1.0 (your@email.com)',
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

        if (!$response->successful()) {
            //Log::error('OSM request failed', ['status' => $response->status()]);
            return;
        }

        $data = $response->json();

        if (empty($data) || !isset($data[0]['lat'])) {
            //Log::warning('OSM returned empty', ['address' => $address]);
            return;
        }

        $msl->latitude = $data[0]['lat'];
        $msl->longitude = $data[0]['lon'];
        $msl->save();

        // Log::info('Geocode saved', [
        //     'lat' => $data[0]['lat'],
        //     'lon' => $data[0]['lon'],
        // ]);
    }
}
