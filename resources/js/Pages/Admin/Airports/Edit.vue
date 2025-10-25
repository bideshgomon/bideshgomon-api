<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    airport: Object,
    countries: Array,
});

const form = useForm({
    name: props.airport.name,
    code: props.airport.code,
    country_id: props.airport.country_id,
});

const submit = () => {
    form.put(route('admin.api.airports.update', props.airport.id), {
        onSuccess: () => {
            router.visit(route('admin.airports.index'));
        },
    });
};
</script>

<template>
    <Head title="Edit Airport" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold">Edit Airport</h1>
                            <Link :href="route('admin.airports.index')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                Back to List
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="max-w-lg mx-auto">
                            <div>
                                <InputLabel for="name" value="Airport Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="code" value="Airport Code (e.g., DAC, LHR)" />
                                <TextInput
                                    id="code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.code"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.code" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="country_id" value="Country" />
                                <select
                                    id="country_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.country_id"
                                    required
                                >
                                    <option value="" disabled>Select a country</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.country_id" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Airport
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>