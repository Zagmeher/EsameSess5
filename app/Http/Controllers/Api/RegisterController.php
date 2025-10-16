<?php

/**
 * ============================================
 * CONTROLLER API REGISTRAZIONE UTENTI
 * ============================================
 * Questo controller gestisce la registrazione
 * dei nuovi utenti tramite API REST.
 * Valida i dati, salva l'utente nel database
 * e ritorna risposte JSON.
 * ============================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Controller per la gestione della registrazione utenti
 */
class RegisterController extends Controller
{
    /**
     * Metodo per registrare un nuovo utente
     * 
     * Riceve i dati dal form di registrazione (username, email, password),
     * li valida, crea il nuovo utente nel database e ritorna una risposta JSON.
     * 
     * @param Request $request - Oggetto richiesta HTTP contenente i dati del form
     * @return \Illuminate\Http\JsonResponse - Risposta JSON con esito operazione
     */
    public function register(Request $request)
    {
        /**
         * FASE 1: VALIDAZIONE INPUT
         * Verifica che i dati ricevuti rispettino le regole definite
         */
        $validator = Validator::make($request->all(), [
            // Username: obbligatorio, stringa, 3-50 caratteri, unico, solo alfanumerici e underscore
            'username' => 'required|string|min:3|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            // Email: obbligatoria, formato email valido, massimo 100 caratteri, unica nel database
            'email' => 'required|string|email|max:100|unique:users,email',
            // Password: obbligatoria, minimo 6 caratteri
            'password' => 'required|string|min:6',
        ], [
            // Messaggi di errore personalizzati in italiano
            'username.required' => 'Username è obbligatorio',
            'username.min' => 'Username deve essere almeno 3 caratteri',
            'username.unique' => 'Username già esistente',
            'username.regex' => 'Username può contenere solo lettere, numeri e underscore',
            'email.required' => 'Email è obbligatoria',
            'email.email' => 'Email non valida',
            'email.unique' => 'Email già registrata',
            'password.required' => 'Password è obbligatoria',
            'password.min' => 'Password deve essere almeno 6 caratteri',
        ]);

        /**
         * Se la validazione fallisce, ritorna errori con status 422 (Unprocessable Entity)
         */
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errori di validazione',
                'errors' => $validator->errors() // Array di errori per campo
            ], 422);
        }

        /**
         * FASE 2: CREAZIONE UTENTE
         * Tenta di salvare il nuovo utente nel database
         */
        try {
            // Crea nuovo record nella tabella users
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                // Hash della password per sicurezza (bcrypt)
                'password' => Hash::make($request->password),
            ]);

            /**
             * Registrazione completata con successo
             * Ritorna i dati dell'utente creato con status 201 (Created)
             */
            return response()->json([
                'success' => true,
                'message' => 'Registrazione completata con successo!',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]
            ], 201);

        } catch (\Exception $e) {
            /**
             * GESTIONE ERRORI
             * In caso di errore imprevisto (es. problema database),
             * ritorna errore generico con status 500 (Internal Server Error)
             */
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la registrazione'
            ], 500);
        }
    }
}

