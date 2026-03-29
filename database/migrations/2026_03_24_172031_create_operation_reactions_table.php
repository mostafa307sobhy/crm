<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('operation_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('emoji'); // هيتخزن فيها 👍 أو ❤️ أو ✅
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('operation_reactions');
    }
};