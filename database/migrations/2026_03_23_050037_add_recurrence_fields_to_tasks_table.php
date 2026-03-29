<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
    {
        // إضافة عميل التكرار إذا لم يكن موجوداً
        if (!Schema::hasColumn('tasks', 'recurrence_type')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('recurrence_type')->default('none')->after('deadline');
            });
        }

        // إضافة عمود تاريخ الانتهاء إذا لم يكن موجوداً
        if (!Schema::hasColumn('tasks', 'recurrence_end_date')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->date('recurrence_end_date')->nullable()->after('recurrence_type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['recurrence_type', 'recurrence_end_date']);
        });
    }
};
