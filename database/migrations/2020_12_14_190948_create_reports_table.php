<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_request_id');
            $table->foreignId('created_by');
            $table->text('body');
            $table->string('status')->default(\App\Models\Report::DEFAULT_STATE);
            $table->foreignId('document_set_id')->nullable();
            $table->timestamps();

            $table->index(['report_request_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
