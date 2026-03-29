<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('operations', function (Blueprint $table) {
            $table->string('attachment_url')->nullable()->after('action_text');
            $table->foreignId('reply_to_id')->nullable()->constrained('operations')->nullOnDelete()->after('attachment_url');
        });
    }
    public function down() {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['reply_to_id']);
            $table->dropColumn(['attachment_url', 'reply_to_id']);
        });
    }
};