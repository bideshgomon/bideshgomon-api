<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { trans, locale } = useTranslations();

// Example data that would normally come from backend
const exampleData = {
    userName: 'Ahmed Khan',
    serviceCount: 5,
    pendingApplications: 3
};

// SVG flags for better display
const flagUK = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="inline w-6 h-4">
    <clipPath id="s"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
    <clipPath id="t"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
    <g clip-path="url(#s)">
        <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
        <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
        <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#C8102E" stroke-width="4"/>
        <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
        <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
    </g>
</svg>`;

const flagBD = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600" class="inline w-6 h-4">
    <rect width="1000" height="600" fill="#006a4e"/>
    <circle cx="450" cy="300" r="200" fill="#f42a41"/>
</svg>`;
</script>

<template>
    <Head title="Translation Demo" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Current Language Display -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            🌐 Multi-Language Demo
                        </h2>
                        <div class="flex items-center gap-4">
                            <span class="text-gray-700 dark:text-gray-300">Current Language:</span>
                            <span class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-lg font-semibold flex items-center gap-2">
                                <span v-if="locale === 'en'" v-html="flagUK"></span>
                                <span v-else v-html="flagBD"></span>
                                <span>{{ locale === 'en' ? 'English' : 'বাংলা' }}</span>
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                (Use the language switcher in the top right corner)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Translations -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            📋 Navigation Labels
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dashboard</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ trans('ui.dashboard') }}
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Users</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ trans('ui.users') }}
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Services</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ trans('ui.services') }}
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Settings</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ trans('ui.settings') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            ⚡ Action Buttons
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                {{ trans('ui.create') }}
                            </button>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                {{ trans('ui.save') }}
                            </button>
                            <button class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                {{ trans('ui.edit') }}
                            </button>
                            <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                {{ trans('ui.delete') }}
                            </button>
                            <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                {{ trans('ui.cancel') }}
                            </button>
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                {{ trans('ui.search') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Labels -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            🏷️ Status Labels
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-sm">
                                {{ trans('ui.active') }}
                            </span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full text-sm">
                                {{ trans('ui.inactive') }}
                            </span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-sm">
                                {{ trans('ui.pending') }}
                            </span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm">
                                {{ trans('ui.approved') }}
                            </span>
                            <span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full text-sm">
                                {{ trans('ui.rejected') }}
                            </span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full text-sm">
                                {{ trans('ui.completed') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            💬 Messages
                        </h3>
                        <div class="space-y-3">
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded">
                                <p class="text-green-800 dark:text-green-200">
                                    ✓ {{ trans('ui.success') }}
                                </p>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded">
                                <p class="text-red-800 dark:text-red-200">
                                    ✗ {{ trans('ui.error') }}
                                </p>
                            </div>
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    ⚠ {{ trans('ui.warning') }}
                                </p>
                            </div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                                <p class="text-blue-800 dark:text-blue-200">
                                    ℹ {{ trans('ui.info') }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 border-l-4 border-gray-500 rounded">
                                <p class="text-gray-800 dark:text-gray-200">
                                    ⏳ {{ trans('ui.loading') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Labels -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            📝 Form Example
                        </h3>
                        <form class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ trans('ui.name') }}
                                </label>
                                <input 
                                    type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :placeholder="trans('ui.name')"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ trans('ui.email') }}
                                </label>
                                <input 
                                    type="email" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :placeholder="trans('ui.email')"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ trans('ui.phone') }}
                                </label>
                                <input 
                                    type="tel" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :placeholder="trans('ui.phone')"
                                >
                            </div>
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                >
                                    {{ trans('ui.submit') }}
                                </button>
                                <button 
                                    type="button" 
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                                >
                                    {{ trans('ui.cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Code Examples -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            💻 How It Works
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Vue Example -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">In Vue Components:</h4>
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;script setup&gt;
import { useTranslations } from '@/Composables/useTranslations';
const { trans } = useTranslations();
&lt;/script&gt;

&lt;template&gt;
  &lt;h1&gt;&#123;&#123; trans('ui.dashboard') &#125;&#125;&lt;/h1&gt;
  &lt;button&gt;&#123;&#123; trans('ui.save') &#125;&#125;&lt;/button&gt;
&lt;/template&gt;</code></pre>
                            </div>

                            <!-- PHP Example -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">In Blade Templates:</h4>
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;h1&gt;&#123;&#123; __('ui.dashboard') &#125;&#125;&lt;/h1&gt;
&lt;button&gt;&#123;&#123; __('ui.save') &#125;&#125;&lt;/button&gt;</code></pre>
                            </div>

                            <!-- Controller Example -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">In Controllers:</h4>
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>return back()-&gt;with('success', __('ui.success'));

$request-&gt;validate([
    'name' =&gt; 'required',
], [
    'name.required' =&gt; __('validation.required', ['attribute' =&gt; 'name'])
]);</code></pre>
                            </div>

                            <!-- Language Switching -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Automatic Language Detection:</h4>
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>1. URL Parameter: ?lang=bn
2. Session Storage: $_SESSION['locale']
3. User Database: $user-&gt;language
4. Browser Header: Accept-Language
5. Default: config('app.locale')</code></pre>
                            </div>
                        </div>

                        <!-- Pro Tip -->
                        <div class="mt-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded">
                            <p class="text-sm text-indigo-800 dark:text-indigo-200">
                                <span class="font-semibold">💡 Pro Tip:</span>
                                {{ locale === 'en' 
                                    ? 'Switch to Bengali using the language switcher in the top right corner to see all these labels change instantly!'
                                    : 'ইংরেজিতে স্যুইচ করতে উপরের ডানদিকে ভাষা স্যুইচার ব্যবহার করুন!' 
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
