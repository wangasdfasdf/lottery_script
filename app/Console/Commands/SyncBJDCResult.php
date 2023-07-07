<?php

namespace App\Console\Commands;

use App\Models\BjdcResult;
use App\Models\MatchResult;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncBJDCResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:bjdc:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const SALT = 'debacb729609e9fa8b6ebdbf2ac7425511ea5e2e';

    const url = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('sync：start', ['==================================']);
        $url = \config('project.sync_url') . '/bjdc';

        BjdcResult::query()->where('sync', 0)->chunkById(50, function ($matchResults) use ($url) {

            $this->sync($url, $matchResults);

        });
        Log::info('sync：end', ['==================================']);
    }

    public function sync(string $url, Collection $matchResults)
    {
        $ids  = $matchResults->pluck('id');
        $data = $matchResults->makeHidden(['id', 'created_at', 'updated_at', 'deleted_at', 'sync'])->toArray();

        $time  = \time();
        $token = \md5(self::SALT . $time);

        $result = Http::connectTimeout(5)->timeout(3)->withHeaders([
            'time'  => $time,
            'token' => $token,
        ])->post($url, $data);


        $result = \json_decode($result->body(), true);

        if ($result['code'] ?? 0 == 200) {
            BjdcResult::query()->whereIn('id', $ids)->update(['sync' => 1]);
        }

        Log::info('debug', [
            'match'  => $ids,
            'result' => $result,
            'url'    => $url,
        ]);
    }
}
