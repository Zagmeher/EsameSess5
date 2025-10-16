<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comuni', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('regione');
            $table->string('provincia');
            $table->string('sigla_provincia', 2);
            $table->string('codice_catastale', 10);
            $table->string('cap', 10);
            $table->timestamps();
            
            // Indici per ottimizzare le query
            $table->index('nome');
            $table->index('regione');
            $table->index('provincia');
            $table->index('sigla_provincia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comuni');
    }
};
