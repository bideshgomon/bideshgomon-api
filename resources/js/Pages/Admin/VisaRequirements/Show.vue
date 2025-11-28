<template>
    <Head :title="`${visaRequirement.country} - ${visaRequirement.visa_type}`" />

    <AdminLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ visaRequirement.country }} - {{ visaRequirement.visa_type }}
                                </h1>
                                <span 
                                    :class="[
                                        'px-3 py-1 text-sm font-medium rounded-full',
                                        visaRequirement.is_active 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ visaRequirement.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="mt-2 text-gray-600">{{ visaRequirement.visa_category || 'No category specified' }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link 
                                :href="route('admin.visa-requirements.edit', visaRequirement.id)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </Link>
                            <Link 
                                :href="route('admin.visa-requirements.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to List
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Documents (Common for All) -->
                        <div v-if="visaRequirement.required_documents && visaRequirement.required_documents.length > 0" class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">Basic Documents (Common for All)</h2>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ visaRequirement.required_documents.length }} documents
                                </span>
                            </div>
                            <ul class="space-y-2">
                                <li 
                                    v-for="(doc, index) in visaRequirement.required_documents" 
                                    :key="index"
                                    class="flex items-start"
                                >
                                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">{{ doc }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Profession-Specific Documents -->
                        <div v-if="visaRequirement.profession_specific_docs && Object.keys(visaRequirement.profession_specific_docs).length > 0" class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">Profession-Specific Documents</h2>
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                                    {{ Object.keys(visaRequirement.profession_specific_docs).length }} professions
                                </span>
                            </div>
                            <div class="space-y-6">
                                <div 
                                    v-for="(docs, profession) in visaRequirement.profession_specific_docs" 
                                    :key="profession"
                                    class="p-4 border-2 border-indigo-100 rounded-lg bg-indigo-50/30"
                                >
                                    <h3 class="font-semibold text-indigo-900 mb-3">{{ profession }}</h3>
                                    <ul class="space-y-2">
                                        <li 
                                            v-for="(doc, index) in (typeof docs === 'string' ? docs.split('\n').filter(d => d.trim()) : docs)" 
                                            :key="index"
                                            class="flex items-start"
                                        >
                                            <svg class="w-5 h-5 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-gray-700">{{ doc }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Eligibility Criteria -->
                        <div v-if="visaRequirement.eligibility_criteria" class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Eligibility Criteria</h2>
                            <div class="prose prose-sm max-w-none text-gray-600 whitespace-pre-line">
                                {{ visaRequirement.eligibility_criteria }}
                            </div>
                        </div>

                        <!-- Financial Requirements -->
                        <div v-if="visaRequirement.financial_requirements || visaRequirement.min_bank_balance" class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Financial Requirements</h2>
                            <div class="space-y-3">
                                <div v-if="visaRequirement.min_bank_balance" class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Minimum Bank Balance:</span>
                                    <span class="font-semibold text-gray-900">৳{{ visaRequirement.min_bank_balance.toLocaleString() }}</span>
                                </div>
                                <div v-if="visaRequirement.bank_statement_months" class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Bank Statement Period:</span>
                                    <span class="font-semibold text-gray-900">{{ visaRequirement.bank_statement_months }} months</span>
                                </div>
                                <div v-if="visaRequirement.financial_requirements" class="mt-4 prose prose-sm max-w-none text-gray-600 whitespace-pre-line">
                                    {{ visaRequirement.financial_requirements }}
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div v-if="visaRequirement.important_notes" class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                                    <div class="mt-2 text-sm text-yellow-700 whitespace-pre-line">
                                        {{ visaRequirement.important_notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Fees Card -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fee Structure</h2>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b-2 border-indigo-200">
                                    <span class="text-gray-700 font-medium">Embassy Fee:</span>
                                    <span class="font-bold text-indigo-600">৳{{ (visaRequirement.government_fee || 0).toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Service Fee:</span>
                                    <span class="font-semibold text-gray-900">৳{{ (visaRequirement.service_fee || 0).toLocaleString() }}</span>
                                </div>
                                <div v-if="visaRequirement.processing_fee_standard" class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Processing Fee:</span>
                                    <span class="font-semibold text-gray-900">৳{{ (visaRequirement.processing_fee_standard || 0).toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-indigo-600 text-white px-3 rounded -mx-3">
                                    <span class="font-bold">Total Amount:</span>
                                    <span class="font-bold text-lg">
                                        ৳{{ ((visaRequirement.government_fee || 0) + (visaRequirement.service_fee || 0) + (visaRequirement.processing_fee_standard || 0)).toLocaleString() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Processing Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Processing Information</h2>
                            <div class="space-y-3">
                                <div v-if="visaRequirement.processing_days_standard">
                                    <span class="text-sm text-gray-600">Processing Time:</span>
                                    <p class="font-medium text-gray-900">{{ visaRequirement.processing_days_standard }} days</p>
                                </div>
                                <div v-if="visaRequirement.processing_time_info">
                                    <span class="text-sm text-gray-600">Time Info:</span>
                                    <p class="font-medium text-gray-900">{{ visaRequirement.processing_time_info }}</p>
                                </div>
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex items-center">
                                        <svg 
                                            :class="[
                                                'w-5 h-5 mr-2',
                                                visaRequirement.interview_required ? 'text-yellow-500' : 'text-green-500'
                                            ]"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">
                                            Interview {{ visaRequirement.interview_required ? 'Required' : 'Not Required' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <svg 
                                            :class="[
                                                'w-5 h-5 mr-2',
                                                visaRequirement.biometrics_required ? 'text-yellow-500' : 'text-green-500'
                                            ]"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">
                                            Biometrics {{ visaRequirement.biometrics_required ? 'Required' : 'Not Required' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                            <div class="space-y-2">
                                <button 
                                    @click="toggleStatus"
                                    :class="[
                                        'w-full px-4 py-2 rounded-md text-white transition',
                                        visaRequirement.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
                                    ]"
                                >
                                    {{ visaRequirement.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <Link 
                                    :href="route('admin.visa-requirements.edit', visaRequirement.id)"
                                    class="block w-full px-4 py-2 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 transition"
                                >
                                    Edit Requirement
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    visaRequirement: Object,
});

const toggleStatus = () => {
    router.post(
        route('admin.visa-requirements.toggle-active', props.visaRequirement.id),
        {},
        {
            preserveScroll: true,
        }
    );
};
</script>
