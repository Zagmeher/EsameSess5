<?php

/**
 * ============================================
 * MODELLO ELOQUENT - UTENTE
 * ============================================
 * Questo modello rappresenta la tabella "users"
 * nel database. Gestisce l'autenticazione,
 * la serializzazione e il casting dei dati utente.
 * Estende Authenticatable per supportare il login.
 * Implementa JWTSubject per autenticazione JWT.
 * ============================================
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Decommentare per verifica email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Interfaccia per JWT

/**
 * Modello User - Rappresenta un utente del sistema
 * 
 * Estende Authenticatable di Laravel per fornire
 * funzionalità di autenticazione integrate.
 * Implementa JWTSubject per supporto token JWT.
 */
class User extends Authenticatable implements JWTSubject
{
    /** 
     * Traits utilizzati dal modello:
     * - HasFactory: Per creare factory di test
     * - Notifiable: Per inviare notifiche (email, SMS, etc.)
     */
    use HasFactory, Notifiable;

    /**
     * Attributi assegnabili in massa (Mass Assignment)
     * 
     * Definisce quali campi possono essere riempiti
     * tramite metodi come create() o fill().
     * Protezione contro Mass Assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',  // Nome utente univoco
        'email',     // Email univoca per autenticazione
        'password',  // Password hashata con bcrypt
        'comune_id', // ID del comune di residenza (foreign key)
    ];

    /**
     * Attributi nascosti durante la serializzazione
     * 
     * Questi campi non verranno inclusi quando
     * il modello viene convertito in array o JSON,
     * proteggendo dati sensibili.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',       // Password hashata (mai esposta in API)
        'remember_token', // Token per "ricordami" (sessioni persistenti)
    ];

    /**
     * Definisce il casting degli attributi
     * 
     * Converte automaticamente i tipi di dato
     * quando si accede agli attributi del modello.
     * 
     * @return array<string, string> Array associativo campo => tipo
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converte in oggetto Carbon (DateTime)
            'password' => 'hashed',            // Hashing automatico della password
        ];
    }

    /**
     * ============================================
     * METODI JWT - IMPLEMENTAZIONE JWTSubject
     * ============================================
     * Questi metodi sono richiesti dall'interfaccia
     * JWTSubject per il funzionamento dei token JWT
     */

    /**
     * Ottiene l'identificatore univoco per il JWT (primary key)
     * 
     * Questo valore sarà inserito nel payload del token JWT
     * come claim 'sub' (subject) e usato per identificare
     * l'utente quando il token viene decodificato.
     * 
     * @return mixed - Solitamente l'ID dell'utente
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Ritorna la chiave primaria (id)
    }

    /**
     * Definisce custom claims da aggiungere al payload JWT
     * 
     * Permette di inserire dati aggiuntivi nel token JWT
     * oltre ai claim standard (sub, exp, iat, ecc).
     * ATTENZIONE: Non inserire dati sensibili!
     * 
     * @return array<string, mixed> - Array di custom claims
     */
    public function getJWTCustomClaims()
    {
        return [
            'username' => $this->username, // Aggiungi username al token
            'email' => $this->email,       // Aggiungi email al token
            // Puoi aggiungere altri dati non sensibili
            // Es: 'role' => $this->role, 'premium' => $this->is_premium
        ];
    }

    /**
     * ============================================
     * RELAZIONI ELOQUENT
     * ============================================
     */

    /**
     * Relazione: Un User ha molti RefreshToken
     * 
     * Permette di accedere ai refresh token dell'utente:
     * $user->refreshTokens
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Relazione: Un User appartiene a un Comune
     * 
     * Permette di accedere al comune di residenza dell'utente:
     * $user->comune->nome
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comune()
    {
        return $this->belongsTo(Comune::class, 'comune_id');
    }
}
