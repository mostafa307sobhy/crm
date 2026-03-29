<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->string('attachment_url')->nullable()->after('task_desc'); // رابط المرفق (اختياري)
        $table->text('completion_reply')->nullable()->after('status'); // رد الموظف عند الإنجاز
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['attachment_url', 'completion_reply']);
    });
}
};
