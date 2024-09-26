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
        Schema::table('vouchers', function (Blueprint $table) {
            // Agregar las nuevas columnas
            $table->string('voucher_series', 50)->nullable()->after('xml_content');
            $table->string('voucher_number', 50)->nullable()->after('voucher_series');
            $table->string('voucher_type', 50)->nullable()->after('voucher_number'); // Tipo de comprobante (factura, boleta, etc.)
            $table->string('currency', 3)->nullable()->after('voucher_type'); // ISO 4217: 3 letras (e.g., PEN, USD)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['voucher_series', 'voucher_number', 'voucher_type', 'currency']);
        });
    }
};
