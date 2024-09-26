<?php

namespace App\Console\Commands\Vouchers;

use App\Models\Voucher;
use Illuminate\Console\Command;
use SimpleXMLElement;

class UpdateVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vouchers:update-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar las nuevas columnas de la tabla Voucher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener todos los vouchers con la columna `voucher_serie` en null
        $vouchers = Voucher::whereNull('voucher_serie')->get();

        // Mostrar mensaje si no hay vouchers para actualizar
        if ($vouchers->isEmpty()) {
            $this->info('No hay vouchers con serie nula para actualizar.');
            return false;
        }

        // Inicializar la barra de progreso con el total de vouchers
        $progressBar = $this->output->createProgressBar($vouchers->count());

        // Comenzar la barra de progreso
        $progressBar->start();

        $vouchers->each(function (Voucher $voucher) use ($progressBar) {
            try {
                // Cargar el contenido XML del voucher
                $xml = new SimpleXMLElement($voucher->xml_content); // Asegúrate de usar 'xml_content' si es donde almacenas el XML

                // Extraer la información del XML
                $fullIdVoucher = explode('-', (string) $xml->xpath('//cbc:ID')[0]);
                $voucherSerie = trim($fullIdVoucher[0]);
                $voucherNumber = trim($fullIdVoucher[1]);
                $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
                $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

                // Actualizar los campos del voucher
                $voucher->voucher_serie = $voucherSerie;
                $voucher->voucher_number = $voucherNumber;
                $voucher->voucher_type_id = $voucherType;
                $voucher->currency = $currency;

                // Guardar los cambios en el voucher
                $voucher->save();

                // Avanzar la barra de progreso
                $progressBar->advance();
            } catch (\Exception $e) {
                // Mostrar mensaje de error para cada voucher que no se pueda procesar
                $this->error("Error al procesar el voucher ID: {$voucher->id}. Detalle: {$e->getMessage()}");
            }
        });

        // Finalizar la barra de progreso
        $progressBar->finish();

        // Salto de línea para mantener el formato de la consola
        $this->newLine();

        $this->info('Actualización de vouchers completada.');
    }

}
