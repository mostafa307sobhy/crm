<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemLogSeeder extends Seeder
{
    public function run(): void
    {
        $oldLogs = DB::select('SELECT * FROM u757178015_crm_db.system_logs');
        
        foreach ($oldLogs as $log) {
            DB::table('system_logs')->insert([
                'id' => $log->id,
                'user_id' => $log->user_id,
                'action_type' => $log->action_type,
                'action_details' => $log->action_details,
                'ip_address' => $log->ip_address,
                'location_info' => $log->location_info,
                'created_at' => $log->created_at,
                'updated_at' => $log->created_at,
            ]);
        }
        $this->command->info('تم نقل سجلات الرقابة الإدارية بنجاح! 🕵️‍♂️');
    }
}