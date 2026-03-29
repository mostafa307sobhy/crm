# 🏢 نظام إدارة المحاسبة - Accounting CRM

نظام شامل لإدارة العملاء والمهام المحاسبية مع نظام صلاحيات متقدم.

---

## ✨ المميزات الرئيسية

### 🔐 نظام الصلاحيات
- **مدير النظام (Admin):** سيطرة كاملة على النظام
- **موظف (User):** رؤية محدودة للعملاء والمهام المخصصة له

### 👥 إدارة العملاء الذكية
- **نطاق الرؤية الابتكاري:**
  - متاح للجميع
  - تخصيص لموظفين محددين
  - مغلق للإدارة فقط
- معلومات شاملة: باقات، اشتراكات، بيانات ضريبية
- سجل عمليات تفاعلي (Timeline)

### 📋 محرك المهام المتطور
- **مهام متكررة تلقائياً:** يومية، أسبوعية، شهرية
- **نظام إثبات الإنجاز:** رد إنجاز + رابط المخرج
- **فلترة ذكية:** كل موظف يرى مهامه فقط

### 💬 نظام Operations المبتكر
- تفاعلات Emoji مثل Slack (👍❤️✅😂👀)
- ردود على الرسائل
- تثبيت الرسائل المهمة
- روابط مرفقة

### 📊 تقارير وإحصائيات
- رسوم بيانية تفاعلية (Chart.js)
- تقويم المهام (FullCalendar)
- سجل رقابة شامل

---

## 🚀 التثبيت والإعداد

### المتطلبات
- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Laravel 11.x

### خطوات التثبيت

#### 1️⃣ استنساخ المشروع
```bash
git clone <repository-url>
cd accounting-crm
```

#### 2️⃣ تثبيت المكتبات
```bash
composer install
```

#### 3️⃣ إعداد ملف البيئة
```bash
cp .env.example .env
php artisan key:generate
```

#### 4️⃣ تعديل إعدادات قاعدة البيانات
افتح `.env` وعدل:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accounting_crm
DB_USERNAME=root
DB_PASSWORD=
```

#### 5️⃣ إنشاء قاعدة البيانات
```bash
# إنشاء Database
mysql -u root -p
CREATE DATABASE accounting_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 6️⃣ تشغيل Migrations
```bash
php artisan migrate
```

#### 7️⃣ ملء البيانات التجريبية
```bash
php artisan db:seed
```

**سيتم إنشاء:**
- ✅ 1 Admin + 5 موظفين
- ✅ 13 عميل (10 نشطين + 3 معطلين)
- ✅ 40+ مهمة
- ✅ جهات اتصال، وثائق، ملاحظات
- ✅ سجل عمليات تفاعلي

#### 8️⃣ تشغيل السيرفر
```bash
php artisan serve
```

افتح المتصفح: `http://localhost:8000`

---

## 🔐 بيانات الدخول الافتراضية

### المدير
```
Username: admin
Password: admin123
```

### الموظفين
```
Username: ahmed | sara | mohamed | fatima | ali
Password: 123456
```

---

## 📁 هيكل المشروع

```
accounting-crm/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   └── Middleware/        # IsAdmin Middleware
│   └── Models/                # Eloquent Models
│
├── database/
│   ├── migrations/            # جداول قاعدة البيانات
│   └── seeders/               # البيانات التجريبية
│
├── resources/
│   └── views/                 # واجهات Blade
│       ├── layouts/
│       ├── clients/
│       ├── tasks/
│       ├── reports/
│       └── ...
│
└── routes/
    └── web.php                # المسارات
```

---

## 🔧 الأوامر المفيدة

### إعادة تعيين قاعدة البيانات
```bash
php artisan migrate:fresh --seed
```
⚠️ **تحذير:** سيحذف جميع البيانات!

### إنشاء مستخدم جديد يدوياً
```php
php artisan tinker

User::create([
    'username' => 'newuser',
    'password' => Hash::make('password'),
    'role' => 'user', // أو 'admin'
]);
```

### مسح الـ Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🎨 التقنيات المستخدمة

### Backend
- Laravel 11.x (PHP Framework)
- MySQL (Database)
- Eloquent ORM (Database Management)

### Frontend
- Blade Templates
- Bootstrap 5.3
- Font Awesome 6.4
- SweetAlert2 (Alerts)
- Chart.js (Charts)
- FullCalendar (Calendar)

### مكتبات مساعدة
- Carbon (Date/Time)
- Laravel Soft Deletes
- Custom Middleware

---

## 📊 قاعدة البيانات

### الجداول الرئيسية

#### users
- المستخدمين (Admin/User)
- Soft Deletes ✅

#### clients
- العملاء
- نطاق الرؤية (visibility)
- معلومات الاشتراك
- Soft Deletes ✅

#### tasks
- المهام
- نظام التكرار
- رد الإنجاز
- Soft Deletes ✅

#### operations
- سجل العمليات (Timeline)
- تفاعلات Emoji
- الردود والروابط

#### documents
- روابط Google Drive

#### contacts
- جهات الاتصال

#### client_notes
- ملاحظات وتنبيهات

---

## 🔒 الأمان

### الحماية المطبقة
- ✅ Middleware للصلاحيات (IsAdmin)
- ✅ CSRF Protection
- ✅ Password Hashing (bcrypt)
- ✅ Soft Deletes (منع فقدان البيانات)
- ✅ Validation على كل المدخلات
- ✅ Try-Catch للأخطاء

### نطاق الرؤية
- Admin: يرى كل شيء
- User: يرى فقط:
  - العملاء المتاحين للجميع
  - العملاء المخصصين له
  - مهامه فقط

---

## 🐛 استكشاف الأخطاء

### مشكلة: "Class not found"
```bash
composer dump-autoload
```

### مشكلة: "SQLSTATE[HY000]"
- تأكد من إعدادات `.env`
- تأكد من تشغيل MySQL

### مشكلة: صفحة بيضاء
```bash
php artisan config:clear
php artisan cache:clear
```

### مشكلة: "419 Page Expired"
```bash
php artisan cache:clear
# أعد تحميل الصفحة
```

---

## 📝 الميزات القادمة (Roadmap)

- [ ] نظام الإشعارات
- [ ] تصدير التقارير PDF/Excel
- [ ] API للتكامل الخارجي
- [ ] تطبيق موبايل
- [ ] نظام الفواتير الإلكترونية

---

## 👨‍💻 المساهمة

هذا النظام مبني بواسطة Laravel + Bootstrap. للمساهمة:

1. Fork المشروع
2. أنشئ Branch جديد
3. Commit التعديلات
4. Push للـ Branch
5. افتح Pull Request

---

## 📄 الترخيص

هذا المشروع خاص ومملوك لبيت المحاسبة.

---

## 📞 الدعم

في حالة وجود مشاكل أو استفسارات، يرجى فتح Issue على GitHub.

---

**تم بناء النظام بـ ❤️ باستخدام Laravel**
