<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_clause_evaluations', 'plan_action')) {
                $table->text('plan_action')->nullable();
            }
            if (!Schema::hasColumn('audit_clause_evaluations', 'responsable')) {
                $table->string('responsable')->nullable();
            }
            if (!Schema::hasColumn('audit_clause_evaluations', 'date_echeance')) {
                $table->date('date_echeance')->nullable();
            }
            if (!Schema::hasColumn('audit_clause_evaluations', 'priorite')) {
                $table->enum('priorite', ['haute', 'moyenne', 'basse'])->nullable();
            }
            if (!Schema::hasColumn('audit_clause_evaluations', 'commentaires')) {
                $table->text('commentaires')->nullable();
            }
            if (!Schema::hasColumn('audit_clause_evaluations', 'statut_traitement')) {
                $table->enum('statut_traitement', ['a_traiter', 'en_cours', 'traite', 'valide'])->default('a_traiter');
            }
        });
    }

    public function down()
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'plan_action', 
                'responsable', 
                'date_echeance', 
                'priorite', 
                'commentaires', 
                'statut_traitement'
            ]);
        });
    }
};