<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clauses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reference_id');
            $table->uuid('parent_id')->nullable();
            $table->string('numero', 50);
            $table->string('titre', 255);
            $table->text('description')->nullable();
            $table->integer('niveau')->default(1);
            $table->string('chemin_complet', 255);
            $table->enum('niveau_importance', ['faible', 'moyen', 'eleve', 'critique'])->default('moyen');
            $table->integer('ordre_affichage')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['reference_id', 'numero']);
            $table->index(['reference_id', 'parent_id']);
            $table->index(['reference_id', 'chemin_complet']);
            $table->index('parent_id');
            $table->index('niveau');
            
            $table->foreign('reference_id')->references('id')->on('iso_references')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('clauses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('utilisateurs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clauses');
    }
};