<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_value', 10, 2)->nullable()->after('weight_unit');
            $table->string('currency', 10)->nullable()->after('price_value');
            $table->string('price_display', 30)->nullable()->after('currency');
            $table->string('unit_price_display', 40)->nullable()->after('price_display');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'price_value',
                'currency',
                'price_display',
                'unit_price_display',
            ]);
        });
    }
};
