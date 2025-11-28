<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    MagnifyingGlassIcon, PlusIcon, FunnelIcon, BriefcaseIcon,
    MapPinIcon, CurrencyDollarIcon, EyeIcon, PencilIcon, TrashIcon,
    SparklesIcon, CheckCircleIcon, XCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jobs: Object,
    countries: Array,
    categories: Array,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const country_id = ref(props.filters.country_id || '');
const category = ref(props.filters.category || '');
const status = ref(props.filters.status || '');
const is_featured = ref(props.filters.is_featured || '');
const showFilters = ref(false);
const selectedJobs = ref([]);

const performSearch = () => {
    router.get(route('admin.jobs.index'), {
        search: search.value,
        country_id: country_id.value,
        category: category.value,
        status: status.value,
        is_featured: is_featured.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    country_id.value = '';
    category.value = '';
    status.value = '';
    is_featured.value = '';
    performSearch();
};

const hasActiveFilters = computed(() => {
    return search.value || country_id.value || category.value || status.value || is_featured.value;
});

const toggleJobSelection = (jobId) => {
    const index = selectedJobs.value.indexOf(jobId);
    if (index > -1) {
        selectedJobs.value.splice(index, 1);
    } else {
        selectedJobs.value.push(jobId);
    }
};

const toggleAllJobs = () => {
    if (selectedJobs.value.length === props.jobs.data.length) {
        selectedJobs.value = [];
    } else {
        selectedJobs.value = props.jobs.data.map(job => job.id);
    }
};

const bulkUpdateStatus = (newStatus) => {
    if (selectedJobs.value.length === 0) {
        alert('Please select jobs to update');
        return;
    }
    
    if (confirm(`Update ${selectedJobs.value.length} job(s) to ${newStatus}?`)) {
        router.post(route('admin.jobs.bulk-update-status'), {
            job_ids: selectedJobs.value,
            status: newStatus,
        }, {
            onSuccess: () => selectedJobs.value = [],
        });
    }
};

const bulkDelete = () => {
    if (selectedJobs.value.length === 0) {
        alert('Please select jobs to delete');
        return;
    }
    
    if (confirm(`Delete ${selectedJobs.value.length} job(s)? This action cannot be undone.`)) {
        router.post(route('admin.jobs.bulk-delete'), {
            job_ids: selectedJobs.value,
        }, {
            onSuccess: () => selectedJobs.value = [],
        });
    }
};

const deleteJob = (jobId) => {
    if (confirm('Delete this job posting? This action cannot be undone.')) {
        router.delete(route('admin.jobs.destroy', jobId));
    }
};

const getCategoryColor = (cat) => {
    const colors = {
        'hospitality': 'bg-purple-100 text-purple-700',
        'construction': 'bg-orange-100 text-orange-700',
        'healthcare': 'bg-blue-100 text-blue-700',
        'it': 'bg-green-100 text-green-700',
        'manufacturing': 'bg-gray-100 text-gray-700',
        'education': 'bg-indigo-100 text-indigo-700',
        'retail': 'bg-pink-100 text-pink-700',
        'transportation': 'bg-yellow-100 text-yellow-700',
    };
    return colors[cat.toLowerCase()] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Manage Job Postings" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 pb-12">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Job Postings Management</h1>
                            <p class="mt-2 text-gray-600">Manage all job postings on the platform</p>
                        </div>
                        <Link
                            :href="route('admin.jobs.create')"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-sm"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Create Job
                        </Link>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-gray-600 text-sm">Total Jobs</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ stats.total }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-gray-600 text-sm">Active</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ stats.active }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-gray-600 text-sm">Featured</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ stats.featured }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-gray-600 text-sm">Expired</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ stats.expired }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
                <!-- Search & Filters -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                            <input
                                v-model="search"
                                @keyup.enter="performSearch"
                                type="text"
                                placeholder="Search jobs by title, company, or category..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <button
                            @click="showFilters = !showFilters"
                            class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors"
                        >
                            <FunnelIcon class="h-5 w-5 mr-2" />
                            Filters
                            <span v-if="hasActiveFilters" class="ml-2 px-2 py-0.5 bg-indigo-600 text-white text-xs rounded-full">Active</span>
                        </button>
                    </div>

                    <!-- Expandable Filters -->
                    <div v-if="showFilters" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select
                                    v-model="country_id"
                                    @change="performSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Countries</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select
                                    v-model="category"
                                    @change="performSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select
                                    v-model="status"
                                    @change="performSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                                <select
                                    v-model="is_featured"
                                    @change="performSearch"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Jobs</option>
                                    <option value="1">Featured Only</option>
                                    <option value="0">Non-Featured</option>
                                </select>
                            </div>
                        </div>
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="mt-4 text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                        >
                            Clear all filters
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div v-if="selectedJobs.length > 0" class="bg-indigo-50 rounded-xl p-4 mb-6 flex items-center justify-between">
                    <span class="text-sm font-medium text-indigo-900">{{ selectedJobs.length }} job(s) selected</span>
                    <div class="flex gap-2">
                        <button
                            @click="bulkUpdateStatus('active')"
                            class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                        >
                            Activate
                        </button>
                        <button
                            @click="bulkUpdateStatus('inactive')"
                            class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors"
                        >
                            Deactivate
                        </button>
                        <button
                            @click="bulkDelete"
                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="space-y-4">
                    <div v-if="jobs.data.length === 0" class="bg-white rounded-2xl shadow-sm p-12 text-center">
                        <BriefcaseIcon class="h-16 w-16 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No jobs found</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your filters or create a new job posting</p>
                        <Link
                            :href="route('admin.jobs.create')"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors"
                        >
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Create Job
                        </Link>
                    </div>

                    <div
                        v-for="job in jobs.data"
                        :key="job.id"
                        class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6"
                    >
                        <div class="flex items-start gap-4">
                            <!-- Checkbox -->
                            <input
                                type="checkbox"
                                :checked="selectedJobs.includes(job.id)"
                                @change="toggleJobSelection(job.id)"
                                class="mt-1 h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                            />

                            <!-- Job Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-xl font-bold text-gray-900">{{ job.title }}</h3>
                                            <span v-if="job.is_featured" class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                                                <SparklesIcon class="h-3 w-3 mr-1" />
                                                Featured
                                            </span>
                                            <span v-if="job.is_active" class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                <CheckCircleIcon class="h-3 w-3 mr-1" />
                                                Active
                                            </span>
                                            <span v-else class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                                <XCircleIcon class="h-3 w-3 mr-1" />
                                                Inactive
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mb-3">{{ job.company_name }}</p>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                            <div class="flex items-center">
                                                <MapPinIcon class="h-4 w-4 mr-1" />
                                                {{ job.city }}, {{ job.country?.name }}
                                            </div>
                                            <div class="flex items-center">
                                                <CurrencyDollarIcon class="h-4 w-4 mr-1" />
                                                {{ job.salary_currency }} {{ job.salary_min.toLocaleString() }} - {{ job.salary_max.toLocaleString() }}
                                            </div>
                                            <div class="flex items-center">
                                                <EyeIcon class="h-4 w-4 mr-1" />
                                                {{ job.views_count }} views
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <span :class="getCategoryColor(job.category)" class="px-3 py-1 text-xs font-medium rounded-full">
                                                {{ job.category }}
                                            </span>
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                                {{ job.job_type }}
                                            </span>
                                            <span class="px-3 py-1 bg-gray-50 text-gray-700 text-xs font-medium rounded-full">
                                                {{ job.applications_count }} applications
                                            </span>
                                            <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full">
                                                {{ job.positions_available }} positions
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 ml-4">
                                        <Link
                                            :href="route('admin.jobs.show', job.id)"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="View Details"
                                        >
                                            <EyeIcon class="h-5 w-5" />
                                        </Link>
                                        <Link
                                            :href="route('admin.jobs.edit', job.id)"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit Job"
                                        >
                                            <PencilIcon class="h-5 w-5" />
                                        </Link>
                                        <button
                                            @click="deleteJob(job.id)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete Job"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="jobs.data.length > 0" class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ jobs.from }} to {{ jobs.to }} of {{ jobs.total }} jobs
                    </div>
                    <div class="flex items-center space-x-2">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="link in jobs.links"
                            :key="link.label"
                            :href="link.url || undefined"
                            :class="[
                                'px-4 py-2 text-sm rounded-lg font-medium',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : link.url
                                    ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
