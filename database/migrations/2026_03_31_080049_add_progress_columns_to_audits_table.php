<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits', 'progress_rate')) {
                $table->float('progress_rate')->default(0)->after('score_total');
            }
            if (!Schema::hasColumn('audits', 'conformity_rate')) {
                $table->float('conformity_rate')->default(0)->after('progress_rate');
            }
            if (!Schema::hasColumn('audits', 'non_conform_count')) {
                $table->integer('non_conform_count')->default(0)->after('conformity_rate');
            }
            if (!Schema::hasColumn('audits', 'critique_risk_count')) {
                $table->integer('critique_risk_count')->default(0)->after('non_conform_count');
            }
            if (!Schema::hasColumn('audits', 'high_risk_count')) {
                $table->integer('high_risk_count')->default(0)->after('critique_risk_count');
            }
            if (!Schema::hasColumn('audits', 'clause_conformity_rate')) {
                $table->float('clause_conformity_rate')->default(0)->after('high_risk_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $columns = [
                'progress_rate',
                'conformity_rate',
                'non_conform_count',
                'critique_risk_count',
                'high_risk_count',
                'clause_conformity_rate'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('audits', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};