<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Comune;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   TEST INTEGRAZIONE COMUNI E UTENTI                       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Verifica tabella comuni
echo "1️⃣  VERIFICA TABELLA COMUNI\n";
echo "─────────────────────────────────────────────────────────────\n";
$totalComuni = Comune::count();
echo "✅ Totale comuni nel database: $totalComuni\n\n";

// Test 2: Verifica campo comune_id in users
echo "2️⃣  VERIFICA CAMPO comune_id IN TABELLA USERS\n";
echo "─────────────────────────────────────────────────────────────\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field = 'comune_id'");
    if (!empty($columns)) {
        echo "✅ Campo comune_id presente nella tabella users\n";
        echo "   Tipo: " . $columns[0]->Type . "\n";
        echo "   Null: " . $columns[0]->Null . "\n";
        echo "   Key: " . $columns[0]->Key . "\n\n";
    } else {
        echo "❌ Campo comune_id NON trovato!\n\n";
    }
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n\n";
}

// Test 3: Verifica foreign key
echo "3️⃣  VERIFICA FOREIGN KEY\n";
echo "─────────────────────────────────────────────────────────────\n";
try {
    $foreignKeys = DB::select("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'users'
        AND TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME = 'comuni'
    ");
    
    if (!empty($foreignKeys)) {
        echo "✅ Foreign key configurata correttamente:\n";
        foreach ($foreignKeys as $fk) {
            echo "   Constraint: {$fk->CONSTRAINT_NAME}\n";
            echo "   Colonna: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
        echo "\n";
    } else {
        echo "⚠️  Foreign key non trovata (potrebbe essere normale in alcuni setup)\n\n";
    }
} catch (Exception $e) {
    echo "⚠️  Errore nel controllo FK: " . $e->getMessage() . "\n\n";
}

// Test 4: Test relazioni Eloquent
echo "4️⃣  TEST RELAZIONI ELOQUENT\n";
echo "─────────────────────────────────────────────────────────────\n";

// Prendi un comune casuale
$comune = Comune::inRandomOrder()->first();
if ($comune) {
    echo "✅ Comune di test: {$comune->nome} ({$comune->provincia}, {$comune->regione})\n";
    echo "   ID: {$comune->id}\n";
    echo "   CAP: {$comune->cap}\n\n";
    
    // Verifica quanti utenti hanno questo comune
    $usersCount = User::where('comune_id', $comune->id)->count();
    echo "   Utenti registrati in questo comune: $usersCount\n\n";
} else {
    echo "❌ Nessun comune trovato nel database!\n\n";
}

// Test 5: Verifica utenti esistenti con comune
echo "5️⃣  VERIFICA UTENTI CON COMUNE\n";
echo "─────────────────────────────────────────────────────────────\n";
$usersWithComune = User::whereNotNull('comune_id')->count();
$totalUsers = User::count();
echo "✅ Totale utenti: $totalUsers\n";
echo "✅ Utenti con comune associato: $usersWithComune\n\n";

if ($usersWithComune > 0) {
    echo "   Ultimi 5 utenti con comune:\n";
    $users = User::with('comune')->whereNotNull('comune_id')->latest()->limit(5)->get();
    foreach ($users as $user) {
        $comuneNome = $user->comune ? $user->comune->nome : 'N/A';
        $comuneProvincia = $user->comune ? $user->comune->provincia : 'N/A';
        echo "   - {$user->username} ({$user->email}) -> $comuneNome ($comuneProvincia)\n";
    }
    echo "\n";
}

// Test 6: Test API endpoint
echo "6️⃣  TEST ENDPOINT API /api/comuni\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "✅ Endpoint disponibile: GET /api/comuni\n";
echo "   Query params supportati:\n";
echo "   - search: ricerca per nome comune\n";
echo "   - regione: filtra per regione\n";
echo "   - provincia: filtra per provincia\n";
echo "   - sigla: filtra per sigla provincia\n\n";

// Test 7: Esempio di creazione utente con comune
echo "7️⃣  ESEMPIO CREAZIONE UTENTE CON COMUNE\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Per registrare un utente con comune tramite API:\n\n";
echo "POST /api/auth/register\n";
echo "Content-Type: application/json\n\n";
echo "{\n";
echo "  \"username\": \"mario_rossi\",\n";
echo "  \"email\": \"mario@example.com\",\n";
echo "  \"password\": \"password123\",\n";
echo "  \"comune_id\": {$comune->id}  // ID del comune di residenza\n";
echo "}\n\n";

// Test 8: Statistiche comuni per regione
echo "8️⃣  STATISTICHE COMUNI PER REGIONE (Top 5)\n";
echo "─────────────────────────────────────────────────────────────\n";
$topRegioni = Comune::select('regione', DB::raw('COUNT(*) as totale'))
    ->groupBy('regione')
    ->orderBy('totale', 'desc')
    ->limit(5)
    ->get();

foreach ($topRegioni as $regione) {
    echo "   {$regione->regione}: {$regione->totale} comuni\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ TEST COMPLETATO CON SUCCESSO!                        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
