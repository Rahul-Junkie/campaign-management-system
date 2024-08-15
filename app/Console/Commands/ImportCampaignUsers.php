<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignUser;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ImportCampaignUsers extends Command
{
    protected $signature = 'import:campaign-users {campaignId} {file}';
    protected $description = 'Import users from a CSV file into a campaign';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $campaignId = $this->argument('campaignId');
        $file = $this->argument('file');

        $campaign = Campaign::findOrFail($campaignId);

        // Load CSV file
        $csv = Reader::createFromPath(Storage::path($file), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(['name', 'email']); // Adjust headers according to your CSV file

        foreach ($records as $record) {
            CampaignUser::updateOrCreate(
                ['email' => $record['email'], 'campaign_id' => $campaignId],
                [
                    'name' => $record['name'],
                    'email_sent_status' => 'pending',
                ]
            );
        }

        $this->info('Users imported successfully!');
    }
}
