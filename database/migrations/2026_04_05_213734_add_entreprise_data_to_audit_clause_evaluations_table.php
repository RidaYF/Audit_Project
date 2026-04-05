<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_clause_evaluations', 'entreprise_data')) {
                $table->json('entreprise_data')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('audit_clause_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('audit_clause_evaluations', 'entreprise_data')) {
                $table->dropColumn('entreprise_data');
            }
        });
    }
};