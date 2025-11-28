// PWA Installation and Service Worker Management
export class PWAManager {
  constructor() {
    this.deferredPrompt = null;
    this.swRegistration = null;
    this.isInstalled = false;
    this.isOnline = navigator.onLine;
  }

  /**
   * Initialize PWA features
   */
  async init() {
    // Check if already installed
    this.checkInstallStatus();

    // Register service worker
    await this.registerServiceWorker();

    // Setup install prompt
    this.setupInstallPrompt();

    // Setup online/offline detection
    this.setupNetworkDetection();

    // Setup update detection
    this.setupUpdateDetection();

    console.log('✅ PWA initialized');
  }

  /**
   * Check if app is installed
   */
  checkInstallStatus() {
    // Check if running as PWA
    if (window.matchMedia('(display-mode: standalone)').matches || 
        window.navigator.standalone === true) {
      this.isInstalled = true;
      console.log('📱 Running as installed PWA');
    }
  }

  /**
   * Register service worker
   */
  async registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
      console.log('⚠️ Service Worker not supported');
      return;
    }

    try {
      this.swRegistration = await navigator.serviceWorker.register('/sw.js', {
        scope: '/'
      });

      console.log('✅ Service Worker registered:', this.swRegistration.scope);

      // Check for updates every hour
      setInterval(() => {
        this.swRegistration.update();
      }, 60 * 60 * 1000);

    } catch (error) {
      console.error('❌ Service Worker registration failed:', error);
    }
  }

  /**
   * Setup install prompt
   */
  setupInstallPrompt() {
    window.addEventListener('beforeinstallprompt', (e) => {
      // Prevent the default install prompt
      e.preventDefault();
      
      // Store the event for later use
      this.deferredPrompt = e;
      
      // Show custom install button
      this.showInstallButton();
      
      console.log('💾 Install prompt ready');
    });

    // Detect when app is installed
    window.addEventListener('appinstalled', () => {
      this.isInstalled = true;
      this.hideInstallButton();
      console.log('✅ App installed successfully');
      
      // Track installation
      this.trackInstall();
    });
  }

  /**
   * Show install button
   */
  showInstallButton() {
    // Dispatch event for Vue components to show install UI
    window.dispatchEvent(new CustomEvent('pwa:showInstall'));
  }

  /**
   * Hide install button
   */
  hideInstallButton() {
    window.dispatchEvent(new CustomEvent('pwa:hideInstall'));
  }

  /**
   * Prompt user to install
   */
  async promptInstall() {
    if (!this.deferredPrompt) {
      console.log('⚠️ Install prompt not available');
      return false;
    }

    // Show the install prompt
    this.deferredPrompt.prompt();

    // Wait for user choice
    const { outcome } = await this.deferredPrompt.userChoice;
    
    console.log(`User ${outcome} the install prompt`);

    // Clear the deferred prompt
    this.deferredPrompt = null;

    return outcome === 'accepted';
  }

  /**
   * Setup network detection
   */
  setupNetworkDetection() {
    window.addEventListener('online', () => {
      this.isOnline = true;
      console.log('🌐 Back online');
      window.dispatchEvent(new CustomEvent('pwa:online'));
    });

    window.addEventListener('offline', () => {
      this.isOnline = false;
      console.log('📡 Gone offline');
      window.dispatchEvent(new CustomEvent('pwa:offline'));
    });
  }

  /**
   * Setup update detection
   */
  setupUpdateDetection() {
    if (!this.swRegistration) return;

    this.swRegistration.addEventListener('updatefound', () => {
      const newWorker = this.swRegistration.installing;

      newWorker.addEventListener('statechange', () => {
        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
          // New version available
          console.log('🔄 New version available');
          window.dispatchEvent(new CustomEvent('pwa:updateAvailable'));
        }
      });
    });
  }

  /**
   * Update to new version
   */
  async updateApp() {
    if (!this.swRegistration || !this.swRegistration.waiting) {
      return;
    }

    // Tell service worker to skip waiting
    this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });

    // Reload page when new service worker takes over
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      window.location.reload();
    });
  }

  /**
   * Request notification permission
   */
  async requestNotificationPermission() {
    if (!('Notification' in window)) {
      console.log('⚠️ Notifications not supported');
      return false;
    }

    const permission = await Notification.requestPermission();
    console.log('🔔 Notification permission:', permission);
    
    return permission === 'granted';
  }

  /**
   * Subscribe to push notifications
   */
  async subscribeToPush() {
    if (!this.swRegistration) {
      console.log('⚠️ Service Worker not registered');
      return null;
    }

    try {
      const subscription = await this.swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: this.urlBase64ToUint8Array(import.meta.env.VITE_VAPID_PUBLIC_KEY || '')
      });

      console.log('✅ Push subscription:', subscription);
      return subscription;

    } catch (error) {
      console.error('❌ Push subscription failed:', error);
      return null;
    }
  }

  /**
   * Clear all caches
   */
  async clearCache() {
    if (!this.swRegistration) return;

    this.swRegistration.active.postMessage({ type: 'CLEAR_CACHE' });
    console.log('🗑️ Cache cleared');
  }

  /**
   * Track install analytics
   */
  trackInstall() {
    // Send to analytics
    if (window.gtag) {
      window.gtag('event', 'pwa_install', {
        event_category: 'engagement',
        event_label: 'PWA Installation'
      });
    }
  }

  /**
   * Utility: Convert VAPID key
   */
  urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
      .replace(/\-/g, '+')
      .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }
}

// Create global instance
export const pwa = new PWAManager();

// Auto-initialize on load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => pwa.init());
} else {
  pwa.init();
}
