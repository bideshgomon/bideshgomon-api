<template>
    <AuthenticatedLayout>
        <Head title="Request Translation" />
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">New Translation Request</h1>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Source Language *</label>
                                <input v-model="form.source_language" type="text" required class="w-full px-4 py-2 border rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Language *</label>
                                <input v-model="form.target_language" type="text" required class="w-full px-4 py-2 border rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                                <select v-model="form.document_type" required class="w-full px-4 py-2 border rounded-lg">
                                    <option value="legal">Legal</option>
                                    <option value="academic">Academic</option>
                                    <option value="business">Business</option>
                                    <option value="medical">Medical</option>
                                    <option value="certificate">Certificate</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Certification *</label>
                                <select v-model="form.certification_type" required class="w-full px-4 py-2 border rounded-lg">
                                    <option value="standard">Standard (৳0)</option>
                                    <option value="certified">Certified (৳1000)</option>
                                    <option value="notarized">Notarized (৳2000)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Page Count *</label>
                                <input v-model.number="form.page_count" type="number" min="1" required class="w-full px-4 py-2 border rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price per Page *</label>
                                <input v-model.number="form.price_per_page" type="number" min="0" required class="w-full px-4 py-2 border rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Urgency *</label>
                                <select v-model="form.urgency" required class="w-full px-4 py-2 border rounded-lg">
                                    <option value="standard">Standard - 5 days</option>
                                    <option value="express">Express - 3 days (৳800)</option>
                                    <option value="urgent">Urgent - 2 days (৳1500)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Required By</label>
                                <input v-model="form.required_by" type="date" class="w-full px-4 py-2 border rounded-lg" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <Link :href="route('translation.index')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    source_language: '', target_language: '', document_type: 'legal',
    certification_type: 'standard', page_count: 1, urgency: 'standard',
    price_per_page: 250, required_by: ''
});

const submit = () => form.post(route('translation.store'));
</script>
