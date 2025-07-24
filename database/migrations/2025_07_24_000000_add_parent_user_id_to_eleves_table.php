<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_user_id')->nullable()->after('id');
            $table->foreign('parent_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropForeign(['parent_user_id']);
            $table->dropColumn('parent_user_id');
        });
    }
}; 