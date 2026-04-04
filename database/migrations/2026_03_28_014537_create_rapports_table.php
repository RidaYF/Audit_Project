<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('audit_id');
            $table->string('version')->default('1.0');
            $table->string('expert_nom')->nullable();
            $table->text('confidentialite_texte')->nullable();
            $table->text('cadre_mission')->nullable();
            $table->text('methodologie')->nullable();
            $table->text('synthese')->nullable();
            $table->text('analyse_risques')->nullable();
            $table->text('introduction_plan_action')->nullable();
            $table->text('conclusion')->nullable();
            $table->json('equipe_expert')->nullable();
            $table->json('equipe_audite')->nullable();
            $table->json('planning_reel')->nullable();
            $table->json('outils_utilises')->nullable();
            $table->text('entreprise_contexte')->nullable();
            $table->text('justification_perimetre')->nullable();
            $table->text('methodologie_details')->nullable();
            $table->text('demarche_risques')->nullable();
            $table->json('prev_actions')->nullable();
            $table->json('plan_act')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('audit_id')->references('id')->on('audits')->onDelete('cascade');
            $table->unique('audit_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rapports');
    }
};