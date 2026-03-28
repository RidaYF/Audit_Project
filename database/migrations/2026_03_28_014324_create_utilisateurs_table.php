<?php
// database/migrations/2026_03_28_000001_create_utilisateurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 50)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->enum('role', ['auditeur', 'formateur', 'admin'])->default('auditeur');
            $table->enum('niveau', ['debutant', 'intermediaire', 'avance', 'expert'])->default('debutant');
            $table->float('progression_globale')->default(0);
            $table->string('avatar_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('date_inscription')->useCurrent();
            $table->timestamp('derniere_connexion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};