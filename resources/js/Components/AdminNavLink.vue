<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-900' // Active state
        : 'flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white' // Inactive state
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>