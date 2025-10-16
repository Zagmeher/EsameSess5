<?php

/**
 * ============================================
 * MIGRATION - TABELLA REFRESH TOKENS
 * ============================================
 * Questa migration crea la tabella per gestire
 * i refresh token JWT con rotazione sicura.
 * Permette di invalidare token compromessi e
 * tracciare l'accesso degli utenti.
 * ============================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegue la migration - crea la tabella refresh_tokens
     * 
     * Struttura tabella:
     * - id: Identificativo univoco
     * - user_id: Riferimento all'utente (foreign key)
     * - token: Hash del refresh token
     * - expires_at: Data scadenza token
     * - revoked: Flag per invalidazione manuale
     * - ip_address: IP del client per sicurezza
     * - user_agent: Browser/device info
     * - timestamps: created_at, updated_at
     */
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            // Chiave primaria auto-incrementale
            $table->id();
            
            // Riferimento all'utente - chiave esterna verso tabella users
            // Uso unsignedBigInteger per compatibilità con id bigint
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade'); // Cancella token se utente eliminato
            
            // Token hashato (non salvare mai token in chiaro!)
            $table->string('token', 500)->unique();
            
            // Data e ora di scadenza del refresh token
            $table->timestamp('expires_at');
            
            // Flag per revocare manualmente il token (logout, cambio password)
            $table->boolean('revoked')->default(false);
            
            // Informazioni di sicurezza sul client
            $table->ipAddress('ip_address')->nullable(); // IP del client
            $table->text('user_agent')->nullable();      // Browser/device info
            
            // Timestamp automatici (created_at, updated_at)
            $table->timestamps();
            
            // Indici per performance query
            $table->index('user_id');           // Ricerca veloce per utente
            $table->index('token');             // Ricerca veloce per token
            $table->index(['user_id', 'revoked']); // Query utente + stato
        });
    }

    /**
     * Rollback della migration - elimina la tabella
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
