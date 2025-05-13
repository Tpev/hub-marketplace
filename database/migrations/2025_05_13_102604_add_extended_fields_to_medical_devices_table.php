<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtendedFieldsToMedicalDevicesTable extends Migration
{
    public function up()
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            $table->decimal('price_new', 10, 2)->nullable()->after('price');
            $table->boolean('shipping_available')->default(false)->after('price_new');
            $table->string('main_category')->nullable()->after('shipping_available');
            $table->string('aux_category')->nullable()->after('main_category');
            $table->string('city')->nullable()->after('aux_category');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->integer('quantity')->default(1)->after('country');
        });
    }

    public function down()
    {
        Schema::table('medical_devices', function (Blueprint $table) {
            $table->dropColumn([
                'price_new',
                'shipping_available',
                'main_category',
                'aux_category',
                'city',
                'state',
                'country',
                'quantity',
            ]);
        });
    }
}
