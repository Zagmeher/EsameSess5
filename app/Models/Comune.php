<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comune extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comuni';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'regione',
        'provincia',
        'sigla_provincia',
        'codice_catastale',
        'cap',
    ];

    /**
     * Scope per cercare comuni per nome
     */
    public function scopeByNome($query, $nome)
    {
        return $query->where('nome', 'like', "%{$nome}%");
    }

    /**
     * Scope per filtrare per regione
     */
    public function scopeByRegione($query, $regione)
    {
        return $query->where('regione', $regione);
    }

    /**
     * Scope per filtrare per provincia
     */
    public function scopeByProvincia($query, $provincia)
    {
        return $query->where('provincia', $provincia);
    }

    /**
     * Scope per filtrare per sigla provincia
     */
    public function scopeBySiglaProvincia($query, $sigla)
    {
        return $query->where('sigla_provincia', $sigla);
    }

    /**
     * Relazione: Un Comune ha molti User
     * 
     * Permette di accedere agli utenti residenti in questo comune:
     * $comune->users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'comune_id');
    }
}
