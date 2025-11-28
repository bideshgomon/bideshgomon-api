<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { QuestionMarkCircleIcon, MagnifyingGlassIcon, FunnelIcon, XMarkIcon, ChevronDownIcon, HandThumbUpIcon, HandThumbDownIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

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
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <QuestionMarkCircleIcon class="w-8 h-8 text-indigo-600" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">FAQs</h1>
                                <p class="text-sm text-gray-600">Find answers to common questions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <MagnifyingGlassIcon class="w-4 h-4 inline mr-1" />
                                    Search FAQs
                                </label>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search questions or answers..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @keyup.enter="applyFilters"
                                />
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <FunnelIcon class="w-4 h-4 inline mr-1" />
                                    Category
                                </label>
                                <select
                                    v-model="selectedCategory"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        <div class="flex gap-3 mt-4">
                            <button
                                @click="applyFilters"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                            >
                                <MagnifyingGlassIcon class="w-4 h-4" />
                                Search
                            </button>
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                <XMarkIcon class="w-4 h-4" />
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FAQ Categories (Quick Links) -->
                <div v-if="!selectedCategory && categories.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Browse by Category</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <button
                                v-for="category in categories"
                                :key="category.id"
                                @click="selectedCategory = category.id; applyFilters()"
                                class="p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all text-left group"
                            >
                                <div class="flex items-center gap-3">
                                    <div v-if="category.icon" class="text-2xl">{{ category.icon }}</div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700">{{ category.name }}</h3>
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
                            <QuestionMarkCircleIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No FAQs found</h3>
                            <p class="mt-1 text-sm text-gray-500">No FAQs match your search criteria.</p>
                            <button
                                @click="clearFilters"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                            >
                                <XMarkIcon class="w-4 h-4" />
                                Clear filters
                            </button>
                        </div>

                        <!-- FAQ Items -->
                        <div v-else class="space-y-3">
                            <div
                                v-for="faq in faqs"
                                :key="faq.id"
                                class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors"
                            >
                                <!-- Question Header -->
                                <button
                                    @click="toggleFaq(faq.id)"
                                    class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex-1">
                                        <h3 class="text-base font-semibold text-gray-900">{{ faq.question }}</h3>
                                        <div class="flex items-center gap-3 mt-2 text-sm text-gray-500">
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-md text-xs font-medium">
                                                {{ faq.category?.name }}
                                            </span>
                                            <span class="text-xs">{{ faq.view_count }} views</span>
                                            <span v-if="faq.helpful_count > 0" class="flex items-center gap-1 text-green-600 text-xs">
                                                <HandThumbUpIcon class="w-3 h-3" />
                                                {{ faq.helpful_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <ChevronDownIcon
                                        class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4"
                                        :class="{ 'rotate-180': isFaqExpanded(faq.id) }"
                                    />
                                </button>

                                <!-- Answer Content -->
                                <div
                                    v-show="isFaqExpanded(faq.id)"
                                    class="px-6 py-4 bg-indigo-50 border-t border-indigo-100"
                                >
                                    <div class="prose prose-sm max-w-none text-gray-700 mb-4" v-html="faq.answer"></div>

                                    <!-- Helpful Feedback -->
                                    <div class="pt-4 border-t border-indigo-200">
                                        <p class="text-sm font-medium text-gray-700 mb-3">Was this helpful?</p>
                                        <div class="flex gap-2">
                                            <button
                                                @click="markHelpful(faq.id)"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm font-medium transition-colors"
                                            >
                                                <HandThumbUpIcon class="w-4 h-4" />
                                                Yes
                                            </button>
                                            <button
                                                @click="markNotHelpful(faq.id)"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm font-medium transition-colors"
                                            >
                                                <HandThumbDownIcon class="w-4 h-4" />
                                                No
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support CTA -->
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-6 mt-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <ChatBubbleLeftRightIcon class="w-6 h-6 text-indigo-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Still have questions?</h3>
                                <p class="text-sm text-gray-600 mt-1">Our support team is here to help you.</p>
                            </div>
                        </div>
                        <Link
                            :href="route('support.create')"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors"
                        >
                            <ChatBubbleLeftRightIcon class="w-5 h-5" />
                            Contact Support
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
