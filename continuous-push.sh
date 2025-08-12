#!/bin/bash

# Continuous Push Loop - الدفع المستمر كل دقيقة
# يعمل في الخلفية باستمرار

WORKSPACE="/workspace"
LOG_FILE="/workspace/continuous-push.log"

echo "🚀 === بدء الدفع المستمر === 🚀" | tee -a $LOG_FILE
echo "⚡ LEGEND-AI Auto-Push System Activated" | tee -a $LOG_FILE
echo "🕐 الدفع كل 60 ثانية إلى main branch" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

while true; do
    DATE=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$DATE] 🔄 فحص التحديثات..." | tee -a $LOG_FILE
    
    cd $WORKSPACE
    
    # فحص وجود تغييرات في المشروع الرئيسي
    if [[ -n $(git status --porcelain) ]]; then
        echo "[$DATE] 📝 تغييرات جديدة في المشروع الرئيسي" | tee -a $LOG_FILE
        
        git add -A
        COMMIT_MSG="🤖 LEGEND-AI Swarm: Continuous Progress Update - $DATE"
        git commit -m "$COMMIT_MSG"
        
        echo "[$DATE] 🚀 دفع إلى main..." | tee -a $LOG_FILE
        git push origin main 2>&1 | tee -a $LOG_FILE
        
        if [ $? -eq 0 ]; then
            echo "[$DATE] ✅ نجح الدفع إلى main" | tee -a $LOG_FILE
        else
            echo "[$DATE] ⚠️ مشكلة في الدفع" | tee -a $LOG_FILE
        fi
    fi
    
    # فحص وجود تغييرات في قطعتي
    if [ -d "$WORKSPACE/qetety-standalone" ]; then
        cd "$WORKSPACE/qetety-standalone"
        if [[ -n $(git status --porcelain 2>/dev/null) ]]; then
            echo "[$DATE] 📝 تغييرات جديدة في قطعتي" | tee -a $LOG_FILE
            
            git add -A
            COMMIT_MSG="🚗 قطعتي Auto-Update: $DATE"
            git commit -m "$COMMIT_MSG"
            echo "[$DATE] ✅ commit قطعتي مكتمل" | tee -a $LOG_FILE
        fi
        cd $WORKSPACE
    fi
    
    echo "[$DATE] 😴 انتظار 60 ثانية..." | tee -a $LOG_FILE
    echo "" | tee -a $LOG_FILE
    sleep 60
done
