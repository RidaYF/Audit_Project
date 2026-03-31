<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id');
            $table->uuid('reference_id');
            $table->text('objectif_specifique')->nullable();
            $table->integer('ponderation')->default(1);
            $table->timestamps();

            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
            $table->foreign('reference_id')->references('id')->on('iso_references')->onDelete('cascade');
            $table->unique(['audit_id', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_references');
    }
};