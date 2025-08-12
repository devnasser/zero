/**
 * Zero Platform Optimized JavaScript
 * Lightweight Alpine.js + Livewire compatible functions
 */

// Zero Platform Utilities
window.ZeroPlatform = {
    // Localization support
    locale: document.documentElement.lang || 'ar',
    
    // Initialize the platform
    init() {
        this.setupRTL();
        this.setupLivewire();
        this.setupVoiceSearch();
        console.log('Zero Platform initialized');
    },
    
    // RTL Support
    setupRTL() {
        if (this.locale === 'ar') {
            document.documentElement.dir = 'rtl';
            document.body.classList.add('rtl');
        }
    },
    
    // Livewire Integration
    setupLivewire() {
        document.addEventListener('livewire:load', function () {
            console.log('Livewire loaded');
        });
        
        document.addEventListener('livewire:update', function () {
            console.log('Livewire updated');
        });
    },
    
    // Voice Search for illiterate users
    setupVoiceSearch() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            window.voiceSearchAvailable = true;
            console.log('Voice search available');
        }
    },
    
    // Start voice recognition
    startVoiceSearch(inputElement) {
        if (!window.voiceSearchAvailable) return;
        
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();
        
        recognition.lang = this.locale === 'ar' ? 'ar-SA' : 'en-US';
        recognition.continuous = false;
        recognition.interimResults = false;
        
        recognition.onstart = function() {
            inputElement.placeholder = 'جاري الاستماع...';
            inputElement.classList.add('listening');
        };
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            inputElement.value = transcript;
            inputElement.dispatchEvent(new Event('input'));
        };
        
        recognition.onerror = function(event) {
            console.error('Voice recognition error:', event.error);
            inputElement.placeholder = 'خطأ في التعرف على الصوت';
        };
        
        recognition.onend = function() {
            inputElement.classList.remove('listening');
            inputElement.placeholder = 'ابحث باستخدام الصوت...';
        };
        
        recognition.start();
    },
    
    // Loading indicator
    showLoading(element) {
        element.innerHTML = '<span class="zero-loading"></span> جاري التحميل...';
        element.disabled = true;
    },
    
    hideLoading(element, originalText) {
        element.innerHTML = originalText;
        element.disabled = false;
    },
    
    // Notification system
    notify(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    ZeroPlatform.init();
});

// Alpine.js integration
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized with Zero Platform');
});
