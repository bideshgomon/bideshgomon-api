<template>
    <Head title="Team Members" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Team Members</h2>
                            <Link :href="route('agency.team.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Add Team Member
                            </Link>
                        </div>

                        <div v-if="teamMembers.length === 0" class="text-center py-12">
                            <UsersIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No team members</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding your first team member.</p>
                            <div class="mt-6">
                                <Link :href="route('agency.team.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <PlusIcon class="h-5 w-5 mr-2" />
                                    Add Team Member
                                </Link>
                            </div>
                        </div>

                        <div v-else>
                            <div class="mb-4 text-sm text-gray-600">
                                Drag and drop team members to reorder them
                            </div>
                            
                            <draggable 
                                v-model="sortedTeamMembers" 
                                @end="handleReorder"
                                item-key="id"
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                            >
                                <template #item="{element}">
                                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow cursor-move">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0">
                                                <img 
                                                    v-if="element.photo" 
                                                    :src="`/storage/${element.photo}`" 
                                                    alt="Team member photo"
                                                    class="h-16 w-16 rounded-full object-cover"
                                                />
                                                <div v-else class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <UserIcon class="h-8 w-8 text-indigo-600" />
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ element.name }}</h3>
                                                <p class="text-sm text-gray-600">{{ element.position }}</p>
                                                <p v-if="element.years_experience" class="text-xs text-gray-500 mt-1">
                                                    {{ element.years_experience }} years experience
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="element.bio" class="mt-4 text-sm text-gray-600 line-clamp-3">
                                            {{ element.bio }}
                                        </div>

                                        <div v-if="element.languages && element.languages.length > 0" class="mt-4">
                                            <div class="flex flex-wrap gap-2">
                                                <span v-for="language in element.languages.slice(0, 3)" :key="language" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ language }}
                                                </span>
                                                <span v-if="element.languages.length > 3" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    +{{ element.languages.length - 3 }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    element.is_visible ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                                ]">
                                                    {{ element.is_visible ? 'Visible' : 'Hidden' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <Link :href="route('agency.team.edit', element.id)" class="text-indigo-600 hover:text-indigo-900">
                                                    <PencilIcon class="h-5 w-5" />
                                                </Link>
                                                <button @click="deleteMember(element.id)" class="text-red-600 hover:text-red-900">
                                                    <TrashIcon class="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import draggable from 'vuedraggable';
import { UsersIcon, UserIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    teamMembers: Array,
});

const sortedTeamMembers = ref([...props.teamMembers]);

const handleReorder = () => {
    const order = sortedTeamMembers.value.map((member, index) => ({
        id: member.id,
        display_order: index
    }));

    router.post(route('agency.team.reorder'), { order }, {
        preserveScroll: true,
    });
};

const deleteMember = (id) => {
    if (!confirm('Are you sure you want to delete this team member?')) return;

    router.delete(route('agency.team.destroy', id), {
        preserveScroll: true,
    });
};
</script>
