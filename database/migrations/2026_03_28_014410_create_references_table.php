<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->string('nom', 100);
            $table->text('description')->nullable();
            $table->string('type_norme', 20);
            $table->string('version', 10)->nullable();
            $table->date('date_publication')->nullable();
            $table->string('url_officielle', 500)->nullable();
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Ajouter les clés étrangères vers la table utilisateurs
            $table->foreign('created_by')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('utilisateurs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso_references');
    }
};