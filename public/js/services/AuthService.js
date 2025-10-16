/**
 * ============================================
 * AUTH SERVICE - GESTIONE AUTENTICAZIONE JWT
 * ============================================
 * Servizio centralizzato per gestire tutte le operazioni
 * di autenticazione con JWT (JSON Web Token).
 *
 * Funzionalità:
 * - Login/Logout/Registrazione
 * - Auto-refresh del token scaduto
 * - Gestione storage sicuro dei token
 * - Recupero dati utente autenticato
 * - Cambio password con revoca sessioni
 *
 * SICUREZZA:
 * - Token rotation ad ogni refresh
 * - Gestione errori 401 (non autorizzato)
 * - Validazione risposta server
 * - Clear automatico in caso di errori critici
 * ============================================
 */
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { TokenService } from './TokenService';
/**
 * Classe AuthService
 *
 * Fornisce metodi statici per tutte le operazioni
 * di autenticazione. Non richiede istanziazione.
 */
export class AuthService {
    /**
     * Registra un nuovo utente
     *
     * @param {RegisterData} data - Dati registrazione (username, email, password)
     * @returns {Promise<AuthResponse>} - Dati utente e token JWT
     *
     * Endpoint: POST /api/auth/register
     *
     * Flusso:
     * 1. Invia dati al server
     * 2. Server valida e crea utente
     * 3. Server genera JWT e refresh token
     * 4. Client salva token in localStorage
     * 5. Ritorna dati utente
     *
     * @throws {Error} Se registrazione fallisce
     */
    static register(data) {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch(`${this.BASE_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            const result = yield response.json();
            // Controlla se registrazione è riuscita
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Registrazione fallita');
            }
            // Salva token nel localStorage
            this.saveTokens(result.data);
            return result.data;
        });
    }
    /**
     * Effettua login utente
     *
     * @param {LoginCredentials} credentials - Email e password
     * @returns {Promise<AuthResponse>} - Dati utente e token JWT
     *
     * Endpoint: POST /api/auth/login
     *
     * Flusso:
     * 1. Invia credenziali al server
     * 2. Server verifica credenziali (bcrypt)
     * 3. Server genera JWT e refresh token
     * 4. Client salva token in localStorage
     * 5. Ritorna dati utente
     *
     * @throws {Error} Se credenziali non valide
     */
    static login(credentials) {
        return __awaiter(this, void 0, void 0, function* () {
            const response = yield fetch(`${this.BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(credentials),
            });
            const result = yield response.json();
            // Controlla se login è riuscito
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Login fallito');
            }
            // Salva token nel localStorage
            this.saveTokens(result.data);
            return result.data;
        });
    }
    /**
     * Effettua logout utente
     *
     * @returns {Promise<void>}
     *
     * Endpoint: POST /api/auth/logout
     * Headers: Authorization: Bearer {access_token}
     *
     * Flusso:
     * 1. Recupera access token e refresh token
     * 2. Invia richiesta logout al server
     * 3. Server invalida access token
     * 4. Server revoca refresh token
     * 5. Client rimuove token da localStorage
     *
     * NOTA: Anche se la chiamata fallisce,
     * rimuove comunque i token locali per sicurezza.
     */
    static logout() {
        return __awaiter(this, void 0, void 0, function* () {
            const accessToken = TokenService.getAccessToken();
            const refreshToken = TokenService.getRefreshToken();
            try {
                if (accessToken) {
                    yield fetch(`${this.BASE_URL}/logout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${accessToken}`,
                        },
                        body: JSON.stringify({ refresh_token: refreshToken }),
                    });
                }
            }
            finally {
                // Rimuove SEMPRE i token locali, anche se logout fallisce
                TokenService.clearAllTokens();
            }
        });
    }
    /**
     * Rinnova l'access token usando il refresh token
     *
     * @returns {Promise<AuthResponse>} - Nuovi token (access + refresh)
     *
     * Endpoint: POST /api/auth/refresh
     *
     * Flusso:
     * 1. Recupera refresh token da localStorage
     * 2. Invia richiesta refresh al server
     * 3. Server valida refresh token (tabella DB)
     * 4. Server genera NUOVO access token e NUOVO refresh token
     * 5. Server REVOCA vecchio refresh token (token rotation)
     * 6. Client salva nuovi token
     *
     * TOKEN ROTATION: Il vecchio refresh token diventa
     * invalido dopo l'uso, per massima sicurezza.
     *
     * @throws {Error} Se refresh token non valido o revocato
     */
    static refreshToken() {
        return __awaiter(this, void 0, void 0, function* () {
            const refreshToken = TokenService.getRefreshToken();
            // Se non c'è refresh token, non può fare refresh
            if (!refreshToken) {
                throw new Error('Nessun refresh token disponibile');
            }
            const response = yield fetch(`${this.BASE_URL}/refresh`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ refresh_token: refreshToken }),
            });
            const result = yield response.json();
            // Controlla se refresh è riuscito
            if (!result.success || !result.data) {
                // Se refresh fallisce, rimuove tutti i token
                TokenService.clearAllTokens();
                throw new Error(result.message || 'Refresh token fallito');
            }
            // Salva i NUOVI token (il vecchio refresh è ora revocato)
            this.saveTokens(result.data);
            return result.data;
        });
    }
    /**
     * Recupera i dati dell'utente autenticato
     *
     * @returns {Promise<User>} - Dati utente (da JWT)
     *
     * Endpoint: GET /api/auth/me
     * Headers: Authorization: Bearer {access_token}
     *
     * Flusso:
     * 1. Recupera access token
     * 2. Invia richiesta al server con token
     * 3. Server decodifica JWT e recupera user_id
     * 4. Server ritorna dati utente dal DB
     *
     * @throws {Error} Se access token non valido o scaduto
     */
    static getAuthenticatedUser() {
        return __awaiter(this, void 0, void 0, function* () {
            const accessToken = TokenService.getAccessToken();
            // Se non c'è access token, non può recuperare utente
            if (!accessToken) {
                throw new Error('Utente non autenticato');
            }
            const response = yield fetch(`${this.BASE_URL}/me`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${accessToken}`,
                },
            });
            const result = yield response.json();
            // Controlla se richiesta è riuscita
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Impossibile recuperare dati utente');
            }
            return result.data;
        });
    }
    /**
     * Cambia la password dell'utente autenticato
     *
     * @param {ChangePasswordData} data - Password corrente, nuova e conferma
     * @returns {Promise<void>}
     *
     * Endpoint: POST /api/auth/change-password
     * Headers: Authorization: Bearer {access_token}
     *
     * Flusso:
     * 1. Invia password corrente e nuova al server
     * 2. Server verifica password corrente (bcrypt)
     * 3. Server aggiorna password (hash bcrypt)
     * 4. Server REVOCA TUTTI i refresh token utente
     * 5. Server invalida access token corrente
     * 6. Client rimuove token locali
     * 7. Client reindirizza a pagina login
     *
     * SICUREZZA MASSIMA: Cambiare password invalida
     * TUTTE le sessioni attive (multi-dispositivo).
     *
     * @throws {Error} Se password corrente errata o validazione fallisce
     */
    static changePassword(data) {
        return __awaiter(this, void 0, void 0, function* () {
            const accessToken = TokenService.getAccessToken();
            if (!accessToken) {
                throw new Error('Utente non autenticato');
            }
            const response = yield fetch(`${this.BASE_URL}/change-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${accessToken}`,
                },
                body: JSON.stringify(data),
            });
            const result = yield response.json();
            if (!result.success) {
                throw new Error(result.message || 'Cambio password fallito');
            }
            // Rimuove tutti i token locali (l'utente deve rifare login)
            TokenService.clearAllTokens();
        });
    }
    /**
     * Logout da tutti i dispositivi
     *
     * @returns {Promise<void>}
     *
     * Endpoint: POST /api/auth/logout-all
     * Headers: Authorization: Bearer {access_token}
     *
     * Flusso:
     * 1. Invia richiesta al server
     * 2. Server REVOCA TUTTI i refresh token dell'utente
     * 3. Client rimuove token locali
     * 4. Utente viene disconnesso da tutti i dispositivi
     *
     * Utile in caso di:
     * - Compromissione account
     * - Dispositivo perso/rubato
     * - Cambio credenziali importante
     *
     * @throws {Error} Se richiesta fallisce
     */
    static logoutAll() {
        return __awaiter(this, void 0, void 0, function* () {
            const accessToken = TokenService.getAccessToken();
            if (!accessToken) {
                throw new Error('Utente non autenticato');
            }
            const response = yield fetch(`${this.BASE_URL}/logout-all`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${accessToken}`,
                },
            });
            const result = yield response.json();
            if (!result.success) {
                throw new Error(result.message || 'Logout completo fallito');
            }
            // Rimuove token locali
            TokenService.clearAllTokens();
        });
    }
    /**
     * Verifica se l'utente è autenticato
     *
     * @returns {boolean} - true se ha un access token valido
     *
     * Controlla:
     * 1. Presenza access token in localStorage
     * 2. Token non scaduto (timestamp)
     *
     * NOTA: Non verifica validità lato server,
     * solo validità lato client (scadenza).
     */
    static isAuthenticated() {
        return TokenService.isAccessTokenValid();
    }
    /**
     * Verifica se può fare auto-refresh
     *
     * @returns {boolean} - true se ha un refresh token salvato
     *
     * Utile per decidere se tentare auto-refresh
     * quando l'access token scade.
     */
    static canRefresh() {
        return TokenService.hasRefreshToken();
    }
    /**
     * Metodo helper privato per salvare token
     *
     * @param {AuthResponse} authData - Dati autenticazione con token
     * @private
     *
     * Salva sia access token che refresh token
     * nel localStorage tramite TokenService.
     */
    static saveTokens(authData) {
        TokenService.saveAccessToken(authData.access_token, authData.expires_in);
        TokenService.saveRefreshToken(authData.refresh_token);
    }
    /**
     * Effettua chiamata HTTP protetta con auto-refresh
     *
     * @param {string} url - URL endpoint API
     * @param {RequestInit} options - Opzioni fetch (method, body, etc.)
     * @returns {Promise<Response>} - Risposta fetch
     *
     * Flusso con auto-refresh:
     * 1. Controlla se access token è valido
     * 2. Se scaduto, tenta refresh automatico
     * 3. Se refresh fallisce, lancia errore
     * 4. Aggiunge Authorization header
     * 5. Effettua chiamata
     * 6. Se risposta 401, tenta refresh e riprova
     *
     * IMPORTANTE: Usare questo metodo per TUTTE
     * le chiamate API protette.
     */
    static fetchWithAuth(url_1) {
        return __awaiter(this, arguments, void 0, function* (url, options = {}) {
            // Se token scaduto ma ha refresh token, tenta refresh
            if (!TokenService.isAccessTokenValid() && TokenService.hasRefreshToken()) {
                try {
                    yield this.refreshToken();
                }
                catch (error) {
                    // Refresh fallito, rimuove token e lancia errore
                    TokenService.clearAllTokens();
                    throw new Error('Sessione scaduta, effettua nuovamente il login');
                }
            }
            // Recupera access token
            const accessToken = TokenService.getAccessToken();
            if (!accessToken) {
                throw new Error('Utente non autenticato');
            }
            // Aggiunge Authorization header
            const headers = Object.assign(Object.assign({}, options.headers), { 'Authorization': `Bearer ${accessToken}`, 'Accept': 'application/json' });
            // Effettua chiamata
            const response = yield fetch(url, Object.assign(Object.assign({}, options), { headers }));
            // Se 401 Unauthorized, tenta refresh e riprova
            if (response.status === 401 && TokenService.hasRefreshToken()) {
                try {
                    yield this.refreshToken();
                    // Riprova chiamata con nuovo token
                    const newAccessToken = TokenService.getAccessToken();
                    const retryHeaders = Object.assign(Object.assign({}, options.headers), { 'Authorization': `Bearer ${newAccessToken}`, 'Accept': 'application/json' });
                    return yield fetch(url, Object.assign(Object.assign({}, options), { headers: retryHeaders }));
                }
                catch (error) {
                    // Refresh fallito, rimuove token
                    TokenService.clearAllTokens();
                    throw new Error('Sessione scaduta, effettua nuovamente il login');
                }
            }
            return response;
        });
    }
}
/**
 * URL base per le API di autenticazione
 *
 * NOTA: Tutte le rotte iniziano con /api/auth/
 */
AuthService.BASE_URL = '/api/auth';
//# sourceMappingURL=AuthService.js.map