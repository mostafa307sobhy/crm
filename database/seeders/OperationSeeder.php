<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationSeeder extends Seeder
{
    public function run(): void
    {
        $oldOperations = DB::select('SELECT * FROM u757178015_crm_db.operations');
        
        foreach ($oldOperations as $op) {
            DB::table('operations')->insert([
                'id' => $op->id,
                'client_id' => $op->client_id,
                'user_id' => $op->user_id,
                'action_text' => $op->action_text,
                'file_path' => $op->file_path,
                'file_name' => $op->file_name,
                'is_pinned' => $op->is_pinned,
                'created_at' => $op->created_at,
                'updated_at' => $op->created_at, 
            ]);
        }

        $this->command->info('تم نقل سجلات المحادثات والعمليات بنجاح! 💬');
    }
}