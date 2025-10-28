<script setup>
import { onMounted, ref, useAttrs, computed } from 'vue'; // Import computed and useAttrs

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const attrs = useAttrs(); // Get non-prop attributes like class

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

// Define default classes including dark mode
const defaultClasses = 'rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600';

// Combine default classes with any passed-in classes
const combinedClasses = computed(() => {
    return [defaultClasses, attrs.class];
});

</script>

<template>
    <input
        :class="combinedClasses" {{-- Dynamically merge classes --}}
        v-model="model"
        ref="input"
    />
</template>