<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('operations', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('is_pinned');
            $table->boolean('is_system')->default(false)->after('is_edited');
        });
    }
    public function down() {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'is_system']);
        });
    }
};