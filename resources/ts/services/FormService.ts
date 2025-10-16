/**
 * ============================================
 * SERVIZI PER VALIDAZIONE FORM E CHIAMATE API
 * ============================================
 * Questo file contiene due servizi principali:
 * 1. ValidationService - Logica di validazione campi form
 * 2. ApiService - Gestione chiamate HTTP API con RxJS
 * ============================================
 */

import { Observable, fromEvent } from 'rxjs';
import { map, debounceTime, distinctUntilChanged } from 'rxjs/operators';
import type { User, ApiResponse } from '../models/User';

/**
 * Servizio di validazione per i campi del form
 * Contiene metodi statici per validare username, email, password
 */
export class ValidationService {
    
    /**
     * Valida il campo username
     * Requisiti: minimo 3 caratteri, solo alfanumerici e underscore
     * @param username - Username da validare
     * @returns Oggetto con flag valid e messaggio di errore
     */
    static validateUsername(username: string): { valid: boolean; message: string } {
        if (username.length < 3) {
            return { valid: false, message: 'Username deve essere almeno 3 caratteri' };
        }
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            return { valid: false, message: 'Username può contenere solo lettere, numeri e underscore' };
        }
        return { valid: true, message: '' };
    }

    /**
     * Valida il campo email
     * Verifica formato email valido con regex
     * @param email - Email da validare
     * @returns Oggetto con flag valid e messaggio di errore
     */
    static validateEmail(email: string): { valid: boolean; message: string } {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Pattern base per email
        if (!emailRegex.test(email)) {
            return { valid: false, message: 'Email non valida' };
        }
        return { valid: true, message: '' };
    }

    /**
     * Valida il campo password
     * Requisiti: minimo 6 caratteri
     * @param password - Password da validare
     * @returns Oggetto con flag valid e messaggio di errore
     */
    static validatePassword(password: string): { valid: boolean; message: string } {
        if (password.length < 6) {
            return { valid: false, message: 'Password deve essere almeno 6 caratteri' };
        }
        return { valid: true, message: '' };
    }

    /**
     * Valida conferma password
     * Verifica che coincida con la password principale
     * @param password - Password originale
     * @param confirmPassword - Password di conferma
     * @returns Oggetto con flag valid e messaggio di errore
     */
    static validateConfirmPassword(password: string, confirmPassword: string): { valid: boolean; message: string } {
        if (password !== confirmPassword) {
            return { valid: false, message: 'Le password non coincidono' };
        }
        return { valid: true, message: '' };
    }
}

/**
 * Servizio per gestire le chiamate API
 * Utilizza RxJS Observable per operazioni asincrone
 */
export class ApiService {
    private apiUrl = '/api/register'; // Endpoint Laravel per registrazione
    private csrfToken: string;         // Token CSRF per protezione Laravel

    /**
     * Costruttore - recupera il token CSRF dal meta tag
     */
    constructor() {
        // Ottieni il token CSRF da Laravel (inserito nel meta tag)
        const token = document.querySelector('meta[name="csrf-token"]');
        this.csrfToken = token ? token.getAttribute('content') || '' : '';
    }

    /**
     * Registra un nuovo utente chiamando l'API Laravel
     * @param user - Oggetto utente con dati da inviare
     * @returns Observable che emette la risposta API
     */
    registerUser(user: User): Observable<ApiResponse> {
        return new Observable(observer => {
            // Fetch API per chiamata HTTP POST
            fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',  // Dati in formato JSON
                    'X-CSRF-TOKEN': this.csrfToken,      // Token sicurezza Laravel
                    'Accept': 'application/json'         // Richiedi risposta JSON
                },
                body: JSON.stringify(user) // Converti oggetto user in JSON
            })
            .then(response => response.json()) // Parsifica risposta JSON
            .then((data: ApiResponse) => {
                observer.next(data);   // Emetti dati all'observer
                observer.complete();   // Completa l'observable
            })
            .catch(error => {
                observer.error(error); // Gestisci errori di rete
            });
        });
    }

    /**
     * Configura validazione real-time su un campo input
     * Utilizza RxJS operators per debouncing e distinct
     * @param inputElement - Elemento input HTML da monitorare
     * @param validationFn - Funzione di validazione da applicare
     */
    setupRealtimeValidation(inputElement: HTMLInputElement, validationFn: (value: string) => { valid: boolean; message: string }): void {
        fromEvent(inputElement, 'input') // Ascolta eventi input
            .pipe(
                map((event: Event) => (event.target as HTMLInputElement).value), // Estrai valore
                debounceTime(300),         // Attendi 300ms dopo ultima digitazione
                distinctUntilChanged()     // Emetti solo se valore cambia
            )
            .subscribe(value => {
                const result = validationFn(value); // Esegui validazione
                const feedbackElement = inputElement.nextElementSibling as HTMLElement; // Elemento per messaggio
                
                // Applica stili Bootstrap in base al risultato
                if (!result.valid && value.length > 0) {
                    inputElement.classList.add('is-invalid');    // Bordo rosso
                    inputElement.classList.remove('is-valid');
                    if (feedbackElement) {
                        feedbackElement.textContent = result.message; // Mostra errore
                    }
                } else if (result.valid && value.length > 0) {
                    inputElement.classList.remove('is-invalid');
                    inputElement.classList.add('is-valid');      // Bordo verde
                } else {
                    // Rimuovi tutti gli stili se campo vuoto
                    inputElement.classList.remove('is-invalid', 'is-valid');
                }
            });
    }
}
