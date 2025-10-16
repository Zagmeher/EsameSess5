<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Comune;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Esempi di utilizzo del model Comune\n";
echo "====================================\n\n";

// Esempio 1: Cercare un comune per nome
echo "1. Cerca comuni con 'Roma' nel nome:\n";
$roma = Comune::byNome('Roma')->get();
foreach ($roma as $comune) {
    echo "   - {$comune->nome} ({$comune->provincia}) - {$comune->cap}\n";
}

echo "\n2. Tutti i comuni della regione Lazio:\n";
$lazio = Comune::byRegione('Lazio')->count();
echo "   Totale: {$lazio} comuni\n";

echo "\n3. Comuni della provincia di Milano:\n";
$milano = Comune::byProvincia('Milano')->limit(5)->get();
foreach ($milano as $comune) {
    echo "   - {$comune->nome} - {$comune->cap}\n";
}

echo "\n4. Comuni con sigla provincia TO:\n";
$torino = Comune::bySiglaProvincia('TO')->limit(5)->get();
foreach ($torino as $comune) {
    echo "   - {$comune->nome} - {$comune->cap}\n";
}

echo "\n5. Cerca un comune specifico (Napoli):\n";
$napoli = Comune::where('nome', 'Napoli')->first();
if ($napoli) {
    echo "   Nome: {$napoli->nome}\n";
    echo "   Provincia: {$napoli->provincia}\n";
    echo "   Regione: {$napoli->regione}\n";
    echo "   Sigla: {$napoli->sigla_provincia}\n";
    echo "   Codice Catastale: {$napoli->codice_catastale}\n";
    echo "   CAP: {$napoli->cap}\n";
}

echo "\n✓ Importazione completata con successo!\n";
