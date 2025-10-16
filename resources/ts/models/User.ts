/**
 * ============================================
 * MODELLI TYPESCRIPT - INTERFACCE DATI
 * ============================================
 * Questo file definisce le interfacce TypeScript
 * per la tipizzazione forte dei dati utente e
 * delle risposte API.
 * ============================================
 */

/**
 * Interfaccia per i dati dell'utente
 * Rappresenta la struttura di un oggetto utente
 * usato nel form di registrazione
 */
export interface User {
    username: string;           // Nome utente (min 3 caratteri, alfanumerico)
    email: string;              // Indirizzo email (formato valido)
    password: string;           // Password (min 6 caratteri)
    confirmPassword?: string;   // Conferma password (opzionale, usato solo nel form)
}

/**
 * Interfaccia per la risposta API di Laravel
 * Definisce la struttura standard delle risposte
 * ricevute dagli endpoint backend
 */
export interface ApiResponse {
    success: boolean;           // Flag di successo (true/false)
    message: string;            // Messaggio di risposta per l'utente
    data?: any;                 // Dati opzionali (es. utente creato)
    errors?: {                  // Errori di validazione Laravel (opzionali)
        [key: string]: string[]; // Chiave campo -> array messaggi errore
    };
}
