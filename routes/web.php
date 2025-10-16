<?php

/**
 * ============================================
 * FILE DI ROUTING WEB - LARAVEL
 * ============================================
 * Questo file definisce tutte le rotte HTTP
 * dell'applicazione web (non API).
 * 
 * Le rotte mappano gli URL alle risposte
 * (view, controller, closure functions).
 * ============================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AuthController;

/**
 * ROTTA: Redirect Root a Login
 * 
 * URL: /
 * Metodo: GET
 * 
 * Reindirizza la root dell'applicazione alla pagina di login
 */
Route::get('/', function () {
    return view('login'); // Mostra pagina di login
})->name('login');

/**
 * ROTTA: Homepage / Dashboard
 * 
 * URL: /home
 * Metodo: GET
 * Nome: 'home'
 * 
 * Mostra la dashboard per utenti autenticati.
 * La dashboard verifica il token JWT tramite JavaScript.
 */
Route::get('/home', function () {
    return view('dashboard'); // Carica la view dashboard.blade.php
})->name('home'); // Nome rotta usato per helper route('home')

/**
 * ROTTA: Pagina di Registrazione
 * 
 * URL: /register
 * Metodo: GET
 * Nome: 'register'
 * 
 * Mostra il form di registrazione nuovo utente
 * (resources/views/register.blade.php).
 */
Route::get('/register', function () {
    return view('register'); // Carica la view register.blade.php
})->name('register'); // Nome rotta usato per helper route('register')

/**
 * ============================================
 * ROTTE API - AUTENTICAZIONE JWT
 * ============================================
 * Endpoint API RESTful per autenticazione con JWT.
 * Sicurezza massima con token, rate limiting e validazione.
 */

/**
 * --------------------------------------------
 * ROTTE PUBBLICHE (NON RICHIEDONO AUTENTICAZIONE)
 * --------------------------------------------
 */

/**
 * API: Registrazione Nuovo Utente
 * 
 * URL: /api/auth/register
 * Metodo: POST
 * Controller: AuthController@register
 * Autenticazione: NO
 * Rate Limit: 5 tentativi/15 minuti
 * 
 * Payload atteso:
 * {
 *   "username": "string (min 3, max 50, alfanumerico)",
 *   "email": "string (formato email, unico)",
 *   "password": "string (min 6 caratteri)"
 * }
 * 
 * Risposta successo (201):
 * {
 *   "success": true,
 *   "message": "Registrazione completata con successo!",
 *   "data": {
 *     "user": { "id", "username", "email" },
 *     "access_token": "JWT...",
 *     "refresh_token": "random_string...",
 *     "token_type": "bearer",
 *     "expires_in": 3600
 *   }
 * }
 */
Route::post('/api/auth/register', [AuthController::class, 'register']);

/**
 * API: Login Utente
 * 
 * URL: /api/auth/login
 * Metodo: POST
 * Controller: AuthController@login
 * Autenticazione: NO
 * Rate Limit: 5 tentativi/15 minuti
 * 
 * Payload atteso:
 * {
 *   "email": "string (formato email)",
 *   "password": "string"
 * }
 * 
 * Risposta successo (200):
 * {
 *   "success": true,
 *   "message": "Login effettuato con successo",
 *   "data": {
 *     "user": { "id", "username", "email" },
 *     "access_token": "JWT...",
 *     "refresh_token": "random_string...",
 *     "token_type": "bearer",
 *     "expires_in": 3600
 *   }
 * }
 */
Route::post('/api/auth/login', [AuthController::class, 'login']);

/**
 * API: Refresh Token
 * 
 * URL: /api/auth/refresh
 * Metodo: POST
 * Controller: AuthController@refresh
 * Autenticazione: NO (ma richiede refresh_token valido)
 * 
 * Payload atteso:
 * {
 *   "refresh_token": "string (refresh token ricevuto al login)"
 * }
 * 
 * Risposta successo (200):
 * {
 *   "success": true,
 *   "message": "Token rinnovato con successo",
 *   "data": {
 *     "access_token": "nuovo_JWT...",
 *     "refresh_token": "nuovo_random_string...",
 *     "token_type": "bearer",
 *     "expires_in": 3600
 *   }
 * }
 * 
 * NOTA: Implementa token rotation per sicurezza -
 * il vecchio refresh token viene revocato.
 */
Route::post('/api/auth/refresh', [AuthController::class, 'refresh']);

/**
 * --------------------------------------------
 * ROTTE PROTETTE (RICHIEDONO JWT VALIDO)
 * --------------------------------------------
 * Middleware: auth:api
 * Header richiesto: Authorization: Bearer {access_token}
 */
