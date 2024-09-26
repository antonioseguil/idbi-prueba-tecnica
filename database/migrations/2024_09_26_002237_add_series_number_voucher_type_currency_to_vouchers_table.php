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
            $table->string('voucher_serie', 50)->nullable()->after('xml_content');
            $table->string('voucher_number', 50)->nullable()->after('voucher_serie');
            $table->string('voucher_type_id', 2)->nullable()->after('voucher_number'); // Codigo de tipo de comprobante (factura, boleta, etc.)
            $table->string('currency', 3)->nullable()->after('voucher_type_id'); // 3 letras (e.g., PEN, USD)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['voucher_serie', 'voucher_number', 'voucher_type_id', 'currency']);
        });
    }
};
