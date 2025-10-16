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

import { TokenService } from './TokenService';
import { User } from '../models/User';

/**
 * Interfaccia per risposta API standard
 * 
 * Tutte le API ritornano questo formato:
 * {
 *   success: boolean,
 *   message: string,
 *   data?: object,
 *   errors?: object
 * }
 */
interface ApiResponse<T = any> {
    success: boolean;
    message: string;
    data?: T;
    errors?: Record<string, string[]>;
}

/**
 * Interfaccia per dati login
 */
interface LoginCredentials {
    email: string;
    password: string;
}

/**
 * Interfaccia per dati registrazione
 */
interface RegisterData {
    username: string;
    email: string;
    password: string;
}

/**
 * Interfaccia per risposta autenticazione
 * (login, register, refresh)
 */
interface AuthResponse {
    user: User;
    access_token: string;
    refresh_token: string;
    token_type: string;
    expires_in: number;
}

/**
 * Interfaccia per dati cambio password
 */
interface ChangePasswordData {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
}

/**
 * Classe AuthService
 * 
 * Fornisce metodi statici per tutte le operazioni
 * di autenticazione. Non richiede istanziazione.
 */
export class AuthService {
    
    /**
     * URL base per le API di autenticazione
     * 
     * NOTA: Tutte le rotte iniziano con /api/auth/
     */
    private static readonly BASE_URL = '/api/auth';

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
    static async register(data: RegisterData): Promise<AuthResponse> {
        const response = await fetch(`${this.BASE_URL}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result: ApiResponse<AuthResponse> = await response.json();

        // Controlla se registrazione è riuscita
        if (!result.success || !result.data) {
            throw new Error(result.message || 'Registrazione fallita');
        }

        // Salva token nel localStorage
        this.saveTokens(result.data);

        return result.data;
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
    static async login(credentials: LoginCredentials): Promise<AuthResponse> {
        const response = await fetch(`${this.BASE_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(credentials),
        });

        const result: ApiResponse<AuthResponse> = await response.json();

        // Controlla se login è riuscito
        if (!result.success || !result.data) {
            throw new Error(result.message || 'Login fallito');
        }

        // Salva token nel localStorage
        this.saveTokens(result.data);

        return result.data;
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
    static async logout(): Promise<void> {
        const accessToken = TokenService.getAccessToken();
        const refreshToken = TokenService.getRefreshToken();

        try {
            if (accessToken) {
                await fetch(`${this.BASE_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${accessToken}`,
                    },
                    body: JSON.stringify({ refresh_token: refreshToken }),
                });
            }
        } finally {
            // Rimuove SEMPRE i token locali, anche se logout fallisce
            TokenService.clearAllTokens();
        }
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
    static async refreshToken(): Promise<AuthResponse> {
        const refreshToken = TokenService.getRefreshToken();

        // Se non c'è refresh token, non può fare refresh
        if (!refreshToken) {
            throw new Error('Nessun refresh token disponibile');
        }

        const response = await fetch(`${this.BASE_URL}/refresh`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ refresh_token: refreshToken }),
        });

        const result: ApiResponse<AuthResponse> = await response.json();

        // Controlla se refresh è riuscito
        if (!result.success || !result.data) {
            // Se refresh fallisce, rimuove tutti i token
            TokenService.clearAllTokens();
            throw new Error(result.message || 'Refresh token fallito');
        }

        // Salva i NUOVI token (il vecchio refresh è ora revocato)
        this.saveTokens(result.data);

        return result.data;
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
    static async getAuthenticatedUser(): Promise<User> {
        const accessToken = TokenService.getAccessToken();

        // Se non c'è access token, non può recuperare utente
        if (!accessToken) {
            throw new Error('Utente non autenticato');
        }

        const response = await fetch(`${this.BASE_URL}/me`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
            },
        });

        const result: ApiResponse<User> = await response.json();

        // Controlla se richiesta è riuscita
        if (!result.success || !result.data) {
            throw new Error(result.message || 'Impossibile recuperare dati utente');
        }

        return result.data;
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
    static async changePassword(data: ChangePasswordData): Promise<void> {
        const accessToken = TokenService.getAccessToken();

        if (!accessToken) {
            throw new Error('Utente non autenticato');
        }

        const response = await fetch(`${this.BASE_URL}/change-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
            },
            body: JSON.stringify(data),
        });

        const result: ApiResponse = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Cambio password fallito');
        }

        // Rimuove tutti i token locali (l'utente deve rifare login)
        TokenService.clearAllTokens();
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
    static async logoutAll(): Promise<void> {
        const accessToken = TokenService.getAccessToken();

        if (!accessToken) {
            throw new Error('Utente non autenticato');
        }

        const response = await fetch(`${this.BASE_URL}/logout-all`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
            },
        });

        const result: ApiResponse = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Logout completo fallito');
        }

        // Rimuove token locali
        TokenService.clearAllTokens();
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
    static isAuthenticated(): boolean {
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
    static canRefresh(): boolean {
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
    private static saveTokens(authData: AuthResponse): void {
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
    static async fetchWithAuth(url: string, options: RequestInit = {}): Promise<Response> {
        // Se token scaduto ma ha refresh token, tenta refresh
        if (!TokenService.isAccessTokenValid() && TokenService.hasRefreshToken()) {
            try {
                await this.refreshToken();
            } catch (error) {
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
        const headers = {
            ...options.headers,
            'Authorization': `Bearer ${accessToken}`,
            'Accept': 'application/json',
        };

        // Effettua chiamata
        const response = await fetch(url, { ...options, headers });

        // Se 401 Unauthorized, tenta refresh e riprova
        if (response.status === 401 && TokenService.hasRefreshToken()) {
            try {
                await this.refreshToken();
                
                // Riprova chiamata con nuovo token
                const newAccessToken = TokenService.getAccessToken();
                const retryHeaders = {
                    ...options.headers,
                    'Authorization': `Bearer ${newAccessToken}`,
                    'Accept': 'application/json',
                };
                
                return await fetch(url, { ...options, headers: retryHeaders });
            } catch (error) {
                // Refresh fallito, rimuove token
                TokenService.clearAllTokens();
                throw new Error('Sessione scaduta, effettua nuovamente il login');
            }
        }

        return response;
    }
}
