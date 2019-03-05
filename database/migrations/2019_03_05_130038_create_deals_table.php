<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('deal_stage');
            $table->integer('product_id')->refernces('id')->on('raw_products')->onDelete('set null'); 
            $table->string('deal_value');
            $table->string('payment_status');
            $table->string('expected_close_date');
            $table->string('probability');
            $table->string('type');
            $table->string('source');
            $table->string('campaign');
            
            $table->timestamps();
            $table->integer('created_by_id');
            $table->integer('updated_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
