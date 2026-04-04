<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id')->unique();
            
            // --- Informations de Suivi ---
            $table->string('version')->default('2.1');
            $table->string('expert_nom')->nullable();
            $table->timestamp('date_generation')->useCurrent();
            
            // --- Sections Textuelles ---
            $table->longText('confidentialite_texte')->nullable(); 
            $table->longText('cadre_mission')->nullable(); 
            $table->longText('methodologie')->nullable();
            $table->text('synthese')->nullable(); 
            $table->longText('analyse_risques')->nullable(); 
            $table->text('introduction_plan_action')->nullable();

            // --- Données Structurées (JSON compatible SQLite) ---
            $table->json('equipe_expert')->nullable(); 
            $table->json('equipe_audite')->nullable(); 
            $table->json('planning_reel')->nullable(); 
            $table->json('outils_utilises')->nullable(); 
            $table->json('statistiques')->nullable(); 
            
            // --- Technique ---
            $table->enum('format', ['html', 'pdf', 'json', 'docx'])->default('html');
            $table->string('chemin_fichier', 500)->nullable();
            $table->string('signature_electronique', 255)->nullable();
            
            $table->timestamps();

            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};