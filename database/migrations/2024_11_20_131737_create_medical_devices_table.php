<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('medical_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('brand');
            $table->string('location');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('condition', ['new', 'used', 'refurbished']);
            $table->string('image')->nullable(); // Path to the device image
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_devices');
    }
}
