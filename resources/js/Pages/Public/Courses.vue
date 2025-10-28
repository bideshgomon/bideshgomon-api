<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { MagnifyingGlassIcon, MapPinIcon, AcademicCapIcon } from '@heroicons/vue/24/solid';
import debounce from 'lodash/debounce';

const props = defineProps({
    initialCourses: Object,
});

const courses = ref(props.initialCourses);
const searchTerm = ref('');

const searchCourses = debounce(() => {
    router.get(route('public.courses.search'), { search: searchTerm.value }, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            courses.value = page.props.initialCourses;
        }
    });
}, 300);

watch(searchTerm, searchCourses);

</script>

<template>
    <Head title="Find Courses" />
    <GuestLayout>
        <div class="bg-gray-100 dark:bg-gray-900 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl">
                        Discover Your Ideal Course
                    </h1>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">
                        Search through countless programs offered by universities worldwide.
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
                            placeholder="Search by course name, university, or country..."
                            class="w-full pl-10 pr-4 py-2 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    </div>
                     </div>

                <div v-if="courses.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="course in courses.data" :key="course.id"
                         class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl flex flex-col">
                        <Link :href="route('public.courses.show', course.id)" class="block flex flex-col flex-grow">
                             <div class="p-6 flex-grow">
                                <div class="flex items-center text-sm text-brand-600 dark:text-brand-400 mb-1">
                                    <AcademicCapIcon class="h-4 w-4 mr-1 flex-shrink-0" />
                                    <span>{{ course.degree_level?.name || 'N/A' }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 truncate" :title="course.name">
                                    {{ course.name }}
                                </h3>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 truncate" :title="course.university?.name">
                                    {{ course.university?.name || 'University N/A' }}
                                </p>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <MapPinIcon class="h-4 w-4 mr-1 flex-shrink-0" />
                                    <span>{{ course.university?.city?.name || 'City N/A' }}, {{ course.university?.country?.name || 'Country N/A' }}</span>
                                </div>
                                <p v-if="course.description" class="mt-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 flex-grow">
                                    {{ course.description }}
                                </p>
                             </div>
                             <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 mt-auto">
                                 <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Duration: {{ course.duration_years || 'N/A' }} {{ course.duration_years ? 'Years' : '' }}</span>
                             </div>
                        </Link>
                    </div>
                </div>
                <div v-else class="text-center py-16">
                    <p class="text-xl text-gray-500 dark:text-gray-400">No courses found matching your criteria.</p>
                </div>

                <div v-if="courses.links.length > 3" class="mt-12">
                    <Pagination :links="courses.links" />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>