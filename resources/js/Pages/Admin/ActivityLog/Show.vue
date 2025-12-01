<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import BaseCard from '@/Components/Base/BaseCard.vue'
import BaseBadge from '@/Components/Base/BaseBadge.vue'
import BaseButton from '@/Components/Base/BaseButton.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
    activity: Object
})

const { formatDate, formatTime } = useBangladeshFormat()

const getEventVariant = (event) => {
    const variants = {
        'created': 'success',
        'updated': 'info',
        'deleted': 'danger',
        'restored': 'success',
        'login': 'primary',
        'logout': 'default'
    }
    return variants[event] || 'default'
}
</script>

<template>
    <Head :title="`Activity #${activity.id}`" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.activity-log.index')">
                        <BaseButton variant="ghost" :icon="true">
                            <ArrowLeftIcon class="h-5 w-5" />
                        </BaseButton>
                    </Link>
                    <div>
                        <h1 class="text-display-md font-bold text-gray-900">Activity Details</h1>
                        <p class="mt-1 text-gray-600">Activity #{{ activity.id }}</p>
                    </div>
                </div>
            </div>

            <!-- Activity Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Main Information -->
                <BaseCard padding="lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Main Information</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Event</dt>
                            <dd class="mt-1">
                                <BaseBadge :variant="getEventVariant(activity.event)" rounded>
                                    {{ activity.event || 'unknown' }}
                                </BaseBadge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activity.description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Log Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ activity.log_name || 'default' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ formatDate(activity.created_at) }} at {{ formatTime(activity.created_at) }}
                            </dd>
                        </div>
                        <div v-if="activity.batch_uuid">
                            <dt class="text-sm font-medium text-gray-500">Batch UUID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ activity.batch_uuid }}</dd>
                        </div>
                    </dl>
                </BaseCard>

                <!-- Subject & Causer -->
                <div class="space-y-6">
                    <!-- Subject -->
                    <BaseCard padding="lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subject</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ activity.subject_type ? activity.subject_type.split('\\').pop() : 'N/A' }}
                                </dd>
                            </div>
                            <div v-if="activity.subject_id">
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ activity.subject_id }}</dd>
                            </div>
                        </dl>
                    </BaseCard>

                    <!-- Causer -->
                    <BaseCard padding="lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Performed By</h2>
                        <div v-if="activity.causer">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ activity.causer.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ activity.causer.email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ activity.causer.id }}</dd>
                                </div>
                            </dl>
                        </div>
                        <p v-else class="text-sm text-gray-500">System action</p>
                    </BaseCard>
                </div>
            </div>

            <!-- Properties -->
            <BaseCard v-if="activity.properties && Object.keys(activity.properties).length > 0" padding="lg">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Properties</h2>
                <div class="bg-gray-50 rounded-lg p-4 overflow-auto">
                    <pre class="text-sm text-gray-900">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                </div>
            </BaseCard>
        </div>
    </AdminLayout>
</template>
