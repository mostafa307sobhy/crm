<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $oldAttachments = DB::select('SELECT * FROM u757178015_crm_db.admin_attachments');
        
        foreach ($oldAttachments as $att) {
            DB::table('attachments')->insert([
                'id' => $att->id,
                'client_id' => $att->client_id,
                'doc_name' => $att->doc_name,
                'file_path' => $att->file_path,
                'created_at' => $att->uploaded_at,
                'updated_at' => $att->uploaded_at,
            ]);
        }
        $this->command->info('تم نقل سجلات الملفات بنجاح! 📁');
    }
}