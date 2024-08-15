<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('csv_path')->nullable();
            $table->text('email_body')->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('total_contacts')->default(0)->nullable();
            $table->integer('processed_contacts')->default(0)->nullable();
            $table->enum('status', ['Importing Users', 'Active', 'In progress', 'Failed', 'Processed'])->default('Active'); // Example statuses
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
