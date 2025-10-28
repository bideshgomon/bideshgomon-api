<script setup>
import { computed } from 'vue';

// Using defineModel for simpler v-model binding
const model = defineModel('checked', {
    type: [Array, Boolean], // Allows boolean for single checkbox
    required: false, // Make it optional
    default: false, // Default to unchecked
});

// Props for value attribute (used when checked is an array)
const props = defineProps({
    value: {
        default: null,
    },
});

// Proxy computed for handling array vs boolean modelValue
const proxyChecked = computed({
    get() {
        return model.value;
    },
    set(val) {
        model.value = val;
    }
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
    />
</template>