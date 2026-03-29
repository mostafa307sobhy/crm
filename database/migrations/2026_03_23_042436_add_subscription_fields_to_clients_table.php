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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('subscription_duration')->nullable()->after('sub_start_date'); // مدة الاشتراك
            $table->date('sub_end_date')->nullable()->after('subscription_duration'); // تاريخ الانتهاء
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['subscription_duration', 'sub_end_date']);
        });
    }
};
