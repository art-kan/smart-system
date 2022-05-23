<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegesOnGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges_on_groups', function (Blueprint $table) {
            $table->foreignId('group_id');
            $table->foreignId('target_id');

            $table->primary(['group_id', 'target_id']);

            $table->boolean('inspect_priv')->default(false);
            $table->boolean('edit_info_priv')->default(false);
            $table->boolean('dissolve_priv')->default(false);

            $table->boolean('group_members_priv')->default(false);
            $table->boolean('add_members_priv')->default(false);
            $table->boolean('remove_members_priv')->default(false);
            $table->boolean('edit_members_priv')->default(false);
            $table->boolean('fire_members_priv')->default(false);

            $table->boolean('ask_reports_priv')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privileges_on_groups');
    }
}
