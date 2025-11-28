<template>
    <div :class="['inline-flex items-center', sizeClass]">
        <!-- Flag image -->
        <img 
            v-if="flagUrl"
            :src="flagUrl" 
            :alt="`${countryName} flag`"
            :class="['rounded shadow-sm', imageSizeClass]"
            loading="lazy"
        />
        
        <!-- Fallback emoji flag -->
        <span v-else :class="['inline-block', emojiSizeClass]">
            {{ flagEmoji }}
        </span>

        <!-- Country name (optional) -->
        <span v-if="showName && countryName" :class="['ml-2 font-medium', textSizeClass]">
            {{ countryName }}
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    countryCode: {
        type: String,
        required: true,
        // ISO 3166-1 alpha-2 code (e.g., 'BD', 'US', 'GB')
    },
    countryName: String,
    size: {
        type: String,
        default: 'md', // xs, sm, md, lg
    },
    showName: {
        type: Boolean,
        default: false,
    },
    useImage: {
        type: Boolean,
        default: true,
    },
});

// Convert country code to flag emoji
const flagEmoji = computed(() => {
    if (!props.countryCode) return '🏳️';
    
    const codePoints = props.countryCode
        .toUpperCase()
        .split('')
        .map(char => 127397 + char.charCodeAt(0));
    
    return String.fromCodePoint(...codePoints);
});

// Get flag image URL from CDN
const flagUrl = computed(() => {
    if (!props.useImage || !props.countryCode) return null;
    
    // Using flagcdn.com for high-quality flag images
    const code = props.countryCode.toLowerCase();
    const sizeMap = {
        xs: 'w20',
        sm: 'w40',
        md: 'w80',
        lg: 'w160',
    };
    const size = sizeMap[props.size] || sizeMap.md;
    
    return `https://flagcdn.com/${size}/${code}.png`;
});

const sizeClass = computed(() => {
    return 'inline-flex';
});

const imageSizeClass = computed(() => {
    const sizes = {
        xs: 'w-4 h-3',
        sm: 'w-6 h-4',
        md: 'w-8 h-6',
        lg: 'w-12 h-9',
    };
    return sizes[props.size] || sizes.md;
});

const emojiSizeClass = computed(() => {
    const sizes = {
        xs: 'text-base',
        sm: 'text-lg',
        md: 'text-2xl',
        lg: 'text-4xl',
    };
    return sizes[props.size] || sizes.md;
});

const textSizeClass = computed(() => {
    const sizes = {
        xs: 'text-xs',
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg',
    };
    return sizes[props.size] || sizes.md;
});
</script>
