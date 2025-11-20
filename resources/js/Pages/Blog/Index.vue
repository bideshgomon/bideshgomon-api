<template>
    <Head title="Blog" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <h1 class="text-4xl font-bold mb-4">Blog & Guides</h1>
                <p class="text-xl text-blue-100">Expert tips and guides for your travel journey</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search articles..."
                        class="w-full px-4 py-3 pl-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        @input="handleSearch"
                    />
                    <MagnifyingGlassIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Featured Posts -->
                    <div v-if="featuredPosts.length > 0 && !filters.search && !filters.category" class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Articles</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <article
                                v-for="post in featuredPosts"
                                :key="post.id"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition cursor-pointer"
                                @click="$inertia.visit(route('blog.show', post.slug))"
                            >
                                <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600"></div>
                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full" :style="{ backgroundColor: post.category.color + '20', color: post.category.color }">
                                            {{ post.category.name }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ post.title }}</h3>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ post.excerpt }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ formatDate(post.published_at) }}</span>
                                        <span>{{ post.reading_time }} min read</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- All Posts -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ filters.category ? 'Category: ' + filters.category : 'Latest Articles' }}
                            </h2>
                            <span class="text-sm text-gray-500">{{ posts.total }} articles</span>
                        </div>

                        <div v-if="posts.data.length > 0" class="space-y-6">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition cursor-pointer"
                                @click="$inertia.visit(route('blog.show', post.slug))"
                            >
                                <div class="md:flex">
                                    <div class="md:w-1/3 h-48 md:h-auto bg-gradient-to-br from-blue-400 to-indigo-600"></div>
                                    <div class="p-6 md:w-2/3">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full" :style="{ backgroundColor: post.category.color + '20', color: post.category.color }">
                                                {{ post.category.name }}
                                            </span>
                                            <span v-if="post.is_featured" class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                                                Featured
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ post.title }}</h3>
                                        <p class="text-gray-600 mb-4 line-clamp-2">{{ post.excerpt }}</p>
                                        <div class="flex items-center gap-4 text-sm text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <UserCircleIcon class="h-4 w-4" />
                                                <span>{{ post.author.name }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <CalendarIcon class="h-4 w-4" />
                                                <span>{{ formatDate(post.published_at) }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <ClockIcon class="h-4 w-4" />
                                                <span>{{ post.reading_time }} min read</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <EyeIcon class="h-4 w-4" />
                                                <span>{{ formatNumber(post.views_count) }}</span>
                                            </div>
                                        </div>
                                        <div v-if="post.tags.length > 0" class="flex flex-wrap gap-2 mt-4">
                                            <span
                                                v-for="tag in post.tags.slice(0, 3)"
                                                :key="tag.id"
                                                class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded cursor-pointer hover:bg-gray-200"
                                                @click.stop="filterByTag(tag.slug)"
                                            >
                                                #{{ tag.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div v-else class="text-center py-12">
                            <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No articles found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="posts.last_page > 1" class="mt-8 flex justify-center">
                            <nav class="flex items-center gap-2">
                                <Link
                                    v-if="posts.prev_page_url"
                                    :href="posts.prev_page_url"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <span class="px-4 py-2 text-sm text-gray-700">
                                    Page {{ posts.current_page }} of {{ posts.last_page }}
                                </span>
                                <Link
                                    v-if="posts.next_page_url"
                                    :href="posts.next_page_url"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Categories</h3>
                        <div class="space-y-2">
                            <Link
                                :href="route('blog.index')"
                                class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition"
                                :class="{ 'bg-blue-50 text-blue-700': !filters.category }"
                            >
                                <span class="text-sm font-medium">All Articles</span>
                            </Link>
                            <Link
                                v-for="category in categories"
                                :key="category.id"
                                :href="route('blog.index', { category: category.slug })"
                                class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition"
                                :class="{ 'bg-blue-50 text-blue-700': filters.category === category.slug }"
                            >
                                <span class="text-sm font-medium">{{ category.name }}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ category.published_posts_count }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Popular Tags -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Popular Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in popularTags"
                                :key="tag.id"
                                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full cursor-pointer hover:bg-gray-200 transition"
                                @click="filterByTag(tag.slug)"
                            >
                                #{{ tag.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon, UserCircleIcon, CalendarIcon, ClockIcon, EyeIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

const props = defineProps({
    posts: Object,
    categories: Array,
    popularTags: Array,
    featuredPosts: Array,
    filters: Object,
});

const { formatDate } = useBangladeshFormat();
const searchQuery = ref(props.filters.search || '');
let searchTimeout = null;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('blog.index'), { search: searchQuery.value }, {
            preserveState: true,
            replace: true,
        });
    }, 500);
};

const filterByTag = (tagSlug) => {
    router.get(route('blog.index', { tag: tagSlug }));
};

const formatNumber = (num) => {
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'k';
    }
    return num;
};
</script>
