<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // <-- Use correct layout
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // <-- For boolean settings
import { ref, computed } from 'vue';

const props = defineProps({
    settings: Object, // Keyed object passed from SettingController@index
});

// Access flash messages
const page = usePage();
const successMessage = computed(() => page.props.flash?.success);

// Initialize form data dynamically from props, wrapping in the expected structure
const formData = {};
for (const key in props.settings) {
    formData[key] = {
        value: props.settings[key].value,
        // Include other properties if needed by the backend, though usually not
        // label: props.settings[key].label,
        // type: props.settings[key].type,
    };
}

const form = useForm({
    settings: formData, // The form data needs to be nested under 'settings' key
});


const submit = () => {
    // Use the PUT route defined in web.php
    form.put(route('admin.settings.update'), {
        preserveScroll: true, // Keep scroll position on update
        // onSuccess: () => { // Flash message is handled automatically by AuthenticatedLayout },
        onError: (errors) => {
            console.error("Error updating settings:", errors);
        },
    });
};
</script>

<template>
    <Head title="Site Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Site Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                 <div
                    v-if="successMessage"
                    class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300 shadow"
                    role="alert"
                >
                    <span class="font-medium">Success!</span> {{ successMessage }}
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                            <header>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">General Settings</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update global site configuration.</p>
                            </header>

                             <div v-for="(setting, key) in settings" :key="key">
                                <InputLabel :for="key" :value="setting.label" />

                                <TextInput
                                    v-if="setting.type === 'text' || setting.type === 'email'"
                                    :id="key"
                                    :type="setting.type"
                                    class="mt-1 block w-full"
                                    v-model="form.settings[key].value"
                                />

                                <div v-else-if="setting.type === 'checkbox'" class="mt-2">
                                     <label class="flex items-center">
                                        <Checkbox :id="key" v-model:checked="form.settings[key].value" />
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Enable</span>
                                    </label>
                                </div>

                                <InputError class="mt-2" :message="form.errors[`settings.${key}.value`]" />
                            </div>

                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 text-right border-t dark:border-gray-600">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Settings' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>