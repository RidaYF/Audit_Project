<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('preuves', function (Blueprint $table) {
            if (!Schema::hasColumn('preuves', 'file_path')) {
                $table->string('file_path')->nullable()->after('image_path');
            }
        });
    }

    public function down()
    {
        if (Schema::hasColumn('preuves', 'file_path')) {
            Schema::table('preuves', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }
    }
};