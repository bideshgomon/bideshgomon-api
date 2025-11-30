<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    HomeIcon,
    WalletIcon,
    UserCircleIcon,
    Cog6ToothIcon,
} from '@heroicons/vue/24/outline';
import {
    HomeIcon as HomeIconSolid,
    WalletIcon as WalletIconSolid,
    UserCircleIcon as UserCircleIconSolid,
    Cog6ToothIcon as Cog6ToothIconSolid,
} from '@heroicons/vue/24/solid';

const page = usePage();
const currentRoute = computed(() => page.url);

const navItems = [
    {
        name: 'Home',
        route: 'dashboard',
        icon: HomeIcon,
        iconActive: HomeIconSolid,
    },
    {
        name: 'Wallet',
        route: 'wallet.index',
        icon: WalletIcon,
        iconActive: WalletIconSolid,
    },
    {
        name: 'Profile',
        route: 'profile.show',
        icon: UserCircleIcon,
        iconActive: UserCircleIconSolid,
    },
    {
        name: 'Settings',
        route: 'profile.edit',
        icon: Cog6ToothIcon,
        iconActive: Cog6ToothIconSolid,
    },
];

const isActive = (routeName) => {
    return currentRoute.value.startsWith('/' + routeName.split('.')[0]);
};
</script>

<template>
    <!-- Fixed Bottom Navigation - Mobile First -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 safe-area-inset-bottom z-50 lg:hidden">
        <div class="flex items-center justify-around px-2 py-2">
            <Link
                v-for="item in navItems"
                :key="item.name"
                :href="route(item.route)"
                class="flex flex-col items-center justify-center min-w-[64px] py-2 px-3 rounded-2xl transition-all touch-manipulation"
                :class="isActive(item.route) 
                    ? 'text-green-600 bg-green-50' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            >
                <component 
                    :is="isActive(item.route) ? item.iconActive : item.icon" 
                    class="h-6 w-6 mb-1"
                />
                <span class="text-xs font-medium">{{ item.name }}</span>
            </Link>
        </div>
    </nav>

    <!-- Spacer for Fixed Bottom Nav -->
    <div class="h-20 lg:hidden"></div>
</template>
