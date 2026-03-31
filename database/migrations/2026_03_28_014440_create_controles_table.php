<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clause_id')->nullable();
            $table->string('code', 50);
            $table->string('titre', 255);
            $table->text('description');
            $table->text('objectif');
            $table->string('categorie', 100)->nullable();
            $table->string('sous_categorie', 100)->nullable();
            $table->enum('niveau_critique', ['faible', 'moyen', 'eleve', 'critique'])->default('moyen');
            $table->enum('type_controle', ['preventif', 'detectif', 'correctif'])->default('preventif');
            $table->integer('ponderation_defaut')->default(1);
            $table->integer('temps_estime')->nullable();
            $table->text('conseils_audit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('categorie');
            $table->index('niveau_critique');
            $table->unique(['clause_id', 'code']);
            
            $table->foreign('clause_id')->references('id')->on('clauses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('utilisateurs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};