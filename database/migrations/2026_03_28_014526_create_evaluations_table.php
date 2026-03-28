<?php
// database/migrations/2026_03_28_000010_create_evaluations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id');
            $table->uuid('controle_id');
            $table->enum('statut', ['non_audite', 'conforme', 'non_conforme', 'non_applicable', 'partiellement_conforme'])->default('non_audite');
            $table->text('justification')->nullable();
            $table->text('recommandation')->nullable();
            $table->enum('gravite_risque', ['faible', 'moyen', 'eleve', 'critique'])->nullable();
            $table->enum('probabilite_risque', ['faible', 'moyen', 'eleve'])->nullable();
            $table->enum('niveau_risque_calcule', ['faible', 'moyen', 'eleve', 'critique'])->nullable();
            $table->jsonb('preuves_consultees')->default('[]');
            $table->text('commentaire_formateur')->nullable();
            $table->timestamp('date_evaluation')->useCurrent();
            $table->float('points_obtenus')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['audit_id', 'controle_id']);
            $table->index('audit_id');
            $table->index('controle_id');
            $table->index('statut');
            
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
            $table->foreign('controle_id')->references('id')->on('controles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};