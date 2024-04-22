<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->integer('manage_id')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('parent_id');
            $table->integer('creator_id')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->integer('tool_id');
            $table->string('action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
//        Schema::dropIfExists('user_role');
        Schema::dropIfExists('permissions');
    }
};
