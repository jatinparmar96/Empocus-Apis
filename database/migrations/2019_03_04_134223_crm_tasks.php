<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrmTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            
            $table->string('title');
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->string('task_type');
            $table->string('outcome')->nullable();
            $table->string('description')->nullable();
            
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
