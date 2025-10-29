<script setup>
defineProps({
    modelValue: null, // Not used directly for file inputs, but good practice
    id: String,
});

// We emit the 'update:modelValue' event with the File object
defineEmits(['update:modelValue']);

const handleChange = (event) => {
    // Emit the File object when the input changes
    emit('update:modelValue', event.target.files[0]);
};
</script>

<template>
    <input
        type="file"
        :id="id"
        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
        @change="$emit('update:modelValue', $event.target.files[0])"
    />
</template>