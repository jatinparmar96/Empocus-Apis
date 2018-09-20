<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('ca_company_name');
            $table->string('ca_company_display_name');
            $table->string('ca_category');
            $table->string('ca_code');
            $table->string('ca_opening_amount');
            $table->string('ca_opening_type');
            $table->string('ca_first_name');
            $table->string('ca_last_name');
            $table->string('ca_mobile_number');
            $table->string('ca_fax');
            $table->string('ca_email');
            $table->string('ca_website');
            $table->string('ca_designation');
            $table->string('ca_branch');
            $table->string('address_id'); 
            $table->string('ca_pan');
            $table->string('ca_gstn');
            $table->string('ca_tan');
            $table->string('ca_commodity');
            $table->string('ca_ecc_no');
            $table->string('ca_rc_no');
            $table->string('ca_division');
            $table->string('ca_range');
            $table->string('ca_commissionerate');
            $table->string('ca_tin_no');
            $table->string('ca_date_opened');
            $table->string('ca_cst_no');
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
