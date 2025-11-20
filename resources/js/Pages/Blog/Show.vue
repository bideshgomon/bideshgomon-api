<template>
    <Head :title="post.title" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Link :href="route('blog.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <ArrowLeftIcon class="h-4 w-4 mr-1" />
                    Back to Blog
                </Link>

                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full" :style="{ backgroundColor: post.category.color + '20', color: post.category.color }">
                        {{ post.category.name }}
                    </span>
                    <span v-if="post.is_featured" class="px-3 py-1 text-sm font-semibold bg-yellow-100 text-yellow-800 rounded-full">
                        Featured
                    </span>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ post.title }}</h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <UserCircleIcon class="h-5 w-5" />
                        <span>{{ post.author.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <CalendarIcon class="h-5 w-5" />
                        <span>{{ formatDate(post.published_at) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <ClockIcon class="h-5 w-5" />
                        <span>{{ post.reading_time }} min read</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <EyeIcon class="h-5 w-5" />
                        <span>{{ formatNumber(post.views_count) }} views</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <article class="bg-white rounded-lg shadow-md p-8 mb-8">
                <!-- Featured Image Placeholder -->
                <div v-if="post.featured_image" class="mb-8 rounded-lg overflow-hidden">
                    <div class="h-96 bg-gradient-to-br from-blue-400 to-indigo-600"></div>
                    <p v-if="post.image_credit" class="text-xs text-gray-500 mt-2">{{ post.image_credit }}</p>
                </div>

                <!-- Excerpt -->
                <div v-if="post.excerpt" class="text-xl text-gray-600 italic mb-8 pb-8 border-b">
                    {{ post.excerpt }}
                </div>

                <!-- Content -->
                <div class="prose prose-lg max-w-none" v-html="post.content"></div>

                <!-- Tags -->
                <div v-if="post.tags.length > 0" class="mt-12 pt-8 border-t">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Tags:</h3>
                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="tag in post.tags"
                            :key="tag.id"
                            :href="route('blog.index', { tag: tag.slug })"
                            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition"
                        >
                            #{{ tag.name }}
                        </Link>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="mt-12 pt-8 border-t">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                            {{ post.author.name.charAt(0) }}
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ post.author.name }}</h4>
                            <p class="text-sm text-gray-600">Author</p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Related Posts -->
            <div v-if="relatedPosts.length > 0" class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article
                        v-for="relatedPost in relatedPosts"
                        :key="relatedPost.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition cursor-pointer"
                        @click="$inertia.visit(route('blog.show', relatedPost.slug))"
                    >
                        <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full" :style="{ backgroundColor: relatedPost.category.color + '20', color: relatedPost.category.color }">
                                    {{ relatedPost.category.name }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ relatedPost.title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ relatedPost.excerpt }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ formatDate(relatedPost.published_at) }}</span>
                                <span>{{ relatedPost.reading_time }} min read</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon, UserCircleIcon, CalendarIcon, ClockIcon, EyeIcon } from '@heroicons/vue/24/outline';
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat';

defineProps({
    post: Object,
    relatedPosts: Array,
});

const { formatDate } = useBangladeshFormat();

const formatNumber = (num) => {
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'k';
    }
    return num;
};
</script>

<style scoped>
.prose {
    @apply text-gray-800;
}

.prose h2 {
    @apply text-2xl font-bold text-gray-900 mt-8 mb-4;
}

.prose h3 {
    @apply text-xl font-bold text-gray-900 mt-6 mb-3;
}

.prose p {
    @apply mb-4 leading-relaxed;
}

.prose ul, .prose ol {
    @apply ml-6 mb-4;
}

.prose li {
    @apply mb-2;
}

.prose a {
    @apply text-blue-600 hover:text-blue-800 underline;
}
</style>
