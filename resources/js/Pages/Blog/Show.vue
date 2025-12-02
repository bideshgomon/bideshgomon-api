<template>
    <GuestLayout>
        <Head :title="post.title" />

        <div class="bg-gradient-to-b from-ocean-50 to-white">
            <!-- Breadcrumb & Back Button -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <Link :href="route('blog.index')" class="inline-flex items-center text-sm font-medium text-ocean-600 hover:text-ocean-700 transition-colors">
                        <ArrowLeftIcon class="h-4 w-4 mr-2" />
                        Back to All Articles
                    </Link>
                </div>
            </div>

            <!-- Article Header -->
            <div class="relative bg-gradient-ocean text-white overflow-hidden">
                <div class="absolute inset-0 bg-pattern opacity-10"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-ocean-600/20 to-sky-600/20"></div>
                
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-white/20 backdrop-blur-sm" :style="{ color: 'white' }">
                            {{ post.category.name }}
                        </span>
                        <span v-if="post.is_featured" class="inline-flex items-center gap-1 px-3 py-1 bg-sunrise-500 text-white text-xs font-bold rounded-full shadow-lg">
                            <StarIcon class="h-3 w-3" />
                            Featured
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ post.title }}</h1>

                    <div class="flex flex-wrap items-center gap-6 text-ocean-100">
                        <div class="flex items-center gap-2">
                            <UserCircleIcon class="h-6 w-6" />
                            <span class="font-medium">{{ post.author.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CalendarIcon class="h-6 w-6" />
                            <span>{{ formatDate(post.published_at) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <ClockIcon class="h-6 w-6" />
                            <span>{{ post.reading_time }} min read</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <EyeIcon class="h-6 w-6" />
                            <span>{{ formatNumber(post.views_count) }} views</span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Content -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-16">
                <article class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-8 border border-gray-100">
                    <!-- Featured Image Placeholder -->
                    <div v-if="post.featured_image" class="mb-10">
                        <div class="rounded-xl overflow-hidden shadow-lg">
                            <div class="h-96 bg-gradient-to-br from-ocean-100 to-sky-100 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-pattern opacity-5"></div>
                                <DocumentTextIcon class="h-24 w-24 text-ocean-300" />
                            </div>
                        </div>
                        <p v-if="post.image_credit" class="text-xs text-gray-500 mt-2 px-4">{{ post.image_credit }}</p>
                    </div>                    <!-- Excerpt -->
                    <div v-if="post.excerpt" class="text-xl leading-relaxed text-gray-600 bg-ocean-50 p-6 rounded-xl mb-10 border-l-4 border-ocean-500">
                        <QuoteIcon class="h-8 w-8 text-ocean-400 mb-2" />
                        {{ post.excerpt }}
                    </div>

                    <!-- Content -->
                    <div class="prose prose-lg prose-ocean max-w-none" v-html="post.content"></div>

                    <!-- Tags -->
                    <div v-if="post.tags.length > 0" class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <TagIcon class="h-5 w-5 text-ocean-500" />
                            <h3 class="text-lg font-bold text-gray-900">Related Topics</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <Link
                                v-for="tag in post.tags"
                                :key="tag.id"
                                :href="route('blog.index', { tag: tag.slug })"
                                class="px-4 py-2 text-sm font-medium bg-ocean-50 text-ocean-700 rounded-full hover:bg-ocean-500 hover:text-white transition-all shadow-sm hover:shadow-md"
                            >
                                #{{ tag.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Author Info -->
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex items-center gap-6 p-6 bg-gradient-to-br from-ocean-50 to-sky-50 rounded-xl">
                            <div class="h-20 w-20 rounded-full bg-gradient-ocean flex items-center justify-center text-white text-2xl font-bold shadow-lg flex-shrink-0">
                                {{ post.author.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-1">{{ post.author.name }}</h4>
                                <p class="text-sm text-gray-600">Content Author</p>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Related Posts -->
                <div v-if="relatedPosts.length > 0" class="mt-16">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-1 w-12 bg-gradient-ocean rounded-full"></div>
                        <h2 class="text-3xl font-bold text-gray-900">Related Articles</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <article
                            v-for="relatedPost in relatedPosts"
                            :key="relatedPost.id"
                            class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer border border-gray-100"
                            @click="$inertia.visit(route('blog.show', relatedPost.slug))"
                        >
                            <div class="h-48 bg-gradient-to-br from-ocean-100 to-sky-100 relative overflow-hidden">
                                <div class="absolute inset-0 bg-pattern opacity-5"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <DocumentTextIcon class="h-16 w-16 text-ocean-300 group-hover:scale-110 transition-transform duration-300" />
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm" :style="{ backgroundColor: relatedPost.category.color + '20', color: relatedPost.category.color }">
                                        {{ relatedPost.category.name }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-ocean-600 transition-colors">
                                    {{ relatedPost.title }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ relatedPost.excerpt }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-gray-100">
                                    <div class="flex items-center gap-1">
                                        <CalendarIcon class="h-4 w-4" />
                                        <span>{{ formatDate(relatedPost.published_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <ClockIcon class="h-4 w-4" />
                                        <span>{{ relatedPost.reading_time }} min</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

    <script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { 
        ArrowLeftIcon, 
        UserCircleIcon, 
        CalendarIcon, 
        ClockIcon, 
        EyeIcon,
        DocumentTextIcon,
        TagIcon,
        StarIcon
    } from '@heroicons/vue/24/outline';
    import { ChatBubbleLeftRightIcon as QuoteIcon } from '@heroicons/vue/24/solid';
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
    .bg-pattern {
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.03) 10px, rgba(255,255,255,.03) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,.03) 10px, rgba(255,255,255,.03) 20px);
    }

    .prose-ocean {
        --tw-prose-body: theme('colors.gray.700');
        --tw-prose-headings: theme('colors.gray.900');
        --tw-prose-links: theme('colors.ocean.600');
        --tw-prose-bold: theme('colors.gray.900');
        --tw-prose-quotes: theme('colors.gray.900');
        --tw-prose-quote-borders: theme('colors.ocean.500');
        --tw-prose-code: theme('colors.ocean.700');
        --tw-prose-pre-bg: theme('colors.gray.900');
    }
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
