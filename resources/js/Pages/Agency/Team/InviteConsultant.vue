<template>
    <Head title="Invite Consultant" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Invite Consultant</h2>
                            <Link :href="route('agency.team.index')" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                ← Back to Team
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.email }"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                <p class="mt-1 text-sm text-gray-500">
                                    If this email already exists in the system, the user will be directly assigned. Otherwise, an invitation email will be sent.
                                </p>
                            </div>

                            <!-- Position -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700">
                                    Position/Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="position"
                                    v-model="form.position"
                                    type="text"
                                    required
                                    placeholder="e.g., Senior Consultant, Visa Specialist"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.position }"
                                />
                                <p v-if="form.errors.position" class="mt-1 text-sm text-red-600">{{ form.errors.position }}</p>
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.role }"
                                >
                                    <option value="">Select role</option>
                                    <option value="manager">Manager (Full Access)</option>
                                    <option value="consultant">Consultant (Limited Access)</option>
                                    <option value="support">Support (Read-Only)</option>
                                </select>
                                <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">{{ form.errors.role }}</p>
                                
                                <!-- Role descriptions -->
                                <div class="mt-2 p-3 bg-gray-50 rounded-md text-sm">
                                    <p v-if="form.role === 'manager'" class="text-gray-700">
                                        <strong>Manager:</strong> Full access to all agency features including financials, team management, and profile editing.
                                    </p>
                                    <p v-else-if="form.role === 'consultant'" class="text-gray-700">
                                        <strong>Consultant:</strong> Can view applications and submit quotes, but cannot access financials or manage team.
                                    </p>
                                    <p v-else-if="form.role === 'support'" class="text-gray-700">
                                        <strong>Support:</strong> Read-only access to view applications. Cannot submit quotes or access financials.
                                    </p>
                                    <p v-else class="text-gray-500">Select a role to see permissions</p>
                                </div>
                            </div>

                            <!-- Submit buttons -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Send Invitation</span>
                                </button>
                                <Link
                                    :href="route('agency.team.index')"
                                    class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Cancel
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = useForm({
    name: '',
    email: '',
    position: '',
    role: 'consultant',
})

function submit() {
    form.post(route('agency.team.invite.store'))
}
</script>
