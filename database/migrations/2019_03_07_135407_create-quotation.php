<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
                
            $table->integer('customer_id')->refernces('id')->on('chart_of_accounts');
            $table->integer('address_id')->refernces('id')->on('addresses');
            $table->date('date');
            $table->date('validity_date');
            $table->integer('delivery_at');
            $table->string('transporter_name')->nullable();
            $table->string('eway_bill_number')->nullable();

            $table->double('gross_amount');
            $table->string('discount_type');
            $table->double('discount');
            $table->double('total');
            $table->double('cgst')->nullable();
            $table->double('sgst')->nullable();
            $table->double('igst')->nullable();
            $table->double('delivery_charges')->nullable();
            $table->double('grand_total');

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
       Schema::dropIfExists('quotations');
    }
}
