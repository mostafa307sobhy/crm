<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — الصفحة غير موجودة | بيت المحاسبة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #002E5D;
            --primary-dark: #001a35;
            --gold: #CFB065;
            --bg: #f4f7f6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,46,93,0.08);
        }
        .icon-wrap {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #e8f0fe, #d0e1ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap i { font-size: 2.2rem; color: var(--primary); }
        .code {
            font-size: 5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: .5rem;
            letter-spacing: -2px;
        }
        .code span { color: var(--gold); }
        h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: .75rem;
        }
        p {
            color: #6c7a8a;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            padding: .75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: .95rem;
            transition: background .2s, transform .15s;
        }
        .btn-home:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: var(--primary);
            padding: .75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: .95rem;
            border: 1.5px solid #dde4ee;
            margin-right: .75rem;
            transition: border-color .2s;
        }
        .btn-back:hover { border-color: var(--primary); }
        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), transparent);
            margin: 1rem auto 1.5rem;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="fas fa-map-signs"></i>
        </div>
        <div class="code">4<span>0</span>4</div>
        <div class="divider"></div>
        <h1>الصفحة غير موجودة</h1>
        <p>
            يبدو أن الصفحة التي تبحث عنها تم نقلها أو حذفها،<br>
            أو ربما كتبت الرابط بشكل خاطئ.
        </p>
        <div>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            <a href="{{ url('/dashboard') }}" class="btn-home">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </div>
    </div>
</body>
</html>
