<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = ref(page.props.locale || 'en');
const showDropdown = ref(false);

const languages = [
    { 
        code: 'en', 
        name: 'English', 
        nativeName: 'English',
        flagSvg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-5 h-4">
            <clipPath id="s"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
            <clipPath id="t"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
            <g clip-path="url(#s)">
                <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#C8102E" stroke-width="4"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
            </g>
        </svg>`
    },
    { 
        code: 'bn', 
        name: 'Bengali', 
        nativeName: 'বাংলা',
        flagSvg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600" class="w-5 h-4">
            <rect width="1000" height="600" fill="#006a4e"/>
            <circle cx="450" cy="300" r="200" fill="#f42a41"/>
        </svg>`
    }
];

const currentLanguage = computed(() => {
    return languages.find(lang => lang.code === currentLocale.value) || languages[0];
});

const switchLanguage = (langCode) => {
    if (langCode === currentLocale.value) {
        showDropdown.value = false;
        return;
    }

    // Update locale and reload page
    router.visit(window.location.pathname, {
        data: { lang: langCode },
        preserveState: true,
        preserveScroll: true,
        only: ['locale'],
        onSuccess: () => {
            currentLocale.value = langCode;
            showDropdown.value = false;
            
            // Store preference
            localStorage.setItem('preferred_locale', langCode);
        }
    });
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = document.getElementById('language-dropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        showDropdown.value = false;
    }
};

// Add event listener for clicking outside
if (typeof window !== 'undefined') {
    document.addEventListener('click', handleClickOutside);
}
</script>

<template>
    <div id="language-dropdown" class="relative">
        <button
            @click.stop="toggleDropdown"
            type="button"
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150"
            :class="{ 'bg-gray-100 dark:bg-gray-700': showDropdown }"
        >
            <span v-html="currentLanguage.flagSvg"></span>
            <span class="hidden sm:inline">{{ currentLanguage.nativeName }}</span>
            <svg
                class="w-4 h-4 transition-transform duration-200"
                :class="{ 'rotate-180': showDropdown }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="showDropdown"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
            >
                <div class="py-1">
                    <button
                        v-for="language in languages"
                        :key="language.code"
                        @click="switchLanguage(language.code)"
                        class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                        :class="{
                            'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20': 
                                language.code === currentLocale,
                            'text-gray-700 dark:text-gray-300': 
                                language.code !== currentLocale
                        }"
                    >
                        <span v-html="language.flagSvg"></span>
                        <div class="flex-1 text-left">
                            <div class="font-medium">{{ language.nativeName }}</div>
                            <div class="text-xs opacity-75">{{ language.name }}</div>
                        </div>
                        <svg
                            v-if="language.code === currentLocale"
                            class="w-5 h-5 text-indigo-600 dark:text-indigo-400"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Ensure dropdown appears above other content */
#language-dropdown {
    z-index: 100;
}
</style>
