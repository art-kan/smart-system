<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegesOnReportRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges_on_report_requests', function (Blueprint $table) {
            $table->foreignId('group_id');
            $table->foreignId('target_id');
            $table->primary(['group_id', 'target_id']);

            $table->boolean('inspect_priv')->default(false);
            $table->boolean('close_priv')->default(false);
            $table->boolean('open_priv')->default(false);
            $table->boolean('edit_info_priv')->default(false);
            $table->boolean('response_priv')->default(false);
            $table->boolean('reject_response_priv')->default(false);
            $table->boolean('accept_response_priv')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privileges_on_report_requests');
    }
}
