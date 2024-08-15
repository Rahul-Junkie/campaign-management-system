<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCampaignUsers;
use App\Jobs\SendCampaignEmailJob;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Notifications\CampaignProcessed;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\LazyCollection;

class CampaignController extends Controller
{

    public $invalid = 0;
    public function sendEmails(Campaign $campaign)
    {
        $data = Excel::toCollection(null, storage_path('app/' . $campaign->csv_path))->first();

        foreach ($data as $row) {
            Mail::send('emails.campaign', ['username' => $row['name']], function ($message) use ($row) {
                $message->to($row['email'])->subject('Your Campaign');
            });
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = Campaign::with('users')->get();
        return Inertia::render('Campaign/create', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email_body' => 'required|string|max:255',
            'csv' => 'required|file|mimes:csv,txt',
        ]);
        // Handle file upload and store the file
        $csvPath = $request->file('csv')->store('csvs');
        $fullPath = Storage::path($csvPath);

        DB::beginTransaction();
        $this->invalid == 0;
        try {
            // Create the campaign before processing the CSV
            $campaign = Campaign::create([
                'name' => $validated['name'],
                'email_body' => $validated['email_body'],
                'csv_path' => $csvPath,
                'created_by' => auth()->id(),
            ]);
            // Process CSV file using LazyCollection for efficient memory usage
            LazyCollection::make(function () use ($fullPath, $request) {
                $handle = fopen($fullPath, 'r');
                while (($line = fgetcsv($handle)) !== false) {
                    yield $line;
                }
                fclose($handle);
            })
                ->skip(1) // Skip the header row
                ->chunk(500) // Adjust the chunk size according to your server's capacity
                ->each(function ($lines) use ($campaign, $request) {
                    $batch = [];
                    foreach ($lines as $line) {
                        // Validate each line (name and email)
                        if (empty($line[0]) || empty($line[1]) || !filter_var($line[1], FILTER_VALIDATE_EMAIL)) {
                            DB::rollBack();
                            $request->session()->flash('error', 'Invalid data in CSV.');
                            $this->invalid = 1;
                            return Inertia::location(route('campaignsCreate'));
                        }

                        $batch[] = [
                            'campaign_id' => $campaign->id,
                            'name' => $line[0],
                            'email' => $line[1],
                            'email_sent_status' => 'Pending',
                        ];
                        $campaign->increment('total_contacts');
                    }
                    // Insert the batch into the database
                    CampaignUser::insert($batch);
                });

            DB::commit();
            Storage::delete($csvPath);
            if ($this->invalid) {
                throw new \Exception('Invalid data in CSV.');
            }
            // Dispatch a job to send emails after processing the CSV
            ProcessCampaignUsers::dispatch($campaign);
            $request->session()->flash('success', 'Campaign added successfully');
            return Inertia::location(route('campaignsCreate'));
            // return redirect()->route('campaignsCreate')->with('success', 'Campaign created and users imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('error', 'An error occurred: ' . $e->getMessage());
            return Inertia::location(route('campaignsCreate'));
        }
    }

    public function getUsers(Campaign $campaign)
    {
        // Return users related to the campaign
        return response()->json($campaign->users);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
