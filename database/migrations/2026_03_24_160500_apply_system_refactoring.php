<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تطبيق التحسينات على قاعدة البيانات
     */
    public function up()
    {
        // 1. تعديلات جدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            // ✅ Soft Deletes فقط (بدون email)
            $table->softDeletes();
        });

        // 2. تعديلات جدول العملاء
        Schema::table('clients', function (Blueprint $table) {
            $table->index('status'); // تسريع البحث بحالة العميل
            $table->softDeletes(); // سلة المهملات
        });

        // 3. تعديلات جدول المهام
        Schema::table('tasks', function (Blueprint $table) {
            // مسح العمود القديم المتضارب لو موجود
            if (Schema::hasColumn('tasks', 'recurrence')) {
                $table->dropColumn('recurrence');
            }
            $table->index('client_id'); // تسريع جلب مهام العميل
            $table->index('status'); // تسريع فلترة المهام المعلقة/المنجزة
            $table->softDeletes(); // سلة المهملات
        });
    }

    /**
     * التراجع عن التحسينات
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
            $table->dropSoftDeletes();
        });
    }
};
