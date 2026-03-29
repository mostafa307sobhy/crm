<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataTransferSeeder extends Seeder
{
    public function run(): void
    {
        // 1. نقل المستخدمين وتشفير كلمات المرور
        $oldUsers = DB::select('SELECT * FROM u757178015_crm_db.users');
        
        foreach ($oldUsers as $user) {
            DB::table('users')->insert([
                'id' => $user->id,
                'username' => $user->username,
                // هنا السحر: نقوم بتشفير كلمة المرور القديمة لتصبح آمنة 100%
                'password' => Hash::make($user->password),
                'role' => $user->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. نقل العملاء وتوزيع الموظفين المخصصين
        $oldClients = DB::select('SELECT * FROM u757178015_crm_db.clients');
        
        foreach ($oldClients as $client) {
            // إدخال بيانات العميل
            DB::table('clients')->insert([
                'id' => $client->id,
                'name' => $client->name,
                'status' => $client->status,
                'is_active' => $client->is_active,
                'package_type' => $client->package_type,
                'sub_start_date' => $client->sub_start_date,
                'sub_renew_date' => $client->sub_renew_date,
                'quick_note' => $client->quick_note,
                'tax_number' => $client->tax_number,
                'commercial_register' => $client->commercial_register,
                'critical_alert' => $client->critical_alert,
                'created_at' => $client->created_at,
                'updated_at' => $client->created_at,
            ]);

            // معالجة الموظفين المخصصين (تفكيك النص 1,4,5 إلى سجلات منفصلة)
            if (!empty($client->assigned_users)) {
                $userIds = explode(',', $client->assigned_users);
                foreach ($userIds as $uid) {
                    if (trim($uid) !== '') {
                        DB::table('client_user')->insert([
                            'client_id' => $client->id,
                            'user_id' => trim($uid),
                        ]);
                    }
                }
            }
        }

        $this->command->info('تم نقل المستخدمين والعملاء وتشفير كلمات المرور بنجاح! 🚀');
    }
}