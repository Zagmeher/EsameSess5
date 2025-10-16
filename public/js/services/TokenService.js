/**
 * ============================================
 * TOKEN SERVICE - GESTIONE STORAGE JWT
 * ============================================
 * Servizio per gestire il salvataggio sicuro e il recupero
 * dei token JWT (access_token e refresh_token) nel browser.
 *
 * Utilizza localStorage per persistenza tra sessioni.
 *
 * SICUREZZA:
 * - I token non contengono dati sensibili (password)
 * - Access token ha durata breve (1 ora)
 * - Refresh token viene ruotato ad ogni rinnovo
 * - Supporto per clear totale in caso di logout
 * ============================================
 */
/**
 * Chiavi usate per salvare i token nel localStorage
 *
 * NOTA: Usare prefisso 'app_' per evitare conflitti
 * con altri script o estensioni browser
 */
const ACCESS_TOKEN_KEY = 'app_access_token';
const REFRESH_TOKEN_KEY = 'app_refresh_token';
const TOKEN_EXPIRY_KEY = 'app_token_expiry';
/**
 * Classe TokenService
 *
 * Fornisce metodi per:
 * - Salvare token JWT nel localStorage
 * - Recuperare token JWT dal localStorage
 * - Verificare validità token (scadenza)
 * - Rimuovere token (logout)
 */
export class TokenService {
    /**
     * Salva l'access token nel localStorage
     *
     * @param {string} token - Access token JWT ricevuto dal server
     * @param {number} expiresIn - Durata validità in secondi (es: 3600 = 1 ora)
     *
     * Calcola e salva anche il timestamp di scadenza
     * per verificare se il token è ancora valido.
     */
    static saveAccessToken(token, expiresIn) {
        localStorage.setItem(ACCESS_TOKEN_KEY, token);
        // Calcola timestamp di scadenza (ora + expiresIn secondi)
        const expiryTime = Date.now() + (expiresIn * 1000);
        localStorage.setItem(TOKEN_EXPIRY_KEY, expiryTime.toString());
    }
    /**
     * Recupera l'access token dal localStorage
     *
     * @returns {string | null} - Access token o null se non presente
     *
     * NOTA: Non verifica la validità del token,
     * usa isAccessTokenValid() per controllarla.
     */
    static getAccessToken() {
        return localStorage.getItem(ACCESS_TOKEN_KEY);
    }
    /**
     * Salva il refresh token nel localStorage
     *
     * @param {string} token - Refresh token ricevuto dal server
     *
     * Il refresh token non ha scadenza lato client,
     * la validità è gestita dal server (tabella refresh_tokens).
     */
    static saveRefreshToken(token) {
        localStorage.setItem(REFRESH_TOKEN_KEY, token);
    }
    /**
     * Recupera il refresh token dal localStorage
     *
     * @returns {string | null} - Refresh token o null se non presente
     */
    static getRefreshToken() {
        return localStorage.getItem(REFRESH_TOKEN_KEY);
    }
    /**
     * Verifica se l'access token è ancora valido
     *
     * @returns {boolean} - true se valido, false se scaduto o assente
     *
     * Controlla:
     * 1. Presenza del token
     * 2. Presenza del timestamp di scadenza
     * 3. Timestamp corrente < timestamp di scadenza
     *
     * MARGINE SICUREZZA: Considera scaduto se mancano meno di 60 secondi,
     * per dare tempo al refresh prima della scadenza effettiva.
     */
    static isAccessTokenValid() {
        const token = this.getAccessToken();
        const expiryTime = localStorage.getItem(TOKEN_EXPIRY_KEY);
        // Se manca token o scadenza, non è valido
        if (!token || !expiryTime) {
            return false;
        }
        // Controlla se il timestamp di scadenza è futuro
        // Margine di 60 secondi per sicurezza
        const now = Date.now();
        const expiry = parseInt(expiryTime, 10);
        return now < (expiry - 60000); // 60000ms = 60 secondi
    }
    /**
     * Verifica se esiste un refresh token salvato
     *
     * @returns {boolean} - true se esiste, false altrimenti
     *
     * Utile per capire se l'utente può fare auto-refresh
     * quando l'access token scade.
     */
    static hasRefreshToken() {
        return this.getRefreshToken() !== null;
    }
    /**
     * Rimuove l'access token dal localStorage
     *
     * Usato quando:
     * - L'utente fa logout
     * - Il token è scaduto e il refresh fallisce
     */
    static removeAccessToken() {
        localStorage.removeItem(ACCESS_TOKEN_KEY);
        localStorage.removeItem(TOKEN_EXPIRY_KEY);
    }
    /**
     * Rimuove il refresh token dal localStorage
     *
     * Usato quando:
     * - L'utente fa logout
     * - Il refresh token è revocato/invalido
     */
    static removeRefreshToken() {
        localStorage.removeItem(REFRESH_TOKEN_KEY);
    }
    /**
     * Rimuove TUTTI i token dal localStorage
     *
     * Usato per logout completo o in caso di errori critici.
     *
     * IMPORTANTE: Chiamare questo metodo prima di
     * reindirizzare alla pagina di login.
     */
    static clearAllTokens() {
        this.removeAccessToken();
        this.removeRefreshToken();
    }
    /**
     * Ottiene il timestamp di scadenza del token
     *
     * @returns {number | null} - Timestamp di scadenza o null
     *
     * Utile per mostrare countdown di scadenza nell'UI
     * o per calcolare quando fare il prossimo refresh.
     */
    static getTokenExpiry() {
        const expiryTime = localStorage.getItem(TOKEN_EXPIRY_KEY);
        return expiryTime ? parseInt(expiryTime, 10) : null;
    }
    /**
     * Calcola i secondi rimanenti prima della scadenza
     *
     * @returns {number} - Secondi rimanenti (0 se scaduto o non presente)
     *
     * Esempio uso: mostrare "Token valido per 45 minuti"
     */
    static getSecondsUntilExpiry() {
        const expiryTime = this.getTokenExpiry();
        if (!expiryTime) {
            return 0;
        }
        const now = Date.now();
        const remaining = Math.max(0, Math.floor((expiryTime - now) / 1000));
        return remaining;
    }
}
//# sourceMappingURL=TokenService.js.map