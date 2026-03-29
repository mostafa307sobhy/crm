<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // إجبار قاعدة البيانات على قبول الحالتين فقط لا غير
        DB::statement("ALTER TABLE clients MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
    public function down(): void {}
};