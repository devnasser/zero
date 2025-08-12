#!/bin/bash

# Auto-Push Script - الدفع التلقائي المستمر
# يتم تشغيله كل دقيقة لدفع التحديثات إلى main

WORKSPACE="/workspace"
LOG_FILE="/workspace/auto-push.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

echo "[$DATE] 🚀 بدء عملية الدفع التلقائي..." >> $LOG_FILE

cd $WORKSPACE

# فحص وجود تغييرات
if [[ -n $(git status --porcelain) ]]; then
    echo "[$DATE] 📝 تم العثور على تغييرات جديدة" >> $LOG_FILE
    
    # إضافة جميع الملفات
    git add -A
    echo "[$DATE] ✅ تم إضافة الملفات" >> $LOG_FILE
    
    # إنشاء commit مع تاريخ ووقت
    COMMIT_MSG="🤖 Auto-commit: LEGEND-AI Swarm Progress - $DATE"
    git commit -m "$COMMIT_MSG"
    echo "[$DATE] ✅ تم إنشاء commit: $COMMIT_MSG" >> $LOG_FILE
    
    # دفع إلى main
    git push origin main
    if [ $? -eq 0 ]; then
        echo "[$DATE] 🎯 ✅ تم الدفع بنجاح إلى main" >> $LOG_FILE
    else
        echo "[$DATE] ❌ فشل في الدفع إلى main" >> $LOG_FILE
    fi
else
    echo "[$DATE] 📋 لا توجد تغييرات جديدة" >> $LOG_FILE
fi

echo "[$DATE] 🏁 انتهاء عملية الدفع التلقائي" >> $LOG_FILE
echo "" >> $LOG_FILE
