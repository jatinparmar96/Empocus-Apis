<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('account_name');
            $table->string('account_employee_number');
            $table->string('account_annual_revenue');
            $table->string('account_website');
            $table->string('account_phone');
            $table->string('account_industry_type');
            $table->string('account_business_type');
            $table->string('account_facebook_link');
            $table->string('account_twitter_link');
            $table->string('account_linkedin_link');
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
        Schema::dropIfExists('accounts');
    }
}
