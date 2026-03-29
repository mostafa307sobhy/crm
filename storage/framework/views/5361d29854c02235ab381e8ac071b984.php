<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المحاسبة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Tajawal', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: linear-gradient(90deg, #d4af37, #f3e5ab);
        }
        .logo-icon {
            font-size: 50px;
            color: #d4af37;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .btn-gold {
            background: linear-gradient(45deg, #d4af37, #e5c158);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s;
        }
        .btn-gold:hover {
            background: linear-gradient(45deg, #c5a028, #d4af37);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background-color: #f9f9f9;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #d4af37;
            background-color: #fff;
        }
        .input-group-text {
            border-radius: 0 10px 10px 0;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-left: none;
            color: #888;
        }
        .form-control {
            border-radius: 10px 0 0 10px;
            border-right: none;
        }
        .footer-text {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h4 class="fw-bold mb-1 text-dark">بيت المحاسبة</h4>
            <p class="text-muted small mb-4">بوابة تسجيل الدخول للنظام</p>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger small fw-bold py-2 mb-4 text-start rounded-3 shadow-sm">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?> <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-dark px-1">اسم المستخدم</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" value="<?php echo e(old('username')); ?>" class="form-control" required placeholder="user" autocomplete="off">
                    </div>
                </div>
                
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-dark px-1">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-gold w-100 fw-bold">
                    تسجيل الدخول <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </form>

            <div class="footer-text">
                &copy; <?php echo e(date('Y')); ?> بيت المحاسبة. جميع الحقوق محفوظة.
            </div>
        </div>
    </div>

</body>
</html><?php /**PATH E:\crm\accounting-crm\resources\views/auth/login.blade.php ENDPATH**/ ?>