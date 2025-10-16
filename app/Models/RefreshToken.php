<?php

/**
 * ============================================
 * MODELLO ELOQUENT - REFRESH TOKEN
 * ============================================
 * Questo modello gestisce i refresh token JWT.
 * Permette la rotazione sicura dei token e
 * il tracking delle sessioni utente.
 * ============================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Modello RefreshToken
 * Gestisce i token di refresh per l'autenticazione JWT
 */
class RefreshToken extends Model
{
    use HasFactory;

    /**
     * Nome della tabella nel database
     * 
     * @var string
     */
    protected $table = 'refresh_tokens';

    /**
     * Attributi assegnabili in massa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'revoked',
        'ip_address',
        'user_agent',
    ];

    /**
     * Cast degli attributi ai tipi corretti
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime', // Converte in Carbon instance
        'revoked' => 'boolean',     // Cast a booleano
    ];

    /**
     * ============================================
     * RELAZIONI ELOQUENT
     * ============================================
     */

    /**
     * Relazione: Refresh Token appartiene a un User
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ============================================
     * METODI STATICI PER GESTIONE TOKEN
     * ============================================
     */

    /**
     * Genera un nuovo refresh token per l'utente
     * 
     * @param int $userId - ID dell'utente
     * @param string|null $ipAddress - IP del client
     * @param string|null $userAgent - User agent del browser
     * @param int $expirationDays - Giorni prima della scadenza (default: 30)
     * @return string - Token in chiaro (da inviare al client)
     */
    public static function generateForUser(
        int $userId, 
        ?string $ipAddress = null, 
        ?string $userAgent = null,
        int $expirationDays = 30
    ): string {
        // Genera token casuale sicuro (64 caratteri)
        $token = Str::random(64);
        
        // Crea e salva il refresh token (hashed)
        self::create([
            'user_id' => $userId,
            'token' => hash('sha256', $token), // Hash SHA-256 per sicurezza
            'expires_at' => Carbon::now()->addDays($expirationDays),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'revoked' => false,
        ]);
        
        // Ritorna il token in chiaro (solo ora, poi sarà perso)
        return $token;
    }

    /**
     * Verifica se il token è valido (non scaduto e non revocato)
     * 
     * @return bool - true se valido, false altrimenti
     */
    public function isValid(): bool
    {
        return !$this->revoked && $this->expires_at->isFuture();
    }

    /**
     * Revoca il refresh token (logout o compromissione)
     * 
     * @return bool - Risultato dell'operazione
     */
    public function revoke(): bool
    {
        $this->revoked = true;
        return $this->save();
    }

    /**
     * ============================================
     * SCOPE QUERY (FILTRI PREDEFINITI)
     * ============================================
     */

    /**
     * Scope: Filtra solo token validi (non scaduti, non revocati)
     * 
     * Uso: RefreshToken::valid()->get()
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query->where('revoked', false)
                     ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope: Filtra token scaduti
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * ============================================
     * METODI DI PULIZIA
     * ============================================
     */

    /**
     * Elimina tutti i token scaduti (pulizia database)
     * Da eseguire periodicamente con scheduled task
     * 
     * @return int - Numero di token eliminati
     */
    public static function purgeExpired(): int
    {
        return self::expired()->delete();
    }

    /**
     * Revoca tutti i token di un utente (cambio password, logout da tutti i device)
     * 
     * @param int $userId - ID dell'utente
     * @return int - Numero di token revocati
     */
    public static function revokeAllForUser(int $userId): int
    {
        return self::where('user_id', $userId)->update(['revoked' => true]);
    }
}
