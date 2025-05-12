<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up()
{
    Schema::create('buyer_inquiries', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable(); // optional for guest
        $table->string('email')->nullable(); // optional for guest
        $table->text('message');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_inquiries');
    }
};