Route::middleware('auth:api')->group(function () {
    
    /**
     * API: Profilo Utente Corrente
     * 
     * URL: /api/auth/me
     * Metodo: GET
     * Controller: AuthController@me
     * Autenticazione: SI (JWT)
     * 
     * Headers:
     * Authorization: Bearer {access_token}
     * 
     * Risposta successo (200):
     * {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "username": "mario_rossi",
     *     "email": "mario@example.com",
     *     "email_verified_at": null,
     *     "created_at": "2025-10-14T17:30:00.000000Z"
     *   }
     * }
     */
    Route::get('/api/auth/me', [AuthController::class, 'me']);

    /**
     * API: Logout Utente
     * 
     * URL: /api/auth/logout
     * Metodo: POST
     * Controller: AuthController@logout
     * Autenticazione: SI (JWT)
     * 
     * Headers:
     * Authorization: Bearer {access_token}
     * 
     * Payload opzionale:
     * {
     *   "refresh_token": "string (per revocarlo)"
     * }
     * 
     * Risposta successo (200):
     * {
     *   "success": true,
     *   "message": "Logout effettuato con successo"
     * }
     * 
     * NOTA: Invalida l'access token corrente e revoca
     * il refresh token se fornito.
     */
    Route::post('/api/auth/logout', [AuthController::class, 'logout']);

    /**
     * API: Cambio Password
     * 
     * URL: /api/auth/change-password
     * Metodo: POST
     * Controller: AuthController@changePassword
     * Autenticazione: SI (JWT)
     * 
     * Headers:
     * Authorization: Bearer {access_token}
     * 
     * Payload:
     * {
     *   "current_password": "string",
     *   "new_password": "string (min 6, diversa dalla corrente)",
     *   "new_password_confirmation": "string (uguale a new_password)"
     * }
     * 
     * Risposta successo (200):
     * {
     *   "success": true,
     *   "message": "Password cambiata con successo. Effettua nuovamente il login."
     * }
     * 
     * NOTA SICUREZZA: Cambiare password revoca automaticamente
     * TUTTI i refresh token e invalida il token corrente.
     * L'utente deve fare login da tutti i dispositivi.
     */
    Route::post('/api/auth/change-password', [AuthController::class, 'changePassword']);

    /**
     * API: Logout da Tutti i Dispositivi
     * 
     * URL: /api/auth/logout-all
     * Metodo: POST
     * Controller: AuthController@logoutAll
     * Autenticazione: SI (JWT)
     * 
     * Headers:
     * Authorization: Bearer {access_token}
     * 
     * Risposta successo (200):
     * {
     *   "success": true,
     *   "message": "Logout effettuato da tutti i dispositivi (N sessioni chiuse)"
     * }
     * 
     * NOTA: Revoca TUTTI i refresh token dell'utente.
     * Utile in caso di compromissione account.
     */
    Route::post('/api/auth/logout-all', [AuthController::class, 'logoutAll']);
});

/**
 * ============================================
 * ROTTA LEGACY - MANTENUTA PER COMPATIBILITÀ
 * ============================================
 * Questa rotta è stata sostituita da /api/auth/register
 * ma è mantenuta per compatibilità con il frontend esistente.
 * Considerare di rimuoverla dopo aggiornamento frontend.
 */
Route::post('/api/register', [RegisterController::class, 'register']);

/**
 * API: Ottieni Lista Comuni
 * 
 * URL: /api/comuni
 * Metodo: GET
 * Autenticazione: NO
 * 
 * Query params opzionali:
 * - search: string (cerca per nome comune)
 * - regione: string (filtra per regione)
 * - provincia: string (filtra per provincia)
 * - sigla: string (filtra per sigla provincia)
 * 
 * Risposta successo (200):
 * {
 *   "success": true,
 *   "data": [
 *     {
 *       "id": 1,
 *       "nome": "Roma",
 *       "provincia": "Roma",
 *       "regione": "Lazio",
 *       "sigla_provincia": "RM",
 *       "cap": "00100"
 *     }
 *   ]
 * }
 */
Route::get('/api/comuni', function () {
    $query = \App\Models\Comune::query();
    
    // Filtra per ricerca nel nome
    if (request('search')) {
        $query->where('nome', 'like', '%' . request('search') . '%');
    }
    
    // Filtra per regione
    if (request('regione')) {
        $query->where('regione', request('regione'));
    }
    
    // Filtra per provincia
    if (request('provincia')) {
        $query->where('provincia', request('provincia'));
    }
    
    // Filtra per sigla provincia
    if (request('sigla')) {
        $query->where('sigla_provincia', request('sigla'));
    }
    
    $comuni = $query->orderBy('nome')->get();
    
    return response()->json([
        'success' => true,
        'data' => $comuni
    ]);
});

