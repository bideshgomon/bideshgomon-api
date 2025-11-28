<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    MagnifyingGlassIcon, 
    MapPinIcon, 
    BriefcaseIcon,
    CurrencyDollarIcon,
    CalendarIcon,
    FunnelIcon,
    XMarkIcon,
    SparklesIcon,
    ClockIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jobs: Object,
    countries: Array,
    categories: Array,
    jobTypes: Array,
    filters: Object,
    userApplications: Array,
});

const search = ref(props.filters.search || '');
const selectedCountry = ref(props.filters.country_id || '');
const selectedCategory = ref(props.filters.category || '');
const selectedJobType = ref(props.filters.job_type || '');
const salaryMin = ref(props.filters.salary_min || '');
const salaryMax = ref(props.filters.salary_max || '');
const showFilters = ref(false);

const applyFilters = () => {
    router.get('/jobs', {
        search: search.value,
        country_id: selectedCountry.value,
        category: selectedCategory.value,
        job_type: selectedJobType.value,
        salary_min: salaryMin.value,
        salary_max: salaryMax.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    selectedCountry.value = '';
    selectedCategory.value = '';
    selectedJobType.value = '';
    salaryMin.value = '';
    salaryMax.value = '';
    router.get('/jobs');
};

const hasFilters = computed(() => {
    return search.value || selectedCountry.value || selectedCategory.value || selectedJobType.value || salaryMin.value || salaryMax.value;
});

const hasApplied = (jobId) => {
    return props.userApplications.includes(jobId);
};

const formatSalary = (job) => {
    const symbol = job.salary_currency === 'BDT' ? '৳' : job.salary_currency;
    if (job.salary_max && job.salary_max > job.salary_min) {
        return `${symbol}${Number(job.salary_min).toLocaleString()} - ${symbol}${Number(job.salary_max).toLocaleString()}`;
    }
    return `${symbol}${Number(job.salary_min).toLocaleString()}`;
};

const getCategoryColor = (category) => {
    const colors = {
        'hospitality': 'bg-blue-100 text-blue-800',
        'construction': 'bg-orange-100 text-orange-800',
        'healthcare': 'bg-green-100 text-green-800',
        'it': 'bg-purple-100 text-purple-800',
        'manufacturing': 'bg-gray-100 text-gray-800',
        'education': 'bg-indigo-100 text-indigo-800',
        'retail': 'bg-pink-100 text-pink-800',
        'transportation': 'bg-yellow-100 text-yellow-800',
        'security': 'bg-red-100 text-red-800',
        'agriculture': 'bg-lime-100 text-lime-800',
        'logistics': 'bg-cyan-100 text-cyan-800',
        'automotive': 'bg-slate-100 text-slate-800',
    };
    return colors[category] || 'bg-gray-100 text-gray-800';
};

const getJobTypeLabel = (type) => {
    const labels = {
        'full_time': 'Full Time',
        'part_time': 'Part Time',
        'contract': 'Contract',
        'temporary': 'Temporary',
        'seasonal': 'Seasonal',
    };
    return labels[type] || type.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
};
</script>

<template>
    <Head title="Job Opportunities" />

    <AuthenticatedLayout>
        <!-- Mobile Header -->
        <div class="bg-white border-b border-gray-200 px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Job Opportunities</h1>
                        <p class="text-gray-600 text-sm mt-1">{{ jobs.total }} jobs available</p>
                    </div>
                    <Link
                        :href="route('jobs.my-applications')"
                        class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors"
                    >
                        <BriefcaseIcon class="h-5 w-5" />
                        <span class="hidden sm:inline">My Applications</span>
                    </Link>
                </div>

                <!-- Search Bar -->
                <div class="relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input
                        v-model="search"
                        @keyup.enter="applyFilters"
                        type="text"
                        placeholder="Search jobs, companies..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-0 focus:ring-2 focus:ring-white/50"
                    />
                </div>

                <!-- Filter Toggle -->
                <button
                    @click="showFilters = !showFilters"
                    class="mt-3 w-full flex items-center justify-center space-x-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors"
                >
                    <FunnelIcon class="h-5 w-5" />
                    <span>{{ showFilters ? 'Hide Filters' : 'Show Filters' }}</span>
                    <span v-if="hasFilters" class="bg-white/30 px-2 py-0.5 rounded-full text-xs">Active</span>
                </button>
            </div>
        </div>

        <!-- Filters Panel -->
        <div v-if="showFilters" class="bg-white border-b px-4 py-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Country Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <select
                            v-model="selectedCountry"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Countries</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select
                            v-model="selectedCategory"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                            </option>
                        </select>
                    </div>

                    <!-- Job Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
                        <select
                            v-model="selectedJobType"
                            @change="applyFilters"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Types</option>
                            <option v-for="type in jobTypes" :key="type" :value="type">
                                {{ getJobTypeLabel(type) }}
                            </option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="flex items-end">
                        <button
                            v-if="hasFilters"
                            @click="clearFilters"
                            class="w-full flex items-center justify-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors"
                        >
                            <XMarkIcon class="h-5 w-5" />
                            <span>Clear Filters</span>
                        </button>
                    </div>
                    
                    <!-- Salary Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Salary (৳)</label>
                        <input
                            v-model="salaryMin"
                            @change="applyFilters"
                            type="number"
                            min="0"
                            placeholder="e.g. 30000"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Salary (৳)</label>
                        <input
                            v-model="salaryMax"
                            @change="applyFilters"
                            type="number"
                            min="0"
                            placeholder="e.g. 80000"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Listings -->
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- No Results -->
            <div v-if="jobs.data.length === 0" class="text-center py-12">
                <BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-lg font-medium text-gray-900">No jobs found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
            </div>

            <!-- Job Cards Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="job in jobs.data"
                    :key="job.id"
                    :href="route('jobs.show', job.id)"
                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 overflow-hidden"
                >
                    <!-- Featured Badge -->
                    <div v-if="job.is_featured" class="bg-amber-500 px-4 py-1 flex items-center justify-center space-x-1">
                        <SparklesIcon class="h-4 w-4 text-white" />
                        <span class="text-white text-xs font-semibold">FEATURED</span>
                    </div>

                    <div class="p-4">
                        <!-- Company & Location -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg leading-tight mb-1">{{ job.title }}</h3>
                                <p class="text-sm text-gray-600">{{ job.company_name }}</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <MapPinIcon class="h-4 w-4 mr-1" />
                            <span>{{ job.city }}, {{ job.country.name }}</span>
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', getCategoryColor(job.category)]">
                                {{ job.category }}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ getJobTypeLabel(job.job_type) }}
                            </span>
                            <span v-if="job.experience_years" class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ job.experience_years }} yrs
                            </span>
                        </div>

                        <!-- Salary -->
                        <div class="flex items-center text-indigo-600 font-semibold mb-3">
                            <CurrencyDollarIcon class="h-5 w-5 mr-1" />
                            <span>{{ formatSalary(job) }}/{{ job.salary_period }}</span>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="flex items-center text-xs text-gray-500">
                                <CalendarIcon class="h-4 w-4 mr-1" />
                                <span>{{ new Date(job.published_at).toLocaleDateString() }}</span>
                            </div>
                            
                            <div v-if="hasApplied(job.id)" class="flex items-center text-green-600 text-xs font-medium">
                                <CheckCircleIcon class="h-4 w-4 mr-1" />
                                <span>Applied</span>
                            </div>
                            <div v-else-if="job.application_fee > 0" class="text-xs text-gray-600">
                                Fee: ৳{{ Number(job.application_fee).toLocaleString() }}
                            </div>
                            <div v-else class="text-xs text-green-600 font-medium">
                                Free Application
                            </div>
                        </div>

                        <!-- Deadline Warning -->
                        <div v-if="job.application_deadline" class="mt-2 flex items-center text-xs text-orange-600">
                            <ClockIcon class="h-4 w-4 mr-1" />
                            <span>Deadline: {{ new Date(job.application_deadline).toLocaleDateString() }}</span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="jobs.data.length > 0" class="mt-8 flex items-center justify-between border-t border-gray-200 pt-4">
                <div class="text-sm text-gray-700">
                    Showing {{ jobs.from }} to {{ jobs.to }} of {{ jobs.total }} jobs
                </div>
                <div class="flex items-center space-x-2 overflow-x-auto">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="link in jobs.links"
                        :key="link.label"
                        :href="link.url || undefined"
                        :class="[
                            'px-3 py-2 text-sm rounded-lg',
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
    </AuthenticatedLayout>
</template>
