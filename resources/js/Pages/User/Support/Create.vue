<template>
    <AuthenticatedLayout>
        <Head title="Create Support Ticket" />

        <div class="py-rhythm-xl">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <RhythmicCard variant="gradient" class="mb-rhythm-lg">
                    <template #icon>
                        <PlusCircleIcon class="w-6 h-6 text-white" />
                    </template>
                    <template #title>
                        <h2 class="font-display font-bold text-2xl text-gray-800">Create Support Ticket</h2>
                    </template>
                    <template #description>
                        <p class="text-gray-600">Describe your issue and our team will assist you</p>
                    </template>
                    <template #footer>
                        <Link :href="route('support.index')" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <ArrowLeftIcon class="w-4 h-4" />
                            Back to Tickets
                        </Link>
                    </template>
                </RhythmicCard>

                <RhythmicCard variant="default">

                        <form @submit.prevent="submit" class="space-y-rhythm-md">
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-rhythm-xs">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="subject"
                                    v-model="form.subject"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-ocean-500 focus:ring-ocean-500 transition-colors"
                                    :class="{ 'border-red-500': form.errors.subject }"
                                    required
                                    placeholder="Brief description of your issue"
                                />
                                <p v-if="form.errors.subject" class="mt-rhythm-xs text-sm text-red-600">{{ form.errors.subject }}</p>
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="category"
                                    v-model="form.category"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.category }"
                                    required
                                >
                                    <option value="">Select Category</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="billing">Billing & Payment</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="service_inquiry">Service Inquiry</option>
                                    <option value="complaint">Complaint</option>
                                </select>
                                <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                            </div>

                            <!-- Priority -->
                            <div class="mb-4">
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="priority"
                                    v-model="form.priority"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.priority }"
                                    required
                                >
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <p v-if="form.errors.priority" class="mt-1 text-sm text-red-600">{{ form.errors.priority }}</p>
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="6"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.message }"
                                    required
                                    placeholder="Please describe your issue in detail..."
                                ></textarea>
                                <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
                            </div>

                            <!-- Attachments -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Attachments (Optional)
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileUpload"
                                    multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Max 5 files, 10MB each. Allowed: PDF, DOC, DOCX, JPG, PNG
                                </p>
                                <p v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">{{ form.errors.attachments }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-rhythm-sm pt-rhythm-md border-t border-gray-200">
                                <Link :href="route('support.index')">
                                    <FlowButton variant="secondary">
                                        Cancel
                                    </FlowButton>
                                </Link>
                                <FlowButton
                                    type="submit"
                                    variant="primary"
                                    :loading="form.processing"
                                >
                                    <template #icon>
                                        <PlusCircleIcon class="w-5 h-5" />
                                    </template>
                                    {{ form.processing ? 'Creating...' : 'Create Ticket' }}
                                </FlowButton>
                            </div>
                        </form>
                    </RhythmicCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { PlusCircleIcon, ArrowLeftIcon } from '@heroicons/vue/24/solid';

const form = useForm({
    subject: '',
    category: '',
    priority: 'normal',
    message: '',
    attachments: [],
});

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    if (files.length > 5) {
        alert('Maximum 5 files allowed');
        event.target.value = '';
        return;
    }
    form.attachments = files;
};

const submit = () => {
    form.post(route('support.store'), {
        preserveScroll: true,
    });
};
</script>
