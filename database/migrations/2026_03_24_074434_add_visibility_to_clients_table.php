<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // إضافة حقل الرؤية بـ 3 حالات
            $table->enum('visibility', ['all', 'specific', 'admins_only'])->default('all')->after('status');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
};
