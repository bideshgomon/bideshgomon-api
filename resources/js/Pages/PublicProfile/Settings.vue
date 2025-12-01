<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
    totalViews: Number,
    recentViews: Array,
});

const form = useForm({
    public_profile_slug: props.user.public_profile_slug || '',
    profile_is_public: props.user.profile_is_public || false,
    profile_headline: props.user.profile_headline || '',
    profile_bio: props.user.profile_bio || '',
    profile_visibility_settings: props.user.profile_visibility_settings || {},
});

const copied = ref(false);

const copyProfileUrl = () => {
    navigator.clipboard.writeText(props.user.public_profile_url);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
};

const submit = () => {
    form.post(route('profile.public.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Public Profile Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Public Profile Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Stats Card -->
                        <div class="mb-6 rounded-lg bg-blue-600 p-6 text-white border-2 border-blue-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">Profile Views</h3>
                                    <p class="text-3xl font-bold">{{ totalViews }}</p>
                                </div>
                                <svg class="h-16 w-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Settings Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Enable Public Profile -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="form.profile_is_public"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <span class="ml-3">
                                        <span class="font-medium text-gray-900">Enable Public Profile</span>
                                        <span class="block text-sm text-gray-500">Allow others to view your profile via a public link</span>
                                    </span>
                                </label>
                            </div>

                            <div v-if="form.profile_is_public" class="space-y-6">
                                <!-- Profile URL -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Custom Profile URL
                                    </label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                                            /profile/
                                        </span>
                                        <TextInput
                                            v-model="form.public_profile_slug"
                                            type="text"
                                            class="flex-1 rounded-none rounded-r-md"
                                            placeholder="your-name"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Only lowercase letters, numbers, and hyphens allowed
                                    </p>
                                </div>

                                <!-- Profile URL Display -->
                                <div v-if="user.public_profile_url" class="rounded-lg bg-blue-50 p-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Your Public Profile URL
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="text"
                                            :value="user.public_profile_url"
                                            readonly
                                            class="flex-1 rounded-md border-gray-300 bg-white shadow-sm"
                                        />
                                        <SecondaryButton @click="copyProfileUrl" type="button">
                                            {{ copied ? '✓ Copied!' : 'Copy' }}
                                        </SecondaryButton>
                                    </div>
                                </div>

                                <!-- Headline -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Professional Headline
                                    </label>
                                    <TextInput
                                        v-model="form.profile_headline"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="e.g., Senior Software Engineer | Full Stack Developer"
                                    />
                                </div>

                                <!-- Bio -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        About Me
                                    </label>
                                    <textarea
                                        v-model="form.profile_bio"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Tell others about yourself..."
                                    ></textarea>
                                </div>

                                <!-- Visibility Settings -->
                                <div class="rounded-lg border border-gray-200 p-4">
                                    <h3 class="mb-4 font-medium text-gray-900">What to show on your public profile</h3>
                                    <div class="space-y-3">
                                        <label v-for="(value, key) in form.profile_visibility_settings" :key="key" class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.profile_visibility_settings[key]"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <span class="ml-3 text-sm text-gray-700 capitalize">
                                                {{ (key || '').replace(/_/g, ' ') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-3">
                                <PrimaryButton :disabled="form.processing">
                                    Save Settings
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- Recent Views -->
                        <div v-if="recentViews && recentViews.length > 0" class="mt-8">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Recent Profile Views</h3>
                            <div class="overflow-hidden rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Location
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                IP Address
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="(view, index) in recentViews" :key="index">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                {{ view.city }}, {{ view.country }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ view.ip_address }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ view.viewed_at }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
