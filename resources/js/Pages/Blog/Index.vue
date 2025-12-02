<template>
    <GuestLayout>
        <Head title="Blog - Expert Travel Guides & Resources" />

        <div class="bg-gradient-to-b from-ocean-50 to-white">
        <!-- Hero Header -->
        <div class="relative bg-gradient-ocean text-white overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-ocean-600/20 to-sky-600/20"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                        <SparklesIcon class="h-5 w-5 text-sunrise-300" />
                        <span class="text-sm font-medium">Expert Travel Insights</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        Blog & <span class="text-sunrise-300">Travel Guides</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-ocean-100 max-w-3xl mx-auto leading-relaxed">
                        Discover expert tips, visa guides, and essential resources for your international journey
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
            <!-- Search Bar -->
            <div class="mb-12">
                <div class="relative max-w-2xl mx-auto">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search articles, guides, and tips..."
                        class="w-full px-6 py-4 pl-14 rounded-2xl border-2 border-white bg-white shadow-xl focus:ring-2 focus:ring-ocean-500 focus:border-ocean-500 text-lg transition-all"
                        @input="handleSearch"
                    />
                    <MagnifyingGlassIcon class="absolute left-5 top-5 h-6 w-6 text-ocean-400" />
                    <div v-if="searchQuery" class="absolute right-5 top-5">
                        <button @click="clearSearch" class="text-gray-400 hover:text-gray-600">
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Featured Posts -->
                    <div v-if="featuredPosts.length > 0 && !filters.search && !filters.category" class="mb-16">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="h-1 w-12 bg-gradient-ocean rounded-full"></div>
                            <h2 class="text-3xl font-bold text-gray-900">Featured Articles</h2>
                            <StarIcon class="h-6 w-6 text-sunrise-500" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <article
                                v-for="post in featuredPosts"
                                :key="post.id"
                                class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer border border-gray-100"
                                @click="$inertia.visit(route('blog.show', post.slug))"
                            >
                                <div class="relative h-56 bg-gradient-to-br from-ocean-100 to-sky-100 overflow-hidden">
                                    <div class="absolute inset-0 bg-pattern opacity-5"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <DocumentTextIcon class="h-20 w-20 text-ocean-300 group-hover:scale-110 transition-transform duration-300" />
                                    </div>
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-sunrise-500 text-white text-xs font-bold rounded-full shadow-lg">
                                            <StarIcon class="h-3 w-3" />
                                            Featured
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm" :style="{ backgroundColor: post.category.color + '20', color: post.category.color }">
                                            {{ post.category.name }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-ocean-600 transition-colors">
                                        {{ post.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ post.excerpt }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
                                        <div class="flex items-center gap-1">
                                            <CalendarIcon class="h-4 w-4" />
                                            <span>{{ formatDate(post.published_at) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <ClockIcon class="h-4 w-4" />
                                            <span>{{ post.reading_time }} min</span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- All Posts -->
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="h-1 w-8 bg-gradient-ocean rounded-full"></div>
                                <h2 class="text-3xl font-bold text-gray-900">
                                    {{ filters.category ? 'Category: ' + filters.category : 'Latest Articles' }}
                                </h2>
                            </div>
                            <span class="text-sm font-medium text-gray-600 bg-gray-100 px-4 py-2 rounded-full">
                                {{ posts.total }} articles
                            </span>
                        </div>

                        <div v-if="posts.data.length > 0" class="space-y-6">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer border border-gray-100"
                                @click="$inertia.visit(route('blog.show', post.slug))"
                            >
                                <div class="md:flex">
                                    <div class="md:w-1/3 h-64 md:h-auto bg-gradient-to-br from-ocean-100 to-sky-100 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-pattern opacity-5"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <DocumentTextIcon class="h-16 w-16 text-ocean-300 group-hover:scale-110 transition-transform duration-300" />
                                        </div>
                                        <div v-if="post.is_featured" class="absolute top-4 left-4">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-sunrise-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                <StarIcon class="h-3 w-3" />
                                                Featured
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-8 md:w-2/3 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-4">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm" :style="{ backgroundColor: post.category.color + '20', color: post.category.color }">
                                                    {{ post.category.name }}
                                                </span>
                                            </div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-ocean-600 transition-colors">
                                                {{ post.title }}
                                            </h3>
                                            <p class="text-gray-600 mb-6 line-clamp-2 leading-relaxed">{{ post.excerpt }}</p>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-6 text-sm text-gray-500 mb-4">
                                                <div class="flex items-center gap-2">
                                                    <UserCircleIcon class="h-5 w-5 text-ocean-400" />
                                                    <span class="font-medium">{{ post.author.name }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <CalendarIcon class="h-5 w-5 text-ocean-400" />
                                                    <span>{{ formatDate(post.published_at) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <ClockIcon class="h-5 w-5 text-ocean-400" />
                                                    <span>{{ post.reading_time }} min read</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <EyeIcon class="h-5 w-5 text-ocean-400" />
                                                    <span>{{ formatNumber(post.views_count) }} views</span>
                                                </div>
                                            </div>
                                            <div v-if="post.tags.length > 0" class="flex flex-wrap gap-2">
                                                <span
                                                    v-for="tag in post.tags.slice(0, 4)"
                                                    :key="tag.id"
                                                    class="px-3 py-1 text-xs font-medium bg-ocean-50 text-ocean-700 rounded-full cursor-pointer hover:bg-ocean-100 transition-colors"
                                                    @click.stop="filterByTag(tag.slug)"
                                                >
                                                    #{{ tag.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div v-else class="text-center py-20 bg-white rounded-2xl shadow-md">
                            <DocumentTextIcon class="mx-auto h-20 w-20 text-gray-300 mb-4" />
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No articles found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search or filters to find what you're looking for.</p>
                            <button
                                @click="clearFilters"
                                class="px-6 py-3 bg-gradient-ocean text-white rounded-full hover:shadow-lg transition-all font-medium"
                            >
                                Clear Filters
                            </button>
                        </div>

                        <!-- Pagination -->
                        <div v-if="posts.last_page > 1" class="mt-12 flex justify-center">
                            <nav class="flex items-center gap-2">
                                <Link
                                    v-if="posts.prev_page_url"
                                    :href="posts.prev_page_url"
                                    class="px-6 py-3 text-sm font-medium text-ocean-700 bg-white border-2 border-ocean-200 rounded-full hover:bg-ocean-50 hover:border-ocean-300 transition-all"
                                >
                                    ← Previous
                                </Link>
                                <span class="px-6 py-3 text-sm font-medium text-gray-700 bg-white rounded-full border-2 border-gray-200">
                                    Page {{ posts.current_page }} of {{ posts.last_page }}
                                </span>
                                <Link
                                    v-if="posts.next_page_url"
                                    :href="posts.next_page_url"
                                    class="px-6 py-3 text-sm font-medium text-ocean-700 bg-white border-2 border-ocean-200 rounded-full hover:bg-ocean-50 hover:border-ocean-300 transition-all"
                                >
                                    Next →
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Categories -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100 sticky top-4">
                        <div class="flex items-center gap-2 mb-6">
                            <FolderIcon class="h-6 w-6 text-ocean-500" />
                            <h3 class="text-xl font-bold text-gray-900">Categories</h3>
                        </div>
                        <div class="space-y-2">
                            <Link
                                :href="route('blog.index')"
                                class="flex items-center justify-between p-3 rounded-xl hover:bg-ocean-50 transition-all group"
                                :class="{ 'bg-gradient-ocean text-white shadow-md': !filters.category, 'text-gray-700': filters.category }"
                            >
                                <span class="text-sm font-medium group-hover:translate-x-1 transition-transform">All Articles</span>
                                <span v-if="!filters.category" class="text-xs bg-white/20 px-2 py-1 rounded-full">Active</span>
                            </Link>
                            <Link
                                v-for="category in categories"
                                :key="category.id"
                                :href="route('blog.index', { category: category.slug })"
                                class="flex items-center justify-between p-3 rounded-xl hover:bg-ocean-50 transition-all group"
                                :class="{ 'bg-gradient-ocean text-white shadow-md': filters.category === category.slug, 'text-gray-700': filters.category !== category.slug }"
                            >
                                <span class="text-sm font-medium group-hover:translate-x-1 transition-transform">{{ category.name }}</span>
                                <span class="text-xs px-2 py-1 rounded-full" :class="filters.category === category.slug ? 'bg-white/20' : 'bg-gray-100 text-gray-600'">
                                    {{ category.published_posts_count }}
                                </span>
                            </Link>
                        </div>
                    </div>

                    <!-- Popular Tags -->
                    <div class="bg-gradient-to-br from-ocean-50 to-sky-50 rounded-2xl shadow-lg p-6 border border-ocean-100">
                        <div class="flex items-center gap-2 mb-6">
                            <TagIcon class="h-6 w-6 text-ocean-500" />
                            <h3 class="text-xl font-bold text-gray-900">Popular Tags</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in popularTags"
                                :key="tag.id"
                                class="px-4 py-2 text-sm font-medium bg-white text-ocean-700 rounded-full cursor-pointer hover:bg-ocean-500 hover:text-white transition-all shadow-sm hover:shadow-md"
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
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    MagnifyingGlassIcon, 
    UserCircleIcon, 
    CalendarIcon, 
    ClockIcon, 
    EyeIcon, 
    DocumentTextIcon,
    StarIcon,
    SparklesIcon,
    FolderIcon,
    TagIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';
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

const clearSearch = () => {
    searchQuery.value = '';
    router.get(route('blog.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    router.get(route('blog.index'));
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

<style scoped>
.bg-pattern {
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.03) 10px, rgba(255,255,255,.03) 20px),
        repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,.03) 10px, rgba(255,255,255,.03) 20px);
}
</style>
