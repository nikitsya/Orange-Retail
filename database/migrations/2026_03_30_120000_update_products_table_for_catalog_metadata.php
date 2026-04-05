<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode', 32)->nullable()->after('sku');
            $table->string('brand')->nullable()->after('name');
            $table->string('subcategory')->nullable()->after('category');
            $table->string('image_url', 2048)->nullable()->after('description');
            $table->string('unit_type', 50)->nullable()->after('image_url');
            $table->string('pack_size')->nullable()->after('unit_type');
            $table->decimal('weight_value', 8, 2)->nullable()->after('pack_size');
            $table->string('weight_unit', 20)->nullable()->after('weight_value');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unique('barcode');
            $table->dropColumn(['price', 'stock', 'is_active', 'created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('category');
            $table->unsignedInteger('stock')->default(0)->after('price');
            $table->boolean('is_active')->default(true)->after('description');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
            $table->dropColumn([
                'barcode',
                'brand',
                'subcategory',
                'image_url',
                'unit_type',
                'pack_size',
                'weight_value',
                'weight_unit',
            ]);
        });
    }
};
