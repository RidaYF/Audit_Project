<?php
// database/migrations/2026_03_28_000002_create_auditeurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditeurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('utilisateur_id')->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('telephone', 20)->nullable();
            $table->string('certification', 255)->nullable();
            $table->integer('experience_ans')->default(0);
            $table->jsonb('specialites')->default('[]');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nom', 'prenom']);
            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditeurs');
    }
};