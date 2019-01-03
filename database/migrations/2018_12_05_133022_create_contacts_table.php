<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_account_contacts', function (Blueprint $table) {
            $table->increments('id');
            //foreign ID's
            $table->integer('company_id');
            $table->integer('account_id');
            $table->integer('created_by_id');
            $table->integer('updated_by_id');
            // Data
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('primary_contact_number');
            $table->string('job_title');
            $table->string('department');
            $table->string('work_telephone_number')->nullable();
            $table->string('work_mobile_number')->nullable();
            $table->string('status');
            $table->string('business_type');
            $table->string('facebook_link');
            $table->string('twitter_link');
            $table->string('linkedin_link');
            $table->string('source');
            $table->string('campaign');
            $table->string('medium');
            $table->string('keyword');
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
        Schema::dropIfExists('contacts');
    }
}
