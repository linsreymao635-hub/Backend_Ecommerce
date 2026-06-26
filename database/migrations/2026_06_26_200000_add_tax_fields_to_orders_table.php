<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->after('user_id');
            $table->decimal('tax_amount', 10, 2)->default(0.00)->after('subtotal');
            $table->decimal('tax_rate', 5, 2)->default(0.00)->after('tax_amount');
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_amount', 'tax_rate']);
        });
    }
};
