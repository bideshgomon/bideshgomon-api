<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-4 py-2 bg-brand-primary border border-transparent rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-opacity-90 focus:bg-opacity-90 active:bg-opacity-95 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>