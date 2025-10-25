<template>
    <div>
        <Head :title="title" />

        <PublicHeader />

        <main>
            <slot />
        </main>

        <PublicFooter />
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PublicHeader from '@/Components/PublicHeader.vue';
import PublicFooter from '@/Components/PublicFooter.vue';

defineProps({
    title: String,
});
</script>

<style>
/* Load brand-specific styles - NOTE: Better practice is to import this in resources/js/app.js */
/* If custom-style.css is not in public/css, adjust path */
/* @import '/css/custom-style.css'; */
/* Or if it's in resources/css: */
/* @import '../../css/custom-style.css'; */

/* Define CSS variables if custom-style.css relies on them and isn't loaded globally */
 :root {
    --brand-primary: #007bff; /* Example Blue */
    --brand-secondary: #6c757d; /* Example Gray */
    --brand-light: #f8f9fa; /* Example Light Gray */
    --brand-border: #dee2e6; /* Example Border Gray */
    /* Add other variables as needed */
 }

.brand-font-primary {
    font-family: 'Arial', sans-serif;
}

</style>