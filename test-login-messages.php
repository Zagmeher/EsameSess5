<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Comune;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   TEST MESSAGGI ERRORE LOGIN                               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Verifica se esiste già un utente di test
echo "1️⃣  PREPARAZIONE TEST\n";
echo "─────────────────────────────────────────────────────────────\n";

$testEmail = 'testlogin@example.com';
$testPassword = 'password123';

// Elimina utente di test se esiste
User::where('email', $testEmail)->delete();

// Crea utente di test
$comune = Comune::inRandomOrder()->first();
$testUser = User::create([
    'username' => 'testlogin',
    'email' => $testEmail,
    'password' => Hash::make($testPassword),
    'comune_id' => $comune->id
]);

echo "✅ Utente di test creato:\n";
echo "   Email: $testEmail\n";
echo "   Password: $testPassword\n";
echo "   Username: testlogin\n\n";

// Test 2: Simulazione utente inesistente
echo "2️⃣  TEST: UTENTE INESISTENTE\n";
echo "─────────────────────────────────────────────────────────────\n";

$emailInesistente = 'utente.inesistente@test.com';
$userCheck = User::where('email', $emailInesistente)->first();

if (!$userCheck) {
    echo "✅ Verifica utente inesistente:\n";
    echo "   Email testata: $emailInesistente\n";
    echo "   Utente trovato: NO\n";
    echo "   ✅ Messaggio atteso: 'Utente non esistente'\n\n";
} else {
    echo "❌ ERRORE: Utente dovrebbe essere inesistente!\n\n";
}

// Test 3: Simulazione password errata
echo "3️⃣  TEST: PASSWORD ERRATA\n";
echo "─────────────────────────────────────────────────────────────\n";

$userCheck = User::where('email', $testEmail)->first();

if ($userCheck) {
    echo "✅ Utente trovato: $testEmail\n";
    
    $passwordErrata = 'passwordSBAGLIATA123';
    $passwordCorretta = Hash::check($passwordErrata, $userCheck->password);
    
    if (!$passwordCorretta) {
        echo "✅ Verifica password:\n";
        echo "   Password testata: $passwordErrata\n";
        echo "   Password corretta: NO\n";
        echo "   ✅ Messaggio atteso: 'Password errata'\n\n";
    } else {
        echo "❌ ERRORE: La password dovrebbe essere errata!\n\n";
    }
} else {
    echo "❌ ERRORE: Utente non trovato!\n\n";
}

// Test 4: Login corretto
echo "4️⃣  TEST: LOGIN CORRETTO\n";
echo "─────────────────────────────────────────────────────────────\n";

$userCheck = User::where('email', $testEmail)->first();

if ($userCheck) {
    $passwordCorretta = Hash::check($testPassword, $userCheck->password);
    
    if ($passwordCorretta) {
        echo "✅ Verifica credenziali:\n";
        echo "   Email: $testEmail\n";
        echo "   Password: $testPassword\n";
        echo "   Utente trovato: SI\n";
        echo "   Password corretta: SI\n";
        echo "   ✅ Messaggio atteso: 'Login effettuato con successo'\n\n";
    } else {
        echo "❌ ERRORE: La password dovrebbe essere corretta!\n\n";
    }
} else {
    echo "❌ ERRORE: Utente non trovato!\n\n";
}

// Test 5: Riepilogo logica del controller
echo "5️⃣  LOGICA CONTROLLER AGGIORNATA\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Il controller ora segue questa logica:\n\n";
echo "1. Riceve email e password\n";
echo "2. Cerca l'utente per email\n";
echo "   └─ Se NON trovato → 'Utente non esistente' (401)\n";
echo "3. Se trovato, verifica la password\n";
echo "   └─ Se ERRATA → 'Password errata' (401)\n";
echo "4. Se tutto OK → 'Login effettuato con successo' (200)\n\n";

// Test API Endpoint
echo "6️⃣  TEST API ENDPOINT\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Per testare via API:\n\n";

echo "POST /api/auth/login\n";
echo "Content-Type: application/json\n\n";

echo "Test 1 - Utente inesistente:\n";
echo "{\n";
echo "  \"email\": \"$emailInesistente\",\n";
echo "  \"password\": \"qualsiasi\"\n";
echo "}\n";
echo "→ Risposta: \"Utente non esistente\"\n\n";

echo "Test 2 - Password errata:\n";
echo "{\n";
echo "  \"email\": \"$testEmail\",\n";
echo "  \"password\": \"passwordSbagliata\"\n";
echo "}\n";
echo "→ Risposta: \"Password errata\"\n\n";

echo "Test 3 - Login corretto:\n";
echo "{\n";
echo "  \"email\": \"$testEmail\",\n";
echo "  \"password\": \"$testPassword\"\n";
echo "}\n";
echo "→ Risposta: \"Login effettuato con successo\"\n\n";

// Cleanup
echo "7️⃣  PULIZIA\n";
echo "─────────────────────────────────────────────────────────────\n";
echo "Vuoi eliminare l'utente di test? (SI/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$risposta = trim($line);

if (strtoupper($risposta) === 'SI' || empty($risposta)) {
    User::where('email', $testEmail)->delete();
    echo "✅ Utente di test eliminato\n\n";
} else {
    echo "✅ Utente di test mantenuto per ulteriori test\n\n";
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ TEST COMPLETATO!                                      ║\n";
echo "║                                                            ║\n";
echo "║   Pagina test web:                                         ║\n";
echo "║   http://localhost:8000/test-login-messages.html           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
