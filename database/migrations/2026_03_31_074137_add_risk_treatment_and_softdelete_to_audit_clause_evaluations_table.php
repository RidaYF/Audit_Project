<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            // Ajouter les colonnes pour le traitement des risques
            if (!Schema::hasColumn('audit_clause_evaluations', 'plan_action')) {
                $table->text('plan_action')->nullable()->after('recommandation');
            }
            
            if (!Schema::hasColumn('audit_clause_evaluations', 'responsable')) {
                $table->string('responsable')->nullable()->after('plan_action');
            }
            
            if (!Schema::hasColumn('audit_clause_evaluations', 'date_echeance')) {
                $table->date('date_echeance')->nullable()->after('responsable');
            }
            
            if (!Schema::hasColumn('audit_clause_evaluations', 'priorite')) {
                $table->enum('priorite', ['haute', 'moyenne', 'basse'])->nullable()->after('date_echeance');
            }
            
            if (!Schema::hasColumn('audit_clause_evaluations', 'commentaires')) {
                $table->text('commentaires')->nullable()->after('priorite');
            }
            
            if (!Schema::hasColumn('audit_clause_evaluations', 'statut_traitement')) {
                $table->enum('statut_traitement', ['a_traiter', 'en_cours', 'traite', 'valide'])
                    ->default('a_traiter')
                    ->after('commentaires');
            }
            
            // Ajouter la colonne soft delete
            if (!Schema::hasColumn('audit_clause_evaluations', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            $columns = [
                'plan_action',
                'responsable',
                'date_echeance',
                'priorite',
                'commentaires',
                'statut_traitement',
                'deleted_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('audit_clause_evaluations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};