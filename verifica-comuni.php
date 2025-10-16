<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Verifica dati nella tabella comuni\n";
echo "===================================\n\n";

$total = DB::table('comuni')->count();
echo "Totale comuni: $total\n\n";

echo "Primi 5 comuni:\n";
$comuni = DB::table('comuni')->limit(5)->get();
foreach ($comuni as $comune) {
    echo "- {$comune->nome} ({$comune->provincia}, {$comune->regione}) - CAP: {$comune->cap}\n";
}

echo "\n";
echo "Comuni per regione:\n";
$perRegione = DB::table('comuni')
    ->select('regione', DB::raw('COUNT(*) as totale'))
    ->groupBy('regione')
    ->orderBy('totale', 'desc')
    ->get();
    
foreach ($perRegione as $reg) {
    echo "- {$reg->regione}: {$reg->totale} comuni\n";
}
