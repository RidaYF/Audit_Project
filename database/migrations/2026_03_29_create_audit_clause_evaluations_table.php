// database/migrations/2026_03_29_create_audit_clause_evaluations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_clause_evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id');
            $table->uuid('clause_id');
            $table->enum('statut', ['non_audite', 'conforme', 'non_conforme', 'partiellement_conforme', 'non_applicable'])->default('non_audite');
            $table->text('justification')->nullable();
            $table->text('recommandation')->nullable();
            $table->enum('niveau_conformite', ['faible', 'moyen', 'eleve', 'excellent'])->nullable();
            $table->float('score')->default(0);
            $table->timestamps();
            
            $table->unique(['audit_id', 'clause_id']);
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
            $table->foreign('clause_id')->references('id')->on('clauses')->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('audit_clause_evaluations');
    }
};