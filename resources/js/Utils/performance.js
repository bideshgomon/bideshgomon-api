import { ref, onMounted } from 'vue';

/**
 * Debounce function execution
 */
export function debounce(fn, delay = 300) {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(this, args), delay);
  };
}

/**
 * Throttle function execution
 */
export function throttle(fn, limit = 300) {
  let inThrottle;
  return function (...args) {
    if (!inThrottle) {
      fn.apply(this, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}

/**
 * Check if reduced motion is preferred
 */
export function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/**
 * Get connection speed
 */
export function useNetworkSpeed() {
  const connectionSpeed = ref('4g');
  const isSlow = ref(false);

  onMounted(() => {
    if ('connection' in navigator) {
      const connection = navigator.connection;
      
      // Determine speed
      const effectiveType = connection.effectiveType;
      connectionSpeed.value = effectiveType;
      
      // Mark slow connections
      isSlow.value = effectiveType === 'slow-2g' || effectiveType === '2g' || effectiveType === '3g';
      
      // Listen for changes
      connection.addEventListener('change', () => {
        connectionSpeed.value = connection.effectiveType;
        isSlow.value = connection.effectiveType === 'slow-2g' || 
                       connection.effectiveType === '2g' || 
                       connection.effectiveType === '3g';
      });
    }
  });

  return { connectionSpeed, isSlow };
}

/**
 * Preload critical resources
 */
export function preloadResource(url, type = 'fetch') {
  const link = document.createElement('link');
  link.rel = 'preload';
  link.href = url;
  
  if (type === 'font') {
    link.as = 'font';
    link.type = 'font/woff2';
    link.crossOrigin = 'anonymous';
  } else if (type === 'image') {
    link.as = 'image';
  } else {
    link.as = 'fetch';
    link.crossOrigin = 'anonymous';
  }
  
  document.head.appendChild(link);
}

/**
 * Prefetch route for faster navigation
 */
export function prefetchRoute(url) {
  const link = document.createElement('link');
  link.rel = 'prefetch';
  link.href = url;
  document.head.appendChild(link);
}

/**
 * Load script dynamically
 */
export function loadScript(src) {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = src;
    script.async = true;
    script.onload = resolve;
    script.onerror = reject;
    document.body.appendChild(script);
  });
}

/**
 * Optimize image loading based on device
 */
export function getOptimizedImageUrl(url, options = {}) {
  const { width, quality = 80, format = 'webp' } = options;
  
  // Check if browser supports WebP
  const supportsWebP = document.createElement('canvas')
    .toDataURL('image/webp')
    .indexOf('data:image/webp') === 0;
  
  // Get device pixel ratio (for retina displays)
  const dpr = window.devicePixelRatio || 1;
  
  // Calculate optimal width
  const optimalWidth = width ? Math.round(width * dpr) : null;
  
  // In a real app, you'd call an image optimization service
  // For now, return original URL with query params
  const params = new URLSearchParams();
  if (optimalWidth) params.append('w', optimalWidth);
  params.append('q', quality);
  if (supportsWebP && format === 'webp') params.append('fm', 'webp');
  
  return `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;
}

/**
 * Detect if element is in viewport
 */
export function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

/**
 * Get performance metrics
 */
export function getPerformanceMetrics() {
  if (!window.performance) return null;
  
  const navigation = performance.getEntriesByType('navigation')[0];
  
  if (!navigation) return null;
  
  return {
    // Time to first byte
    ttfb: navigation.responseStart - navigation.requestStart,
    
    // DOM Content Loaded
    domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
    
    // Full page load
    loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
    
    // DNS lookup
    dns: navigation.domainLookupEnd - navigation.domainLookupStart,
    
    // TCP connection
    tcp: navigation.connectEnd - navigation.connectStart,
    
    // Time to interactive
    tti: navigation.domInteractive - navigation.fetchStart,
  };
}

/**
 * Save to cache with expiry
 */
export function cacheSet(key, value, expiryMinutes = 60) {
  const item = {
    value,
    expiry: Date.now() + (expiryMinutes * 60 * 1000)
  };
  localStorage.setItem(key, JSON.stringify(item));
}

/**
 * Get from cache
 */
export function cacheGet(key) {
  const itemStr = localStorage.getItem(key);
  
  if (!itemStr) return null;
  
  try {
    const item = JSON.parse(itemStr);
    
    // Check expiry
    if (Date.now() > item.expiry) {
      localStorage.removeItem(key);
      return null;
    }
    
    return item.value;
  } catch {
    return null;
  }
}

/**
 * Clear expired cache items
 */
export function clearExpiredCache() {
  const keys = Object.keys(localStorage);
  
  keys.forEach(key => {
    const itemStr = localStorage.getItem(key);
    
    try {
      const item = JSON.parse(itemStr);
      if (item.expiry && Date.now() > item.expiry) {
        localStorage.removeItem(key);
      }
    } catch {
      // Not a cached item or corrupted
    }
  });
}
