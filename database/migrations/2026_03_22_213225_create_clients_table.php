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
        // 1. جدول العملاء
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['lead', 'setup', 'active', 'completed'])->default('lead');
            $table->boolean('is_active')->default(true);
            $table->enum('package_type', ['basic', 'advanced', 'professional', 'comprehensive', 'custom'])->default('basic');
            $table->date('sub_start_date')->nullable();
            $table->date('sub_renew_date')->nullable();
            $table->text('quick_note')->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('commercial_register', 50)->nullable();
            $table->text('critical_alert')->nullable();
            $table->timestamps();
        });

        // 2. الجدول الوسيط لتعيين الموظفين للعملاء (علاقة Many-to-Many)
        Schema::create('client_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
