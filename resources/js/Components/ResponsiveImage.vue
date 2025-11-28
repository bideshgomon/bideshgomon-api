<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  sizes: {
    type: String,
    default: '100vw'
  },
  widths: {
    type: Array,
    default: () => [320, 640, 768, 1024, 1280, 1920]
  },
  lazy: {
    type: Boolean,
    default: true
  },
  aspectRatio: {
    type: String,
    default: null // e.g., '16/9', '4/3', '1/1'
  },
  objectFit: {
    type: String,
    default: 'cover', // 'cover', 'contain', 'fill', 'none'
  },
  placeholder: {
    type: String,
    default: 'blur' // 'blur', 'color', 'none'
  }
});

const img = ref(null);
const loaded = ref(false);
const error = ref(false);

// Generate WebP srcset
const srcset = computed(() => {
  if (!props.src) return '';
  
  const basePath = props.src.replace(/\.[^/.]+$/, ''); // Remove extension
  return props.widths
    .map(width => `${basePath}_${width}w.webp ${width}w`)
    .join(', ');
});

// Fallback to original if WebP not available
const fallbackSrc = computed(() => {
  return props.src;
});

// Placeholder styles
const placeholderStyle = computed(() => {
  if (loaded.value) return {};
  
  const styles = {
    backgroundColor: '#f3f4f6'
  };
  
  if (props.placeholder === 'blur') {
    styles.filter = 'blur(10px)';
    styles.transform = 'scale(1.1)';
  }
  
  return styles;
});

// Container styles with aspect ratio
const containerStyle = computed(() => {
  if (!props.aspectRatio) return {};
  
  return {
    aspectRatio: props.aspectRatio,
    position: 'relative',
    overflow: 'hidden'
  };
});

// Image styles
const imageStyle = computed(() => {
  const styles = {
    objectFit: props.objectFit,
    transition: 'all 0.3s ease-in-out'
  };
  
  if (props.aspectRatio) {
    styles.width = '100%';
    styles.height = '100%';
    styles.position = 'absolute';
    styles.top = '0';
    styles.left = '0';
  }
  
  return styles;
});

// Handle image load
const onLoad = () => {
  loaded.value = true;
};

// Handle image error
const onError = () => {
  error.value = true;
  loaded.value = true;
};

// Lazy loading with Intersection Observer
onMounted(() => {
  if (!props.lazy || !img.value) return;
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const image = entry.target;
        const src = image.dataset.src;
        const srcset = image.dataset.srcset;
        
        if (srcset) image.srcset = srcset;
        if (src) image.src = src;
        
        observer.unobserve(image);
      }
    });
  }, {
    rootMargin: '200px',
    threshold: 0.01
  });
  
  observer.observe(img.value);
});
</script>

<template>
  <div :style="containerStyle" class="responsive-image-container">
    <!-- Placeholder -->
    <div
      v-if="!loaded"
      class="absolute inset-0 bg-gray-200 dark:bg-gray-700 animate-pulse"
      :style="placeholderStyle"
    />
    
    <!-- Error state -->
    <div
      v-if="error"
      class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800"
    >
      <div class="text-center">
        <svg
          class="w-12 h-12 mx-auto text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Failed to load image</p>
      </div>
    </div>
    
    <!-- Main image -->
    <picture v-if="!error">
      <!-- WebP format -->
      <source
        v-if="!lazy"
        type="image/webp"
        :srcset="srcset"
        :sizes="sizes"
      />
      <source
        v-else
        type="image/webp"
        :data-srcset="srcset"
        :sizes="sizes"
      />
      
      <!-- Fallback -->
      <img
        ref="img"
        :alt="alt"
        :class="[
          'responsive-image',
          { 'opacity-0': !loaded },
          { 'opacity-100': loaded }
        ]"
        :style="imageStyle"
        :src="lazy ? undefined : fallbackSrc"
        :data-src="lazy ? fallbackSrc : undefined"
        :data-srcset="lazy ? srcset : undefined"
        @load="onLoad"
        @error="onError"
        loading="lazy"
        decoding="async"
      />
    </picture>
  </div>
</template>

<style scoped>
.responsive-image-container {
  position: relative;
  display: block;
}

.responsive-image {
  max-width: 100%;
  height: auto;
  display: block;
  transition: opacity 0.3s ease-in-out;
}

/* Prevent layout shift */
.responsive-image-container::before {
  content: '';
  display: block;
}

/* Smooth loading animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(1.05);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.responsive-image.opacity-100 {
  animation: fadeIn 0.3s ease-in-out;
}

/* Respect reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  .responsive-image {
    transition: none;
    animation: none;
  }
}
</style>
