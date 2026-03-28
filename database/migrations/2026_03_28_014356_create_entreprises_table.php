<?php
// database/migrations/2026_03_28_000003_create_entreprises_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 255);
            $table->string('secteur', 100);
            $table->enum('taille', ['TPE', 'PME', 'ETI', 'GE']);
            $table->string('image_logo', 500)->nullable();
            $table->text('systemes_cles')->nullable();
            $table->string('localisation', 255)->nullable();
            $table->text('contexte_metier')->nullable();
            $table->text('departements')->nullable();
            $table->text('actifs_critiques')->nullable();
            $table->string('chiffre_affaires', 50)->nullable();
            $table->integer('effectif')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nom');
            $table->index(['secteur', 'taille']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};