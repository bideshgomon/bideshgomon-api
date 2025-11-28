<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    MagnifyingGlassIcon, PlusIcon, FunnelIcon, QuestionMarkCircleIcon,
    EyeIcon, PencilIcon, TrashIcon, CheckCircleIcon, XCircleIcon,
    ArrowsUpDownIcon, ChartBarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    faqs: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const category_id = ref(props.filters?.category_id || '');
const status = ref(props.filters?.status || '');
const showFilters = ref(false);

const performSearch = () => {
    router.get(route('admin.faqs.index'), {
        search: search.value,
        category_id: category_id.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    category_id.value = '';
    status.value = '';
    performSearch();
};

const hasActiveFilters = computed(() => {
    return search.value || category_id.value || status.value;
});

const deleteFaq = (faqId) => {
    if (confirm('Delete this FAQ? This action cannot be undone.')) {
        router.delete(route('admin.faqs.destroy', faqId));
    }
};

const togglePublished = (faqId) => {
    router.patch(route('admin.faqs.update', faqId), {
        _method: 'PATCH',
    });
};

const getCategoryColor = (categoryName) => {
    const colors = [
        'bg-blue-100 text-blue-700',
        'bg-purple-100 text-purple-700',
        'bg-green-100 text-green-700',
        'bg-orange-100 text-orange-700',
        'bg-indigo-100 text-indigo-700',
        'bg-pink-100 text-pink-700',
    ];
    const hash = categoryName?.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) || 0;
    return colors[hash % colors.length];
};

const getHelpfulColor = (faq) => {
    const total = (faq.helpful_count || 0) + (faq.not_helpful_count || 0);
    if (total === 0) return 'text-gray-400';
    const percentage = (faq.helpful_count / total) * 100;
    if (percentage >= 70) return 'text-green-600';
    if (percentage >= 40) return 'text-yellow-600';
    return 'text-red-600';
};

const getHelpfulPercentage = (faq) => {
    const total = (faq.helpful_count || 0) + (faq.not_helpful_count || 0);
    if (total === 0) return 0;
    return Math.round((faq.helpful_count / total) * 100);
};
</script>

<template>
    <Head title="Manage FAQs" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 pb-12">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">FAQ Management</h1>
                            <p class="mt-2 text-gray-600">Manage frequently asked questions and answers</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <Link
                                :href="route('admin.faq-categories.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all"
                            >
                                Manage Categories
                            </Link>
                            <Link
                                :href="route('admin.faqs.create')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Create FAQ
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col space-y-4">
                        <!-- Search Bar -->
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 relative">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="search"
                                    @keyup.enter="performSearch"
                                    type="text"
                                    placeholder="Search FAQs by question or answer..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                            </div>
                            <button
                                @click="performSearch"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                            >
                                Search
                            </button>
                            <button
                                @click="showFilters = !showFilters"
                                class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium flex items-center"
                            >
                                <FunnelIcon class="h-5 w-5 mr-2" />
                                Filters
                            </button>
                        </div>

                        <!-- Filters Panel -->
                        <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select
                                    v-model="category_id"
                                    @change="performSearch"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select
                                    v-model="status"
                                    @change="performSearch"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Status</option>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button
                                    v-if="hasActiveFilters"
                                    @click="clearFilters"
                                    class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQs List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div v-if="faqs.data.length === 0" class="text-center py-12">
                        <QuestionMarkCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No FAQs found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new FAQ.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.faqs.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Create FAQ
                            </Link>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Question
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stats
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="faq in faqs.data" :key="faq.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <ArrowsUpDownIcon class="h-4 w-4 text-gray-400 mr-2" />
                                            <span class="text-sm font-medium text-gray-900">{{ faq.order }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <div class="text-sm font-medium text-gray-900">{{ faq.question }}</div>
                                            <div class="text-sm text-gray-500 line-clamp-2 mt-1">{{ faq.answer }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            v-if="faq.category"
                                            :class="getCategoryColor(faq.category.name)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full"
                                        >
                                            {{ faq.category.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <div class="flex items-center text-xs text-gray-500">
                                                <EyeIcon class="h-3 w-3 mr-1" />
                                                {{ faq.view_count || 0 }} views
                                            </div>
                                            <div class="flex items-center text-xs" :class="getHelpfulColor(faq)">
                                                <ChartBarIcon class="h-3 w-3 mr-1" />
                                                {{ getHelpfulPercentage(faq) }}% helpful
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ faq.helpful_count || 0 }} 👍 / {{ faq.not_helpful_count || 0 }} 👎
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="faq.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                            class="px-2 py-1 text-xs font-semibold rounded-full"
                                        >
                                            {{ faq.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('admin.faqs.edit', faq.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit"
                                            >
                                                <PencilIcon class="h-5 w-5" />
                                            </Link>
                                            <button
                                                @click="togglePublished(faq.id)"
                                                :class="faq.is_published ? 'text-green-600' : 'text-gray-400'"
                                                class="hover:text-green-900"
                                                title="Toggle Published"
                                            >
                                                <CheckCircleIcon class="h-5 w-5" />
                                            </button>
                                            <button
                                                @click="deleteFaq(faq.id)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="faqs.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ faqs.from }} to {{ faqs.to }} of {{ faqs.total }} FAQs
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in faqs.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="{
                                        'bg-indigo-600 text-white': link.active,
                                        'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                        'pointer-events-none opacity-50': !link.url
                                    }"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
