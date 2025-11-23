<template>
    <div class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Advanced Filters</h3>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search In</label>
                <select
                    v-model="localFilters.type"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All</option>
                    
                    <!-- Admin sees everything -->
                    <template v-if="isAdmin">
                        <option value="users">Users</option>
                        <option value="visas">All Visas</option>
                        <option value="tourist_visas">Tourist Visas</option>
                        <option value="work_visas">Work Visas</option>
                        <option value="student_visas">Student Visas</option>
                        <option value="blog">Blog Posts</option>
                        <option value="agencies">Agencies</option>
                    </template>
                    
                    <!-- Regular users see only public data and profile -->
                    <template v-else>
                        <option value="profile">My Profile</option>
                        <option value="blog">Blog Posts</option>
                        <option value="universities">Universities</option>
                        <option value="courses">Courses</option>
                        <option value="jobs">Jobs</option>
                    </template>
                </select>
            </div>

            <!-- Status Filter - Admin Only -->
            <div v-if="isAdmin">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select
                    v-model="localFilters.status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="processing">Processing</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <DateInput
                    v-model="localFilters.date_from"
                    placeholder="DD/MM/YYYY"
                    class="w-full"
                />
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <DateInput
                    v-model="localFilters.date_to"
                    placeholder="DD/MM/YYYY"
                    class="w-full"
                />
            </div>

            <!-- Role Filter (Admin only, for users) -->
            <div v-if="isAdmin && localFilters.type === 'users'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                <select
                    v-model="localFilters.role"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All Roles</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="agency">Agency</option>
                    <option value="consultant">Consultant</option>
                </select>
            </div>

            <!-- Verified Filter (Admin only, for users) -->
            <div v-if="isAdmin && localFilters.type === 'users'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Verification</label>
                <select
                    v-model="localFilters.verified"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All</option>
                    <option :value="true">Verified Only</option>
                    <option :value="false">Unverified Only</option>
                </select>
            </div>

            <!-- Category Filter (for blog - everyone) -->
            <div v-if="localFilters.type === 'blog'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select
                    v-model="localFilters.category_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All Categories</option>
                    <!-- Categories would be loaded from props -->
                </select>
            </div>

            <!-- Featured Filter (for blog) -->
            <div v-if="localFilters.type === 'blog'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Featured</label>
                <select
                    v-model="localFilters.featured"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                    <option value="">All</option>
                    <option :value="true">Featured Only</option>
                    <option :value="false">Not Featured</option>
                </select>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                @click="clearFilters"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
            >
                Clear All
            </button>
            <div class="flex gap-2">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg"
                >
                    Cancel
                </button>
                <button
                    @click="applyFilters"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg"
                >
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import DateInput from '@/Components/DateInput.vue'

const page = usePage()
const isAdmin = computed(() => {
    const role = page.props.auth?.user?.role?.slug
    return role === 'admin' || role === 'super_admin' || role === 'moderator'
})

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:modelValue', 'close', 'apply'])

const localFilters = ref({ ...props.modelValue })

watch(() => props.modelValue, (newVal) => {
    localFilters.value = { ...newVal }
}, { deep: true })

const applyFilters = () => {
    emit('update:modelValue', localFilters.value)
    emit('apply', localFilters.value)
}

const clearFilters = () => {
    localFilters.value = {}
    applyFilters()
}
</script>
