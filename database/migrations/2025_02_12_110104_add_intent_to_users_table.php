<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Using enum for intent with a default value (adjust default if needed)
            $table->enum('intent', ['Buyer', 'Seller', 'Both', 'None'])->default('None')->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('intent');
        });
    }
};
