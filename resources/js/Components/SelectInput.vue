<script setup>
defineProps({
    modelValue: [String, Number, null],
    options: {
        type: Array,
        required: true, // [{ optionValue: '...', optionLabel: '...' }]
    },
    optionValue: {
        type: String,
        default: 'id',
    },
    optionLabel: {
        type: String,
        default: 'name',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <select
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option :value="null" disabled>Select an option</option>
        <option v-for="option in options" :key="option[optionValue]" :value="option[optionValue]">
            {{ option[optionLabel] }}
        </option>
    </select>
</template>