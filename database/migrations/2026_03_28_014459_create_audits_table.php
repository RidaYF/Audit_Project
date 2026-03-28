<?php
// database/migrations/2026_03_28_000008_create_audits_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('auditeur_id');
            $table->uuid('entreprise_id');
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->timestamp('date_debut')->useCurrent();
            $table->timestamp('date_fin')->nullable();
            $table->enum('status', ['en_cours', 'suspendu', 'termine', 'annule'])->default('en_cours');
            $table->float('score_total')->nullable();
            $table->integer('temps_total')->nullable()->comment('en secondes');
            $table->text('commentaire_global')->nullable();
            $table->string('signature_auditeur', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('auditeur_id');
            $table->index('entreprise_id');
            $table->index('status');
            $table->index('date_debut');
            
            $table->foreign('auditeur_id')->references('id')->on('auditeurs');
            $table->foreign('entreprise_id')->references('id')->on('entreprises');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};