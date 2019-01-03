<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('type_id');
            $table->string('type');

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_number');
            $table->string('alternate_mobile_number')->nullable();
            $table->string('designation')->nullable();
            $table->string('branch')->nullable();

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
        //
    }
}
