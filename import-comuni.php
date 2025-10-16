<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Inizio importazione comuni dal CSV...\n\n";

$csvFile = __DIR__ . '/comuniItaliani.csv';

if (!file_exists($csvFile)) {
    die("Errore: File CSV non trovato!\n");
}

$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("Errore: Impossibile aprire il file CSV!\n");
}

$imported = 0;
$errors = 0;

// Leggi il file riga per riga
while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    try {
        // I dati nel CSV sono: id, nome, regione, provincia, sigla, codice_catastale, cap
        DB::table('comuni')->insert([
            'id' => (int)$data[0],
            'nome' => $data[1],
            'regione' => $data[2],
            'provincia' => $data[3],
            'sigla_provincia' => $data[4],
            'codice_catastale' => $data[5],
            'cap' => $data[6],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $imported++;
        
        // Mostra il progresso ogni 100 record
        if ($imported % 100 == 0) {
            echo "Importati $imported comuni...\n";
        }
        
    } catch (Exception $e) {
        $errors++;
        echo "Errore nell'importazione del comune {$data[1]}: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n";
echo "Importazione completata!\n";
echo "Comuni importati: $imported\n";
echo "Errori: $errors\n";

// Verifica il totale nel database
$total = DB::table('comuni')->count();
echo "Totale comuni nel database: $total\n";
