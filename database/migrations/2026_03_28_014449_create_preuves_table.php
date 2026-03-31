<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preuves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('controle_id');
            $table->string('titre', 255);
            $table->string('type_preuve', 20);
            $table->text('contenu')->nullable();
            $table->integer('points_max')->default(1);
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('controle_id')->references('id')->on('controles')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('utilisateurs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preuves');
    }
};