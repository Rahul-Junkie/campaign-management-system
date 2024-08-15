<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignUser;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendCampaignEmailJob;
use Illuminate\Support\Facades\Log;

class ProcessCampaignUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = CampaignUser::with('campaign')->where('campaign_id', $this->campaign->id)->get();
        $this->campaign->update(['total_contacts' => count($users)]);
        $chunkSize = 100;
        $users->chunk($chunkSize)->each(function ($chunkedUsers) {
            // Dispatch a job for each chunk
            $userIds = $chunkedUsers->pluck('id');
            // Update the status of all users in the chunk in a single query
            CampaignUser::whereIn('id', $userIds)->update(['email_sent_status' => 'Added to queue']);
            SendCampaignEmailJob::dispatch($chunkedUsers);
        });
    }
}
