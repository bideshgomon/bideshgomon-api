<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // For boolean settings later
import { ref } from 'vue';

const props = defineProps({
    settings: Object, // Keyed object passed from SettingController@index
});

// Initialize form data dynamically from props
const formData = {};
for (const key in props.settings) {
    formData[key] = props.settings[key].value;
}

const form = useForm({
    settings: formData,
});


const submit = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true, // Keep scroll position on update
        // onSuccess: () => { // Flash message is handled automatically },
    });
};
</script>

<template>
    <Head title="Site Settings" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Site Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="p-6 bg-white border-b border-gray-200 space-y-6">
                            <div v-for="(setting, key) in settings" :key="key">
                                <InputLabel :for="key" :value="setting.label" />

                                <TextInput
                                    :id="key"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.settings[key]"
                                />

                                <InputError class="mt-2" :message="form.errors[`settings.${key}`]" />
                            </div>

                        </div>
                        <div class="px-6 py-4 bg-gray-50 text-right">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Save Settings
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>