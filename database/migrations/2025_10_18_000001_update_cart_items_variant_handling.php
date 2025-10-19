<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop foreign keys temporarily so we can alter supporting indexes
            if (Schema::hasColumn('cart_items', 'cart_id')) {
                $table->dropForeign(['cart_id']);
            }

            if (Schema::hasColumn('cart_items', 'product_id')) {
                $table->dropForeign(['product_id']);
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'variant_signature')) {
                $table->string('variant_signature', 100)->nullable()->after('product_attributes');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'variant_signature')) {
                $table->dropUnique('cart_items_cart_id_product_id_unique');
                $table->unique(['cart_id', 'product_id', 'variant_signature'], 'cart_items_unique_variant');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        DB::table('cart_items')
            ->whereNull('variant_signature')
            ->update(['variant_signature' => '__default__']);
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'cart_id')) {
                $table->dropForeign(['cart_id']);
            }

            if (Schema::hasColumn('cart_items', 'product_id')) {
                $table->dropForeign(['product_id']);
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_unique_variant');
            $table->unique(['cart_id', 'product_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'variant_signature')) {
                $table->dropColumn('variant_signature');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
