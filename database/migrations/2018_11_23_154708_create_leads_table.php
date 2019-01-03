<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('company_name');
            $table->string('lead_status');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('company_email')->nullable();
            $table->string('company_employee_number')->nullable();
            $table->string('company_annual_revenue')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_industry_type')->nullable();
            $table->string('company_business_type')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('deal_name')->nullable();
            $table->integer('deal_value')->nullable();
            $table->date('deal_expected_close_date')->nullable();
            $table->integer('deal_product')->nullable();
            $table->string('source')->nullable();
            $table->string('campaign')->nullable();
            $table->string('medium')->nullable();
            $table->string('keyword')->nullable();
            $table->integer('created_by_id');
            $table->integer('updated_by_id');
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
        Schema::dropIfExists('leads');
    }
}
