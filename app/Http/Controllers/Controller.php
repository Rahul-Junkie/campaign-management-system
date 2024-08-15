<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignEmailJob;
use App\Models\Campaign;
use App\Models\CampaignUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;



use App\Mail\CampaignEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;



class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function processCampaign($campaignId)
{
    $campaign = Campaign::findOrFail($campaignId);
    $batchSize = 50; // Process in chunks of 50

    // Fetch users in chunks
    CampaignUser::where('campaign_id', $campaignId)
        ->chunk($batchSize, function ($users) use ($campaign) {
            $jobs = [];

            foreach ($users as $user) {
                $jobs[] = new SendCampaignEmailJob($user->name, $user->email, $campaign);
            }

            Bus::batch($jobs)->dispatch();
        });

    return back()->with('success', 'Campaign processing has started!');
}
}
