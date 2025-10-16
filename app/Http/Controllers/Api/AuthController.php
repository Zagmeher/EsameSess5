<?php

/**
 * ============================================
 * CONTROLLER AUTH - AUTENTICAZIONE JWT
 * ============================================
 * Controller completo per autenticazione con JWT.
 * Gestisce: registrazione, login, logout, refresh token,
 * profilo utente, cambio password.
 * Implementa sicurezza massima con rate limiting,
 * validazione robusta e gestione refresh token.
 * ============================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Controller per gestione autenticazione con JWT
 * Tutti i metodi ritornano risposte JSON standardizzate
 */
class AuthController extends Controller
{
    /**
     * ============================================
     * REGISTRAZIONE NUOVO UTENTE
     * ============================================
     * Endpoint: POST /api/auth/register
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        /**
         * FASE 1: VALIDAZIONE INPUT
         * Regole di validazione robuste per sicurezza
         */
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/', // Solo alfanumerici e underscore
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:100', // Allineato con frontend
            ],
            'comune_id' => [
                'required',
                'integer',
                'exists:comuni,id', // Verifica che il comune esista
            ],
        ], [
            // Messaggi di errore personalizzati in italiano
            'username.required' => 'L\'username è obbligatorio',
            'username.min' => 'Username troppo corto (minimo 3 caratteri)',
            'username.max' => 'Username troppo lungo (massimo 50 caratteri)',
            'username.unique' => 'Questo username è già in uso',
            'username.regex' => 'Carattere non valido',
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'Email non valida',
            'email.unique' => 'Questa email è già registrata',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'Password non valida',
            'password.max' => 'Password troppo lunga (massimo 100 caratteri)',
            'comune_id.required' => 'Il comune di residenza è obbligatorio',
            'comune_id.integer' => 'Comune non valido',
            'comune_id.exists' => 'Il comune selezionato non esiste',
        ]);

        // Se la validazione fallisce, ritorna errori
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errori di validazione',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            /**
             * FASE 2: CREAZIONE UTENTE
             * Password hashata automaticamente dal modello
             */
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash bcrypt
                'comune_id' => $request->comune_id, // Comune di residenza
            ]);

            /**
             * FASE 3: GENERAZIONE TOKEN JWT
             * Crea access token e refresh token
             */
            $token = JWTAuth::fromUser($user); // Access token JWT

            // Genera refresh token e salvalo nel database
            $refreshToken = RefreshToken::generateForUser(
                $user->id,
                $request->ip(),
                $request->userAgent()
            );

            /**
             * FASE 4: RISPOSTA SUCCESSO
             * Ritorna dati utente e token
             */
            return response()->json([
                'success' => true,
                'message' => 'Registrazione completata con successo!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                    ],
                    'access_token' => $token,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60 // Secondi
                ]
            ], 201); // 201 Created

        } catch (\Exception $e) {
            /**
             * GESTIONE ERRORI
             * Log errore e ritorna messaggio generico
             */
            \Log::error('Errore registrazione: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la registrazione. Riprova più tardi.'
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * ============================================
     * LOGIN UTENTE
     * ============================================
     * Endpoint: POST /api/auth/login
     * Autentica l'utente e ritorna JWT
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        /**
         * FASE 1: VALIDAZIONE CREDENZIALI
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email è obbligatoria',
            'email.email' => 'Email non valida',
            'password.required' => 'Password è obbligatoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Credenziali non valide',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /**
             * FASE 2: VERIFICA ESISTENZA UTENTE
             * Prima verifica se l'utente esiste
             */
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                // Utente non trovato
                return response()->json([
                    'success' => false,
                    'message' => 'Utente non esistente'
                ], 401); // 401 Unauthorized
            }

            /**
             * FASE 3: VERIFICA PASSWORD
             * L'utente esiste, ora verifica la password
             */
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                // Password errata
                return response()->json([
                    'success' => false,
                    'message' => 'Password errata'
                ], 401); // 401 Unauthorized
            }

            /**
             * FASE 4: GENERAZIONE REFRESH TOKEN
             */
            $user = Auth::user();
            
            $refreshToken = RefreshToken::generateForUser(
                $user->id,
                $request->ip(),
                $request->userAgent()
            );

            /**
             * FASE 5: RISPOSTA SUCCESSO
             * Ritorna token e dati utente
             */
            return response()->json([
                'success' => true,
                'message' => 'Login effettuato con successo',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                    ],
                    'access_token' => $token,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ], 200); // 200 OK

        } catch (JWTException $e) {
            /**
             * ERRORE GENERAZIONE TOKEN
             */
            \Log::error('Errore JWT login: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Impossibile creare il token'
            ], 500);
        }
    }

    /**
     * ============================================
     * LOGOUT UTENTE
     * ============================================
     * Endpoint: POST /api/auth/logout
     * Invalida il token corrente e revoca i refresh token
     * 
     * Richiede: Header Authorization: Bearer {token}
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            /**
             * FASE 1: INVALIDA ACCESS TOKEN
             * Blacklist del token JWT corrente
             */
            JWTAuth::invalidate(JWTAuth::getToken());

            /**
             * FASE 2: REVOCA REFRESH TOKEN
             * Marca come revocato il refresh token fornito
             */
            if ($request->has('refresh_token')) {
                $refreshTokenValue = $request->input('refresh_token');
                $hashedToken = hash('sha256', $refreshTokenValue);
                
                $refreshToken = RefreshToken::where('token', $hashedToken)
                                             ->where('user_id', Auth::id())
                                             ->first();
                
                if ($refreshToken) {
                    $refreshToken->revoke();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout effettuato con successo'
            ], 200);

        } catch (JWTException $e) {
            \Log::error('Errore logout: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il logout'
            ], 500);
        }
    }

    /**
     * ============================================
     * REFRESH TOKEN
     * ============================================
     * Endpoint: POST /api/auth/refresh
     * Rinnova l'access token usando il refresh token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        /**
         * FASE 1: VALIDAZIONE REFRESH TOKEN
         */
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token mancante'
            ], 422);
        }

        try {
            /**
             * FASE 2: VERIFICA REFRESH TOKEN NEL DATABASE
             */
            $refreshTokenValue = $request->input('refresh_token');
            $hashedToken = hash('sha256', $refreshTokenValue);
            
            $refreshToken = RefreshToken::where('token', $hashedToken)
                                         ->valid() // Scope: non scaduto e non revocato
                                         ->first();

            if (!$refreshToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token non valido o scaduto'
                ], 401);
            }

            /**
             * FASE 3: GENERA NUOVO ACCESS TOKEN
             */
            $user = $refreshToken->user;
            $newAccessToken = JWTAuth::fromUser($user);

            /**
             * FASE 4: ROTAZIONE REFRESH TOKEN (SICUREZZA)
             * Revoca il vecchio e crea uno nuovo
             */
            $refreshToken->revoke();
            
            $newRefreshToken = RefreshToken::generateForUser(
                $user->id,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Token rinnovato con successo',
                'data' => [
                    'access_token' => $newAccessToken,
                    'refresh_token' => $newRefreshToken,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Errore refresh token: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il rinnovo del token'
            ], 500);
        }
    }

    /**
     * ============================================
     * PROFILO UTENTE CORRENTE
     * ============================================
     * Endpoint: GET /api/auth/me
     * Ritorna i dati dell'utente autenticato
     * 
     * Richiede: Header Authorization: Bearer {token}
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            /**
             * Ottiene l'utente autenticato dal token JWT
             */
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore recupero profilo'
            ], 500);
        }
    }

    /**
     * ============================================
     * CAMBIO PASSWORD
     * ============================================
     * Endpoint: POST /api/auth/change-password
     * Permette all'utente di cambiare la propria password
     * Revoca tutti i refresh token esistenti per sicurezza
     * 
     * Richiede: Header Authorization: Bearer {token}
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        /**
         * FASE 1: VALIDAZIONE INPUT
         */
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:current_password',
            'new_password_confirmation' => 'required|string|same:new_password',
        ], [
            'current_password.required' => 'Password attuale è obbligatoria',
            'new_password.required' => 'Nuova password è obbligatoria',
            'new_password.min' => 'Nuova password deve essere almeno 6 caratteri',
            'new_password.different' => 'La nuova password deve essere diversa da quella attuale',
            'new_password_confirmation.same' => 'Le password non coincidono',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errori di validazione',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /**
             * FASE 2: VERIFICA PASSWORD ATTUALE
             */
            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password attuale non corretta'
                ], 401);
            }

            /**
             * FASE 3: AGGIORNA PASSWORD
             */
            $user->password = Hash::make($request->new_password);
            $user->save();

            /**
             * FASE 4: REVOCA TUTTI I REFRESH TOKEN (SICUREZZA)
             * Forza logout da tutti i dispositivi
             */
            RefreshToken::revokeAllForUser($user->id);

            /**
             * FASE 5: INVALIDA TOKEN CORRENTE
             */
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Password cambiata con successo. Effettua nuovamente il login.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Errore cambio password: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il cambio password'
            ], 500);
        }
    }

    /**
     * ============================================
     * LOGOUT DA TUTTI I DISPOSITIVI
     * ============================================
     * Endpoint: POST /api/auth/logout-all
     * Revoca tutti i refresh token dell'utente
     * 
     * Richiede: Header Authorization: Bearer {token}
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll()
    {
        try {
            $user = Auth::user();

            /**
             * Revoca tutti i refresh token dell'utente
             */
            $revokedCount = RefreshToken::revokeAllForUser($user->id);

            /**
             * Invalida il token corrente
             */
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => "Logout effettuato da tutti i dispositivi ($revokedCount sessioni chiuse)"
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Errore logout all: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il logout'
            ], 500);
        }
    }
}
