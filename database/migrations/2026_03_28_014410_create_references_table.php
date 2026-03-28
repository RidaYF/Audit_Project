<?php
// database/migrations/2026_03_28_000004_create_references_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->string('nom', 100);
            $table->text('description')->nullable();
            $table->enum('type_norme', ['management', 'control', 'risk']);
            $table->string('version', 10)->nullable();
            $table->date('date_publication')->nullable();
            $table->string('url_officielle', 500)->nullable();
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('type_norme');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};