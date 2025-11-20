<template>
    <AuthenticatedLayout>
        <Head title="Translation Request Details" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Request Details</h1>
                    <p class="mt-2 text-gray-600">{{ request.request_reference }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Translation Details</h2>
                            <dl class="grid grid-cols-2 gap-4">
                                <div><dt class="text-sm text-gray-600">Source</dt><dd class="text-sm font-medium">{{ request.source_language }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Target</dt><dd class="text-sm font-medium">{{ request.target_language }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Type</dt><dd class="text-sm font-medium">{{ request.document_type }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Certification</dt><dd class="text-sm font-medium">{{ request.certification_type }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Pages</dt><dd class="text-sm font-medium">{{ request.page_count }}</dd></div>
                                <div><dt class="text-sm text-gray-600">Urgency</dt><dd class="text-sm font-medium">{{ request.urgency }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <div>
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Cost Breakdown</h2>
                            <dl class="space-y-2">
                                <div class="flex justify-between"><dt class="text-sm">Base Price</dt><dd class="font-medium">৳{{ request.price_per_page * request.page_count }}</dd></div>
                                <div class="flex justify-between"><dt class="text-sm">Certification</dt><dd class="font-medium">৳{{ request.certification_fee }}</dd></div>
                                <div class="flex justify-between"><dt class="text-sm">Urgency</dt><dd class="font-medium">৳{{ request.urgency_fee }}</dd></div>
                                <div class="flex justify-between pt-2 border-t"><dt class="font-semibold">Total</dt><dd class="text-xl font-bold text-indigo-600">৳{{ request.total_amount }}</dd></div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ request: Object });
</script>
