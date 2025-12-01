<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import BaseCard from '@/Components/Base/BaseCard.vue'
import BaseInput from '@/Components/Base/BaseInput.vue'
import BaseSelect from '@/Components/Base/BaseSelect.vue'
import BaseButton from '@/Components/Base/BaseButton.vue'
import BaseBadge from '@/Components/Base/BaseBadge.vue'
import { MagnifyingGlassIcon, FunnelIcon, EyeIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const props = defineProps({
    activities: Object,
    filters: Object,
    subjectTypes: Array,
    events: Array
})

const { formatDate, formatTime } = useBangladeshFormat()

const searchQuery = ref(props.filters?.search || '')
const selectedSubjectType = ref(props.filters?.subject_type || '')
const selectedEvent = ref(props.filters?.event || '')
const fromDate = ref(props.filters?.from_date || '')
const toDate = ref(props.filters?.to_date || '')
const showFilters = ref(false)

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedSubjectType.value || selectedEvent.value || fromDate.value || toDate.value
})

const applyFilters = () => {
    router.get(route('admin.activity-log.index'), {
        search: searchQuery.value,
        subject_type: selectedSubjectType.value,
        event: selectedEvent.value,
        from_date: fromDate.value,
        to_date: toDate.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const clearFilters = () => {
    searchQuery.value = ''
    selectedSubjectType.value = ''
    selectedEvent.value = ''
    fromDate.value = ''
    toDate.value = ''
    applyFilters()
}

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

const getSubjectLabel = (subjectType) => {
    if (!subjectType) return 'N/A'
    return subjectType.split('\\').pop()
}
</script>

<template>
    <Head title="Activity Log" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-display-md font-bold text-gray-900">Activity Log</h1>
                    <p class="mt-1 text-gray-600">Track all system activities and user actions</p>
                </div>
            </div>

            <!-- Filters -->
            <BaseCard padding="lg">
                <div class="space-y-4">
                    <!-- Search Bar -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <BaseInput
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search activities..."
                                :icon="MagnifyingGlassIcon"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <BaseButton
                            variant="outline"
                            @click="showFilters = !showFilters"
                            :icon="true"
                        >
                            <FunnelIcon class="h-5 w-5" />
                        </BaseButton>
                        <BaseButton variant="primary" @click="applyFilters">
                            Search
                        </BaseButton>
                        <BaseButton
                            v-if="hasActiveFilters"
                            variant="ghost"
                            @click="clearFilters"
                        >
                            <XMarkIcon class="h-5 w-5 mr-2" />
                            Clear
                        </BaseButton>
                    </div>

                    <!-- Advanced Filters -->
                    <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t">
                        <BaseSelect
                            v-model="selectedSubjectType"
                            :options="subjectTypes"
                            label="Subject Type"
                            placeholder="All types"
                        />
                        <BaseSelect
                            v-model="selectedEvent"
                            :options="events"
                            label="Event"
                            placeholder="All events"
                        />
                        <BaseInput
                            v-model="fromDate"
                            type="date"
                            label="From Date"
                        />
                        <BaseInput
                            v-model="toDate"
                            type="date"
                            label="To Date"
                        />
                    </div>
                </div>
            </BaseCard>

            <!-- Activity List -->
            <BaseCard padding="none">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <BaseBadge :variant="getEventVariant(activity.event)" rounded>
                                        {{ activity.event || 'unknown' }}
                                    </BaseBadge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ activity.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ getSubjectLabel(activity.subject_type) }}
                                    </div>
                                    <div v-if="activity.subject_id" class="text-xs text-gray-500">
                                        ID: {{ activity.subject_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="activity.causer" class="text-sm">
                                        <div class="font-medium text-gray-900">{{ activity.causer.name }}</div>
                                        <div class="text-gray-500">{{ activity.causer.email }}</div>
                                    </div>
                                    <span v-else class="text-sm text-gray-500">System</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDate(activity.created_at) }}</div>
                                    <div class="text-xs text-gray-500">{{ formatTime(activity.created_at) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.activity-log.show', activity.id)"
                                        class="text-ocean-600 hover:text-ocean-900 inline-flex items-center gap-1"
                                    >
                                        <EyeIcon class="h-4 w-4" />
                                        View
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="activities.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="text-lg font-medium">No activities found</div>
                                    <p class="mt-1">Try adjusting your filters</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="activities.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ activities.from }} to {{ activities.to }} of {{ activities.total }} activities
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in activities.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                                    link.active
                                        ? 'bg-ocean-500 text-white'
                                        : link.url
                                        ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </BaseCard>
        </div>
    </AdminLayout>
</template>
