<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controles', function (Blueprint $table) {
            if (!Schema::hasColumn('controles', 'weight')) {
                $table->integer('weight')->default(1)->after('ponderation_defaut')->comment('Poids du contrôle (1-5)');
            }
            if (!Schema::hasColumn('controles', 'auto_calculate')) {
                $table->boolean('auto_calculate')->default(true)->after('weight')->comment('Auto-calcul des indicateurs');
            }
        });
    }

    public function down(): void
    {
        Schema::table('controles', function (Blueprint $table) {
            if (Schema::hasColumn('controles', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('controles', 'auto_calculate')) {
                $table->dropColumn('auto_calculate');
            }
        });
    }
};