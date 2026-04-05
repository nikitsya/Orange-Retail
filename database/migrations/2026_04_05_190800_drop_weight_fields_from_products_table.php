<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight_value', 'weight_unit']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight_value', 8, 2)->nullable()->after('pack_size');
            $table->string('weight_unit', 20)->nullable()->after('weight_value');
        });
    }
};
