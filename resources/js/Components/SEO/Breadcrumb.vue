<script setup>
import { computed } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        required: true
    }
})

const breadcrumbSchema = computed(() => {
    const items = props.items.map((item, index) => ({
        '@type': 'ListItem',
        'position': index + 1,
        'name': item.title,
        'item': item.url
    }))

    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        'itemListElement': items
    }
})
</script>

<template>
    <nav aria-label="Breadcrumb" class="flex" itemscope itemtype="https://schema.org/BreadcrumbList">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li
                v-for="(item, index) in items"
                :key="index"
                class="inline-flex items-center"
                itemprop="itemListElement"
                itemscope
                itemtype="https://schema.org/ListItem"
            >
                <div v-if="index > 0" class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                </div>
                <Link
                    v-if="!item.current"
                    :href="item.url"
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-ocean-600 transition-colors"
                    itemprop="item"
                >
                    <span itemprop="name">{{ item.title }}</span>
                </Link>
                <span
                    v-else
                    class="inline-flex items-center text-sm font-medium text-gray-500"
                    itemprop="name"
                >
                    {{ item.title }}
                </span>
                <meta itemprop="position" :content="index + 1" />
            </li>
        </ol>

        <!-- JSON-LD Schema -->
        <script type="application/ld+json" v-html="JSON.stringify(breadcrumbSchema)"></script>
    </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: { Link }
}
</script>
