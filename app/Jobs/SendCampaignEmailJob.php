<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Notifications\CampaignProcessed;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function handle()
    {
        $campaignId = $this->users->first()->campaign_id; // Assuming all users in the batch belong to the same campaign
        $campaign = Campaign::with('user')->find($campaignId);
        foreach ($this->users as $user) {
            try {
                Mail::to($user->email)->send(new CampaignEmail($user->name, $user->campaign->email_body, 'Email Campaign'));
                $user->update(['email_sent_status' => 'Sent']);
                $campaign->increment('processed_contacts');
            } catch (\Throwable $th) {
                $user->update(['email_sent_status' => 'Failed']);
            }
        }
        // Check if all users' email sent statuses are not 'Pending'
        $pendingUsersCount = CampaignUser::where('campaign_id', $campaignId)
            ->where('email_sent_status', 'Pending')
            ->count();

        if ($pendingUsersCount === 0 && $campaign) {
            $campaign->update(['status' => 'Processed']);
            $campaignOwner = $campaign->user; // Assuming the campaign has an 'owner' relationship
            $htmlContent = '<html>
            <body>
                <h1>Campaign Processed Successfully</h1>
                <p>The campaign "' . $campaign->name . '" has been successfully processed.</p>
                <p>Total contacts processed: ' . $campaign->processed_contacts . '</p>
                <p>Thank you !</p>
            </body>
        </html>';
            Mail::to($campaignOwner->email)->send(new CampaignEmail($campaignOwner->name,  $htmlContent, 'Campaign Processed Successfully'));
        }
    }
}
