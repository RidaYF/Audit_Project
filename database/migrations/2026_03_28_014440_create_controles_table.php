<?php
// database/migrations/2026_03_28_000006_create_controles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clause_id');
            $table->string('code', 50);
            $table->string('titre', 255);
            $table->text('description');
            $table->text('objectif');
            $table->string('categorie', 100)->nullable();
            $table->string('sous_categorie', 100)->nullable();
            $table->enum('niveau_critique', ['faible', 'moyen', 'eleve', 'critique'])->default('moyen');
            $table->enum('type_controle', ['preventif', 'detectif', 'correctif', 'analyse'])->default('preventif');
            $table->integer('ponderation_defaut')->default(1);
            $table->integer('temps_estime')->nullable()->comment('en minutes');
            $table->text('conseils_audit')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['clause_id', 'code']);
            $table->index('code');
            $table->index('categorie');
            
            $table->foreign('clause_id')->references('id')->on('clauses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};