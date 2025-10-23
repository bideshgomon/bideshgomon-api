<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// --- Active link logic ---
const currentRoute = usePage().url;

// Sidebar links for admin
const adminLinks = [
    { name: 'Dashboard', href: '#', current: currentRoute.startsWith('/admin/dashboard') },
    { name: 'Universities', href: route('admin.universities.index'), current: currentRoute.startsWith('/admin/universities') },
    { name: 'Courses', href: route('admin.courses.index'), current: currentRoute.startsWith('/admin/courses') },
    // --- ADDED ---
    { name: 'Job Categories', href: route('admin.job-categories.index'), current: currentRoute.startsWith('/admin/job-categories') }, 
    // --- MODIFIED ---
    { name: 'Manage Users', href: '#', current: false }, 
];

// --- Flash message logic ---
const flash = ref(usePage().props.flash);
const showFlash = ref(false);

// Watch for flash message changes
watch(
    () => usePage().props.flash,
    (newFlash) => {
        flash.value = newFlash;
        if (newFlash && (newFlash.success || newFlash.error)) {
            showFlash.value = true;
            setTimeout(() => (showFlash.value = false), 3000); // auto-hide after 3s
        }
    },
    { deep: true }
);
</script>

<template>
    <AuthenticatedLayout>
        <div
            v-if="showFlash && flash.success"
            class="fixed top-20 right-5 z-50 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 shadow"
            role="alert"
        >
            <span class="font-medium">Success!</span> {{ flash.success }}
        </div>

        <div
            v-if="showFlash && flash.error"
            class="fixed top-20 right-5 z-50 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 shadow"
            role="alert"
        >
            <span class="font-medium">Error!</span> {{ flash.error }}
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">

                    <aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
                        <nav class="space-y-1">
                            <Link
                                v-for="item in adminLinks"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-100 dark:bg-gray-700 text-brand-primary dark:text-white'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                                    'group rounded-md px-3 py-2 flex items-center text-sm font-medium',
                                ]"
                                :aria-current="item.current ? 'page' : undefined"
                            >
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </nav>
                    </aside>

                    <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>