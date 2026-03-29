<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Task;
use App\Models\Operation;
use App\Models\Contact;
use App\Models\Document;
use App\Models\ClientNote;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * ملء قاعدة البيانات ببيانات تجريبية كاملة
     */
    public function run(): void
    {
        echo "🚀 بدء إنشاء البيانات التجريبية...\n\n";

        // ========================================
        // 1️⃣ إنشاء المستخدمين
        // ========================================
        echo "👥 إنشاء المستخدمين...\n";
        
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        echo "   ✅ Admin: username=admin | password=admin123\n";

        $users = [];
        $usernames = ['ahmed', 'sara', 'mohamed', 'fatima', 'ali'];
        
        foreach ($usernames as $username) {
            $users[] = User::create([
                'username' => $username,
                'password' => Hash::make('123456'),
                'role' => 'user',
            ]);
            echo "   ✅ موظف: username=$username | password=123456\n";
        }

        echo "\n";

        // ========================================
        // 2️⃣ إنشاء العملاء
        // ========================================
        echo "🏢 إنشاء العملاء...\n";

        $clientsData = [
            // عملاء نشطين
            ['name' => 'شركة النور للتجارة', 'status' => 'active', 'package' => 'professional', 'amount' => 2500],
            ['name' => 'مؤسسة الأمل للمقاولات', 'status' => 'active', 'package' => 'comprehensive', 'amount' => 3500],
            ['name' => 'شركة الفجر للاستيراد والتصدير', 'status' => 'active', 'package' => 'advanced', 'amount' => 2000],
            ['name' => 'مكتب الإبداع للاستشارات', 'status' => 'active', 'package' => 'basic', 'amount' => 1200],
            ['name' => 'شركة المستقبل للتكنولوجيا', 'status' => 'active', 'package' => 'professional', 'amount' => 2800],
            ['name' => 'مؤسسة البناء الحديث', 'status' => 'active', 'package' => 'advanced', 'amount' => 1800],
            ['name' => 'شركة التميز للخدمات اللوجستية', 'status' => 'active', 'package' => 'comprehensive', 'amount' => 4000],
            ['name' => 'مكتب الرائد للمحاماة', 'status' => 'active', 'package' => 'basic', 'amount' => 1500],
            ['name' => 'شركة الصفوة للتطوير العقاري', 'status' => 'active', 'package' => 'professional', 'amount' => 3200],
            ['name' => 'مؤسسة الرواد للتدريب', 'status' => 'active', 'package' => 'advanced', 'amount' => 1900],
            
            // عملاء معطلين
            ['name' => 'شركة القمة (مؤرشف)', 'status' => 'inactive', 'package' => 'basic', 'amount' => 1000],
            ['name' => 'مؤسسة النجاح (مؤرشف)', 'status' => 'inactive', 'package' => 'advanced', 'amount' => 1500],
            ['name' => 'شركة الأفق (معطل)', 'status' => 'inactive', 'package' => 'professional', 'amount' => 2000],
        ];

        $clients = [];
        foreach ($clientsData as $index => $data) {
            $client = Client::create([
                'name' => $data['name'],
                'status' => $data['status'],
                'package_type' => $data['package'],
                'subscription_amount' => $data['amount'],
                'sub_start_date' => now()->subMonths(rand(1, 6)),
                'subscription_duration' => ['monthly', 'quarterly', 'semi_annual', 'annual'][rand(0, 3)],
                'sub_end_date' => now()->addMonths(rand(1, 12)),
                'tax_number' => '30' . rand(1000000000000, 9999999999999),
                'commercial_register' => '10101' . rand(10000, 99999),
                'visibility' => $index < 7 ? 'all' : ($index < 10 ? 'specific' : 'admins_only'),
                'is_active' => $data['status'] === 'active',
            ]);

            $clients[] = $client;

            // تخصيص موظفين للعملاء (visibility: specific)
            if ($client->visibility === 'specific') {
                $client->assignedUsers()->attach([
                    $users[rand(0, count($users) - 1)]->id,
                    $users[rand(0, count($users) - 1)]->id,
                ]);
            }

            echo "   ✅ {$client->name}\n";
        }

        echo "\n";

        // ========================================
        // 3️⃣ إنشاء جهات الاتصال
        // ========================================
        echo "📞 إنشاء جهات الاتصال...\n";

        $contactNames = [
            ['name' => 'أحمد محمد', 'title' => 'مدير مالي', 'phone' => '0501234567'],
            ['name' => 'سارة علي', 'title' => 'محاسب رئيسي', 'phone' => '0559876543'],
            ['name' => 'محمد حسن', 'title' => 'المدير التنفيذي', 'phone' => '0551122334'],
            ['name' => 'فاطمة أحمد', 'title' => 'مسؤول المشتريات', 'phone' => '0503344556'],
        ];

        foreach ($clients as $client) {
            if ($client->status === 'active') {
                $contact = $contactNames[rand(0, count($contactNames) - 1)];
                Contact::create([
                    'client_id' => $client->id,
                    'name' => $contact['name'],
                    'job_title' => $contact['title'],
                    'phone' => $contact['phone'],
                ]);
            }
        }
        echo "   ✅ تم إضافة جهات الاتصال\n\n";

        // ========================================
        // 4️⃣ إنشاء الوثائق (روابط Drive)
        // ========================================
        echo "📄 إنشاء الوثائق...\n";

        $documentTypes = [
            'السجل التجاري',
            'شهادة الزكاة والدخل',
            'عقد التأسيس',
            'البطاقة الضريبية',
            'التقرير المالي Q1',
        ];

        foreach ($clients as $client) {
            if ($client->status === 'active') {
                $doc = $documentTypes[rand(0, count($documentTypes) - 1)];
                Document::create([
                    'client_id' => $client->id,
                    'name' => $doc,
                    'drive_url' => 'https://drive.google.com/file/d/' . bin2hex(random_bytes(16)),
                ]);
            }
        }
        echo "   ✅ تم إضافة الوثائق\n\n";

        // ========================================
        // 5️⃣ إنشاء الملاحظات والتنبيهات
        // ========================================
        echo "📝 إنشاء الملاحظات...\n";

        $notes = [
            ['type' => 'note', 'content' => 'العميل ملتزم بالمواعيد ودفع الفواتير في الوقت المحدد.'],
            ['type' => 'alert', 'content' => '⚠️ تنبيه: تأخر في تقديم المستندات المطلوبة.'],
            ['type' => 'note', 'content' => 'يفضل التواصل معه صباحاً فقط.'],
            ['type' => 'alert', 'content' => '🔴 مهم: مراجعة الإقرار الضريبي قبل نهاية الشهر.'],
        ];

        foreach ($clients as $client) {
            if ($client->status === 'active' && rand(0, 1)) {
                $note = $notes[rand(0, count($notes) - 1)];
                ClientNote::create([
                    'client_id' => $client->id,
                    'user_id' => $admin->id,
                    'type' => $note['type'],
                    'content' => $note['content'],
                ]);
            }
        }
        echo "   ✅ تم إضافة الملاحظات\n\n";

        // ========================================
        // 6️⃣ إنشاء المهام
        // ========================================
        echo "📋 إنشاء المهام...\n";

        $taskDescriptions = [
            ['desc' => 'إعداد الإقرار الضريبي الشهري', 'priority' => 'high', 'recurrence' => 'monthly'],
            ['desc' => 'مراجعة الحسابات الختامية', 'priority' => 'high', 'recurrence' => 'none'],
            ['desc' => 'إعداد كشف الرواتب', 'priority' => 'medium', 'recurrence' => 'monthly'],
            ['desc' => 'تسوية البنك', 'priority' => 'medium', 'recurrence' => 'weekly'],
            ['desc' => 'متابعة الفواتير المعلقة', 'priority' => 'high', 'recurrence' => 'weekly'],
            ['desc' => 'تحديث بيانات الموظفين', 'priority' => 'low', 'recurrence' => 'none'],
            ['desc' => 'إعداد تقرير المبيعات', 'priority' => 'medium', 'recurrence' => 'monthly'],
            ['desc' => 'مراجعة المخزون', 'priority' => 'low', 'recurrence' => 'none'],
        ];

        $taskCount = 0;
        foreach ($clients as $client) {
            if ($client->status === 'active') {
                // مهام معلقة
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $taskData = $taskDescriptions[rand(0, count($taskDescriptions) - 1)];
                    $task = Task::create([
                        'client_id' => $client->id,
                        'task_desc' => $taskData['desc'],
                        'priority' => $taskData['priority'],
                        'deadline' => now()->addDays(rand(1, 30)),
                        'recurrence_type' => $taskData['recurrence'],
                        'recurrence_end_date' => $taskData['recurrence'] !== 'none' ? now()->addMonths(6) : null,
                        'created_by' => $admin->id,
                        'status' => 'pending',
                    ]);

                    // تكليف موظفين
                    $task->assignedUsers()->attach([
                        $users[rand(0, count($users) - 1)]->id
                    ]);
                    $taskCount++;
                }

                // مهام منجزة
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $taskData = $taskDescriptions[rand(0, count($taskDescriptions) - 1)];
                    $completedDate = now()->subDays(rand(1, 30));
                    
                    $task = Task::create([
                        'client_id' => $client->id,
                        'task_desc' => $taskData['desc'],
                        'priority' => $taskData['priority'],
                        'deadline' => $completedDate->copy()->subDays(1),
                        'recurrence_type' => 'none',
                        'created_by' => $admin->id,
                        'status' => 'completed',
                        'completed_by' => $users[rand(0, count($users) - 1)]->id,
                        'completed_at' => $completedDate,
                        'completion_reply' => 'تم الإنجاز بنجاح ✅',
                    ]);

                    $task->assignedUsers()->attach([
                        $users[rand(0, count($users) - 1)]->id
                    ]);
                    $taskCount++;
                }
            }
        }
        echo "   ✅ تم إنشاء $taskCount مهمة\n\n";

        // ========================================
        // 7️⃣ إنشاء Operations (Timeline)
        // ========================================
        echo "💬 إنشاء سجل العمليات...\n";

        $operations = [
            'تم رفع المستندات المطلوبة',
            'اتصال هاتفي مع العميل لمتابعة الطلب',
            'تم إرسال الفاتورة عبر البريد الإلكتروني',
            'اجتماع مع العميل لمناقشة الخطة المالية',
            'تم استلام الدفعة الأولى',
            'مراجعة شاملة للحسابات مع الإدارة',
        ];

        $operationCount = 0;
        foreach ($clients as $client) {
            if ($client->status === 'active') {
                for ($i = 0; $i < rand(2, 5); $i++) {
                    Operation::create([
                        'client_id' => $client->id,
                        'user_id' => rand(0, 1) ? $admin->id : $users[rand(0, count($users) - 1)]->id,
                        'action_text' => $operations[rand(0, count($operations) - 1)],
                        'is_pinned' => rand(0, 10) > 8, // 20% فرصة للتثبيت
                        'created_at' => now()->subDays(rand(1, 30)),
                    ]);
                    $operationCount++;
                }
            }
        }
        echo "   ✅ تم إنشاء $operationCount عملية\n\n";

        // ========================================
        // ✅ النتيجة النهائية
        // ========================================
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🎉 تم إنشاء البيانات التجريبية بنجاح!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        echo "📊 ملخص البيانات:\n";
        echo "   👤 المستخدمين: " . User::count() . "\n";
        echo "   🏢 العملاء: " . Client::count() . "\n";
        echo "   📋 المهام: " . Task::count() . "\n";
        echo "   💬 العمليات: " . Operation::count() . "\n";
        echo "   📞 جهات الاتصال: " . Contact::count() . "\n";
        echo "   📄 الوثائق: " . Document::count() . "\n\n";
        
        echo "🔐 بيانات الدخول:\n";
        echo "   Admin: username=admin | password=admin123\n";
        echo "   موظف: username=ahmed | password=123456\n\n";
    }
}
