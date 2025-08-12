#!/bin/bash

# 🧹 Zero Platform Final Cleanup Script
# تنظيف نهائي شامل للمشروع

echo "🧹 بدء التنظيف النهائي الشامل..."

# تنظيف ملفات Laravel المؤقتة
echo "1. تنظيف Laravel cache..."
cd core/zero-platform/zero-template
php artisan cache:clear 2>/dev/null
php artisan config:clear 2>/dev/null
php artisan view:clear 2>/dev/null
cd ../../../

# تنظيف ملفات composer غير الضرورية
echo "2. تنظيف Composer cache..."
find . -name "composer.lock" -delete 2>/dev/null
find . -name ".phpunit.cache" -delete 2>/dev/null

# تنظيف ملفات النظام المؤقتة
echo "3. تنظيف ملفات النظام..."
find . -name ".DS_Store" -delete 2>/dev/null
find . -name "Thumbs.db" -delete 2>/dev/null
find . -name "*.tmp" -delete 2>/dev/null
find . -name "*.temp" -delete 2>/dev/null

# تنظيف ملفات IDE
echo "4. تنظيف ملفات IDE..."
rm -rf .vscode 2>/dev/null
rm -rf .idea 2>/dev/null

# تنظيف logs قديمة
echo "5. تنظيف logs قديمة..."
find . -name "*.log" -mtime +7 -delete 2>/dev/null

# إحصائيات نهائية
echo "✅ التنظيف مكتمل!"
echo "📊 حجم المشروع النهائي:"
du -sh . | awk '{print $1}'

echo "🚀 Zero Platform نظيف وجاهز للعمل!"
