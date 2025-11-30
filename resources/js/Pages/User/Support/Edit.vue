<template>
    <AuthenticatedLayout>
        <Head title="Edit Support Ticket" />

        <div class="py-rhythm-xl">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <RhythmicCard variant="ocean" class="mb-rhythm-lg">
                    <template #icon>
                        <PencilIcon class="w-6 h-6 text-white" />
                    </template>
                    <template #title>
                        <h2 class="font-display font-bold text-2xl text-gray-800">Edit Support Ticket</h2>
                    </template>
                    <template #description>
                        <p class="text-gray-600">Update your ticket details</p>
                    </template>
                    <template #footer>
                        <Link :href="route('support.show', ticket.id)" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <ArrowLeftIcon class="w-4 h-4" />
                            Back to Ticket
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
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-700 mb-rhythm-xs">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="category"
                                v-model="form.category"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-ocean-500 focus:ring-ocean-500 transition-colors"
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
                            <p v-if="form.errors.category" class="mt-rhythm-xs text-sm text-red-600">{{ form.errors.category }}</p>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-semibold text-gray-700 mb-rhythm-xs">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-ocean-500 focus:ring-ocean-500 transition-colors"
                                :class="{ 'border-red-500': form.errors.priority }"
                                required
                            >
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <p v-if="form.errors.priority" class="mt-rhythm-xs text-sm text-red-600">{{ form.errors.priority }}</p>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-rhythm-xs">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="6"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-ocean-500 focus:ring-ocean-500 transition-colors"
                                :class="{ 'border-red-500': form.errors.message }"
                                required
                                placeholder="Please describe your issue in detail..."
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-rhythm-xs text-sm text-red-600">{{ form.errors.message }}</p>
                        </div>

                        <!-- Info Note -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-rhythm-sm">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> You can only edit open tickets. Attachments cannot be modified - please add new attachments via a reply if needed.
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-rhythm-sm pt-rhythm-md border-t border-gray-200">
                            <Link :href="route('support.show', ticket.id)">
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
                                    <PencilIcon class="w-5 h-5" />
                                </template>
                                {{ form.processing ? 'Updating...' : 'Update Ticket' }}
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
import RhythmicCard from '@/Components/RhythmicCard.vue';
import FlowButton from '@/Components/FlowButton.vue';
import { PencilIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    ticket: {
        type: Object,
        required: true
    }
});

const form = useForm({
    subject: props.ticket.subject,
    message: props.ticket.message,
    category: props.ticket.category,
    priority: props.ticket.priority,
});

const submit = () => {
    form.put(route('support.update', props.ticket.id), {
        onSuccess: () => {
            // Redirect handled by controller
        }
    });
};
</script>
