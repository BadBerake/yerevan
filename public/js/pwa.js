/**
 * PWA Helper - Handles service worker registration and PWA install prompt
 */

class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.init();
    }

    async init() {
        // Register service worker
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/service-worker.js');
                console.log('[PWA] Service Worker registered:', registration);

                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });
            } catch (error) {
                console.error('[PWA] Service Worker registration failed:', error);
            }
        }

        // Handle install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        // Track installation
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed');
            this.hideInstallButton();
            this.trackEvent('pwa_installed');
        });
    }

    showInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'inline-flex';

            installBtn.addEventListener('click', async () => {
                if (!this.deferredPrompt) return;

                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;

                console.log('[PWA] Install prompt outcome:', outcome);
                this.trackEvent('pwa_prompt', { outcome });

                this.deferredPrompt = null;
                this.hideInstallButton();
            });
        }
    }

    hideInstallButton() {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }
    }

    showUpdateNotification() {
        if (confirm('A new version is available! Reload to update?')) {
            window.location.reload();
        }
    }

    trackEvent(eventName, params = {}) {
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, params);
        }
    }
}

// Initialize PWA Manager
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new PWAManager();
    });
} else {
    new PWAManager();
}

// Offline status indicator
window.addEventListener('online', () => {
    console.log('[PWA] Back online');
    document.body.classList.remove('offline');
    showToast('You are back online!', 'success');
});

window.addEventListener('offline', () => {
    console.log('[PWA] Offline');
    document.body.classList.add('offline');
    showToast('You are offline. Some features may be limited.', 'warning');
});

// Simple toast notification
function showToast(message, type = 'info') {
    const existing = document.querySelector('.pwa-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `pwa-toast pwa-toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideUp 0.3s ease;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add animations
if (!document.getElementById('pwa-animations')) {
    const style = document.createElement('style');
    style.id = 'pwa-animations';
    style.textContent = `
        @keyframes slideUp {
            from { transform: translate(-50%, 100px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translate(-50%, 0); opacity: 1; }
            to { transform: translate(-50%, 100px); opacity: 0; }
        }
        body.offline::before {
            content: 'Offline Mode';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f59e0b;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
        }
        body.offline {
            padding-top: 40px;
        }
    `;
    document.head.appendChild(style);
}
