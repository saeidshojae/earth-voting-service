<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



# سیستم رأی‌گیری

سیستم رأی‌گیری یک میکروسرویس برای مدیریت انتخابات و رأی‌گیری در گروه‌ها است. این سیستم امکان برگزاری انتخابات هیئت مدیره و بازرسان را با قابلیت تفویض رأی فراهم می‌کند.

## ویژگی‌ها

- مدیریت انتخابات گروهی
- ثبت و مدیریت رأی‌ها
- سیستم تفویض رأی
- پردازش خودکار نتایج
- سیستم اعلان‌رسانی
- کنترل دسترسی کاربران

## نیازمندی‌ها

- PHP >= 8.0
- MySQL >= 5.7
- Composer
- Laravel 9.x

## نصب و راه‌اندازی

1. کلون کردن پروژه:
```bash
git clone https://github.com/yourusername/earth-voting-service.git
cd earth-voting-service
```

2. نصب وابستگی‌ها:
```bash
composer install
```

3. کپی فایل تنظیمات:
```bash
cp .env.example .env
```

4. تنظیم متغیرهای محیطی در فایل `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=earth_voting
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. ایجاد کلید برنامه:
```bash
php artisan key:generate
```

6. اجرای میگریشن‌ها:
```bash
php artisan migrate
```

7. راه‌اندازی صف پردازش:
```bash
php artisan queue:work
```

8. تنظیم کرون جاب برای اسکجولر:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## مستندات API

مستندات API در مسیر `/api/documentation` قابل دسترسی است. برای بازسازی مستندات:

```bash
php artisan l5-swagger:generate
```

## تست‌ها

برای اجرای تست‌ها:

```bash
php artisan test
```

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.

# Earth Voting Service

A service for managing voting in Earth Cooperative.

## Test Update
