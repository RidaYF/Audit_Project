<?php
// database/migrations/2026_03_28_000007_create_preuves_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preuves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('controle_id');
            $table->string('titre', 255);
            $table->enum('type_preuve', ['document', 'configuration', 'log', 'entretien', 'capture_ecran', 'analyse', 'risque']);
            $table->text('contenu');
            $table->string('source', 255)->nullable();
            $table->boolean('est_conforme')->nullable();
            $table->boolean('est_critique')->default(false);
            $table->integer('ordre_affichage')->default(0);
            $table->integer('points_max')->default(10);
            $table->text('explication_attendu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('controle_id');
            $table->index('type_preuve');
            
            $table->foreign('controle_id')->references('id')->on('controles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preuves');
    }
};