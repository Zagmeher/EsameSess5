/**
 * ============================================
 * FILE PRINCIPALE APPLICAZIONE TYPESCRIPT
 * ============================================
 * Questo file gestisce l'inizializzazione dell'applicazione
 * e la logica di validazione del form di registrazione.
 * Utilizza RxJS per la programmazione reattiva e la gestione eventi.
 * ============================================
 */
import { fromEvent } from 'rxjs';
import { ApiService, ValidationService } from './services/FormService';
/**
 * Classe principale dell'applicazione
 * Gestisce l'inizializzazione e la logica del form di registrazione
 */
class App {
    /**
     * Costruttore - inizializza l'applicazione
     */
    constructor() {
        this.apiService = new ApiService();
        // Recupera il form di registrazione dal DOM
        this.form = document.getElementById('registrationForm');
        this.init(); // Avvia l'inizializzazione
    }
    /**
     * Metodo di inizializzazione
     * Configura la validazione real-time per tutti i campi del form
     * e imposta il gestore per il submit
     */
    init() {
        // Recupera i riferimenti agli input del form
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        // Setup validazione real-time per ogni campo utilizzando RxJS
        this.apiService.setupRealtimeValidation(usernameInput, ValidationService.validateUsername);
        this.apiService.setupRealtimeValidation(emailInput, ValidationService.validateEmail);
        this.apiService.setupRealtimeValidation(passwordInput, ValidationService.validatePassword);
        // Gestione validazione conferma password
        // Utilizza RxJS fromEvent per ascoltare l'evento input
        fromEvent(confirmPasswordInput, 'input').subscribe(() => {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            // Valida che le due password coincidano
            const result = ValidationService.validateConfirmPassword(password, confirmPassword);
            const feedbackElement = confirmPasswordInput.nextElementSibling;
            // Applica classi Bootstrap per feedback visivo
            if (!result.valid && confirmPassword.length > 0) {
                confirmPasswordInput.classList.add('is-invalid'); // Bordo rosso
                confirmPasswordInput.classList.remove('is-valid');
                if (feedbackElement) {
                    feedbackElement.textContent = result.message; // Mostra messaggio errore
                }
            }
            else if (result.valid && confirmPassword.length > 0) {
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid'); // Bordo verde
            }
        });
        // Gestione evento submit del form con RxJS
        fromEvent(this.form, 'submit').subscribe((event) => {
            event.preventDefault(); // Previene il submit standard HTML
            this.handleSubmit(); // Chiama il metodo personalizzato di gestione
        });
    }
    handleSubmit() {
        const formData = new FormData(this.form);
        const user = {
            username: formData.get('username'),
            email: formData.get('email'),
            password: formData.get('password'),
            confirmPassword: formData.get('confirmPassword')
        };
        // Validazione finale
        if (!this.validateForm(user)) {
            return;
        }
        // Rimuovi confirmPassword prima di inviare al server
        delete user.confirmPassword;
        // Mostra stato di caricamento sul pulsante
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent; // Salva testo originale
        submitBtn.disabled = true; // Disabilita il pulsante
        // Mostra spinner di caricamento Bootstrap
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrazione...';
        // Invia la richiesta di registrazione all'API tramite Observable RxJS
        this.apiService.registerUser(user).subscribe({
            // Callback eseguito quando la richiesta ha successo
            next: (response) => {
                submitBtn.disabled = false; // Riabilita pulsante
                submitBtn.textContent = originalText || 'Registrati'; // Ripristina testo
                if (response.success) {
                    // Mostra messaggio di successo
                    this.showAlert('success', response.message);
                    this.form.reset(); // Pulisce i campi del form
                    // Rimuovi tutte le classi di validazione Bootstrap
                    this.form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                        el.classList.remove('is-valid', 'is-invalid');
                    });
                }
                else {
                    // Mostra messaggio di errore
                    this.showAlert('danger', response.message);
                    // Mostra errori di validazione specifici dal backend Laravel
                    if (response.errors) {
                        this.displayValidationErrors(response.errors);
                    }
                }
            },
            // Callback eseguito in caso di errore di rete o server
            error: (error) => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText || 'Registrati';
                console.error('Errore:', error); // Log errore per debug
                this.showAlert('danger', 'Errore durante la registrazione. Riprova più tardi.');
            }
        });
    }
    /**
     * Metodo per validare tutti i campi del form prima dell'invio
     * @param user - Oggetto utente da validare
     * @returns true se tutti i campi sono validi, false altrimenti
     */
    validateForm(user) {
        let isValid = true; // Flag per tracciare validità complessiva
        // Valida username usando ValidationService
        const usernameResult = ValidationService.validateUsername(user.username);
        if (!usernameResult.valid) {
            this.showFieldError('username', usernameResult.message);
            isValid = false;
        }
        // Valida email usando ValidationService
        const emailResult = ValidationService.validateEmail(user.email);
        if (!emailResult.valid) {
            this.showFieldError('email', emailResult.message);
            isValid = false;
        }
        // Valida password usando ValidationService
        const passwordResult = ValidationService.validatePassword(user.password);
        if (!passwordResult.valid) {
            this.showFieldError('password', passwordResult.message);
            isValid = false;
        }
        // Valida conferma password
        if (user.confirmPassword) {
            // Valida conferma password (deve coincidere con password)
            const confirmResult = ValidationService.validateConfirmPassword(user.password, user.confirmPassword);
            if (!confirmResult.valid) {
                this.showFieldError('confirmPassword', confirmResult.message);
                isValid = false;
            }
        }
        return isValid; // Ritorna true solo se tutti i campi sono validi
    }
    /**
     * Mostra un messaggio di errore su un campo specifico
     * @param fieldId - ID dell'elemento input
     * @param message - Messaggio di errore da visualizzare
     */
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const feedbackElement = field.nextElementSibling; // Elemento per messaggio errore
        field.classList.add('is-invalid'); // Applica stile Bootstrap errore
        if (feedbackElement) {
            feedbackElement.textContent = message; // Mostra messaggio
        }
    }
    /**
     * Visualizza errori di validazione ricevuti dal backend Laravel
     * @param errors - Oggetto con errori per campo (formato Laravel)
     */
    displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const fieldElement = document.getElementById(field);
            if (fieldElement) {
                const feedbackElement = fieldElement.nextElementSibling;
                fieldElement.classList.add('is-invalid'); // Bordo rosso
                if (feedbackElement) {
                    // Mostra il primo errore per il campo
                    feedbackElement.textContent = errors[field][0];
                }
            }
        });
    }
    /**
     * Mostra un alert Bootstrap nella pagina
     * @param type - Tipo di alert ('success', 'danger', 'warning', 'info')
     * @param message - Messaggio da visualizzare
     */
    showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer)
            return; // Esci se container non esiste
        // Crea HTML dell'alert con classi Bootstrap
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        // Auto-dismiss: rimuove automaticamente l'alert dopo 5 secondi
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show'); // Animazione fade out
                setTimeout(() => alertContainer.innerHTML = '', 150); // Rimuove dal DOM
            }
        }, 5000); // 5000 millisecondi = 5 secondi
    }
}
/**
 * ============================================
 * INIZIALIZZAZIONE APPLICAZIONE
 * ============================================
 * Attende che il DOM sia completamente caricato
 * prima di istanziare la classe App
 */
document.addEventListener('DOMContentLoaded', () => {
    new App(); // Crea nuova istanza dell'applicazione
});
//# sourceMappingURL=app.js.map