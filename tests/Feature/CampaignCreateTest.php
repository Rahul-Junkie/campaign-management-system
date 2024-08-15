<?php

use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\User;

use App\Models\CampaignUser;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Create a user and authenticate them
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('Create User , Campaign and import user from csv, Send Mail', function () {
    // Create a campaign
    $campaign = Campaign::factory()->create([
        'name' => 'Test Campaign',
        'created_by' => $this->user->id,
    ]);
    Mail::fake();

    // Create a CSV file in the storage
    // Read the CSV file
    $fullPath = Storage::path('csvs/users.csv');
    $filecsv = file($fullPath);
    $users = [];
    foreach ($filecsv as $i => $data) {

        if ($i === 0) {
            continue; // Skip header
        }
        $data = explode(',', $data);
        // Insert users from CSV into CampaignUser table
        $campaignUser =   CampaignUser::insert([
            'campaign_id' => $campaign->id,
            'name' => $data[0],
            'email' => $data[1],
            'email_sent_status' => 'Pending',
        ]);
        $users = array_merge($users, [$campaignUser]);
        Mail::to($data[1])->send(new CampaignEmail($campaign->name, 'Testing ', 'Campaign testing !'));
    }

    // Assert that the emails were sent
    Mail::assertSent(CampaignEmail::class, function ($mail) use ($data) {
        return $mail->hasTo($data[1]);
    });

    // Assert that the users were inserted into the CampaignUser table
    $this->assertDatabaseHas('campaign_users', [
        'campaign_id' => $campaign->id,
        'name' => 'Jane Doe',
        'email' => 'janedoe@example.com',
    ]);
});
