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
            $table->timestamp('date_generation')->useCurrent();
            $table->enum('format', ['html', 'pdf', 'json', 'docx'])->default('html');
            $table->longText('contenu')->nullable();
            $table->text('synthese')->nullable();
            $table->jsonb('statistiques')->nullable();
            $table->string('signature_electronique', 255)->nullable();
            $table->string('chemin_fichier', 500)->nullable();
            $table->timestamps();

            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};