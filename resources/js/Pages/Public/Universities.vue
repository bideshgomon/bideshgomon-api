<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { MagnifyingGlassIcon, MapPinIcon } from '@heroicons/vue/24/solid';
import debounce from 'lodash/debounce';

const props = defineProps({
    initialUniversities: Object,
});

const universities = ref(props.initialUniversities);
const searchTerm = ref('');

const searchUniversities = debounce(() => {
    router.get(route('public.universities.search'), { search: searchTerm.value }, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            universities.value = page.props.initialUniversities;
        }
    });
}, 300);

watch(searchTerm, searchUniversities);

const getImageUrl = (logoPath) => {
    return logoPath ? `/storage/${logoPath}` : '/img/bideshgomonlogogray.png';
};

</script>

<template>
    <Head title="Find Universities" />
    <GuestLayout>
        <div class="bg-gray-100 dark:bg-gray-900 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                        Explore Universities Worldwide
                    </h1>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
                        Find your perfect institution from our curated list.
                    </p>
                </div>

                <div class="mb-8 max-w-2xl mx-auto">
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                        <TextInput
                            type="text"
                            v-model="searchTerm"
                            placeholder="Search by university name or country..."
                            class="w-full pl-10 pr-4 py-2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    </div>
                     </div>

                <div v-if="universities.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="uni in universities.data" :key="uni.id"
                         class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                        <Link :href="route('public.universities.show', uni.id)" class="block">
                            <div class="h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <img :src="getImageUrl(uni.logo_path)" alt="University Logo" class="max-h-24 max-w-full p-4 object-contain">
                             </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 truncate" :title="uni.name">
                                    {{ uni.name }}
                                </h3>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <MapPinIcon class="h-4 w-4 mr-1 flex-shrink-0" />
                                    <span>{{ uni.city?.name || 'N/A' }}, {{ uni.country?.name || 'N/A' }}</span>
                                </div>
                                <p v-if="uni.description" class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ uni.description }}
                                </p>
                             </div>
                        </Link>
                    </div>
                </div>
                <div v-else class="text-center py-16">
                    <p class="text-xl text-gray-500 dark:text-gray-400">No universities found matching your criteria.</p>
                </div>

                <div v-if="universities.links.length > 3" class="mt-12">
                    <Pagination :links="universities.links" />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>