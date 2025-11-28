/**
 * Image Lazy Loading Directive
 * Usage: <img v-lazy="imageUrl" alt="Description">
 */
export const lazyLoadDirective = {
  mounted(el, binding) {
    const imageUrl = binding.value;
    
    // Create intersection observer
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Load the image
          const img = entry.target;
          
          // Show loading state
          img.classList.add('lazy-loading');
          
          // Load image
          img.src = imageUrl;
          
          // Handle load success
          img.onload = () => {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
          };
          
          // Handle load error
          img.onerror = () => {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
            // Optionally set a fallback image
            img.src = '/images/placeholder.png';
          };
          
          // Stop observing
          observer.unobserve(img);
        }
      });
    }, {
      // Start loading when image is 200px from viewport
      rootMargin: '200px',
      threshold: 0.01
    });
    
    // Start observing
    observer.observe(el);
    
    // Store observer for cleanup
    el._lazyLoadObserver = observer;
  },
  
  unmounted(el) {
    // Cleanup observer
    if (el._lazyLoadObserver) {
      el._lazyLoadObserver.disconnect();
    }
  }
};

/**
 * Background Image Lazy Loading Directive
 * Usage: <div v-lazy-bg="imageUrl"></div>
 */
export const lazyLoadBgDirective = {
  mounted(el, binding) {
    const imageUrl = binding.value;
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Preload the image
          const img = new Image();
          img.src = imageUrl;
          
          img.onload = () => {
            el.style.backgroundImage = `url(${imageUrl})`;
            el.classList.add('lazy-loaded');
          };
          
          observer.unobserve(el);
        }
      });
    }, {
      rootMargin: '200px',
      threshold: 0.01
    });
    
    observer.observe(el);
    el._lazyLoadObserver = observer;
  },
  
  unmounted(el) {
    if (el._lazyLoadObserver) {
      el._lazyLoadObserver.disconnect();
    }
  }
};
