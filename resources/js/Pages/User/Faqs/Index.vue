<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    faqs: Object,
    categories: Array,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || '');
const expandedFaqs = ref(new Set());

const filteredFaqs = computed(() => {
    return props.faqs;
});

const toggleFaq = (faqId) => {
    if (expandedFaqs.value.has(faqId)) {
        expandedFaqs.value.delete(faqId);
    } else {
        expandedFaqs.value.add(faqId);
    }
};

const isFaqExpanded = (faqId) => {
    return expandedFaqs.value.has(faqId);
};

const applyFilters = () => {
    router.get(route('faqs.index'), {
        search: searchQuery.value,
        category_id: selectedCategory.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    applyFilters();
};

const markHelpful = (faqId) => {
    router.post(route('faqs.helpful', faqId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success message already handled by backend
        }
    });
};

const markNotHelpful = (faqId) => {
    router.post(route('faqs.not-helpful', faqId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success message already handled by backend
        }
    });
};
</script>

<template>
    <Head title="Frequently Asked Questions" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Frequently Asked Questions</h1>
                        <p class="text-gray-600">Find answers to common questions about our services.</p>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search FAQs</label>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search questions or answers..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @keyup.enter="applyFilters"
                                />
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select
                                    v-model="selectedCategory"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="applyFilters"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }} ({{ category.active_faqs_count || 0 }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-4">
                            <button
                                @click="applyFilters"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            >
                                Search
                            </button>
                            <button
                                @click="clearFilters"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FAQ Categories (Quick Links) -->
                <div v-if="!selectedCategory && categories.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Browse by Category</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="category in categories"
                                :key="category.id"
                                @click="selectedCategory = category.id; applyFilters()"
                                class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all text-left"
                            >
                                <div class="flex items-center gap-3">
                                    <div v-if="category.icon" class="text-2xl">{{ category.icon }}</div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ category.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ category.active_faqs_count || 0 }} questions</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FAQ List (Accordion) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <span v-if="selectedCategory">
                                {{ categories.find(c => c.id == selectedCategory)?.name || 'Category' }} Questions
                            </span>
                            <span v-else>All Questions</span>
                            <span class="text-gray-500 text-base font-normal ml-2">({{ faqs.length }} results)</span>
                        </h2>

                        <!-- Empty State -->
                        <div v-if="faqs.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 text-gray-500">No FAQs found matching your criteria.</p>
                            <button
                                @click="clearFilters"
                                class="mt-4 text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Clear filters and show all FAQs
                            </button>
                        </div>

                        <!-- FAQ Items -->
                        <div v-else class="space-y-4">
                            <div
                                v-for="faq in faqs"
                                :key="faq.id"
                                class="border border-gray-200 rounded-lg overflow-hidden"
                            >
                                <!-- Question Header -->
                                <button
                                    @click="toggleFaq(faq.id)"
                                    class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ faq.question }}</h3>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                                {{ faq.category?.name }}
                                            </span>
                                            <span>{{ faq.view_count }} views</span>
                                            <span v-if="faq.helpful_count > 0" class="text-green-600">
                                                👍 {{ faq.helpful_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <svg
                                        class="w-5 h-5 text-gray-400 transition-transform"
                                        :class="{ 'rotate-180': isFaqExpanded(faq.id) }"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Answer Content -->
                                <div
                                    v-show="isFaqExpanded(faq.id)"
                                    class="px-6 py-4 bg-gray-50 border-t border-gray-200"
                                >
                                    <div class="prose max-w-none text-gray-700 mb-4" v-html="faq.answer"></div>

                                    <!-- Helpful Feedback -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-600 mb-2">Was this helpful?</p>
                                        <div class="flex gap-2">
                                            <button
                                                @click="markHelpful(faq.id)"
                                                class="px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 text-sm font-medium"
                                            >
                                                👍 Yes
                                            </button>
                                            <button
                                                @click="markNotHelpful(faq.id)"
                                                class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 text-sm font-medium"
                                            >
                                                👎 No
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support CTA -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Still have questions?</h3>
                            <p class="text-gray-600 mt-1">Our support team is here to help you.</p>
                        </div>
                        <Link
                            :href="route('support.create')"
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                        >
                            Contact Support
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
