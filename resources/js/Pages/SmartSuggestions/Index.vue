<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    LightBulbIcon, SparklesIcon, CheckCircleIcon, 
    XMarkIcon, ArrowPathIcon, ExclamationTriangleIcon,
    DocumentTextIcon, UserIcon, BriefcaseIcon, 
    GlobeAltIcon, ClipboardDocumentListIcon, StarIcon,
    FireIcon, BellAlertIcon, InformationCircleIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    suggestions: Array,
    counts: Object,
    assessment: Object,
    profileStrength: Number,
});

const urgentSuggestions = computed(() => 
    props.suggestions.filter(s => s.priority === 'urgent')
);

const highPrioritySuggestions = computed(() => 
    props.suggestions.filter(s => s.priority === 'high')
);

const mediumPrioritySuggestions = computed(() => 
    props.suggestions.filter(s => s.priority === 'medium')
);

const lowPrioritySuggestions = computed(() => 
    props.suggestions.filter(s => s.priority === 'low')
);

const getPriorityColor = (priority) => {
    const colors = {
        urgent: 'bg-red-100 border-2 border-red-300 text-red-700',
        high: 'bg-orange-100 border-2 border-orange-300 text-orange-700',
        medium: 'bg-yellow-100 border-2 border-yellow-300 text-yellow-700',
        low: 'bg-blue-100 border-2 border-blue-300 text-blue-700',
    };
    return colors[priority] || 'bg-gray-100 border-2 border-gray-300 text-gray-700';
};

const getPriorityBadgeColor = (priority) => {
    const colors = {
        urgent: 'bg-red-100 text-red-800 border-red-200',
        high: 'bg-orange-100 text-orange-800 border-orange-200',
        medium: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        low: 'bg-blue-100 text-blue-800 border-blue-200',
    };
    return colors[priority] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getCategoryIcon = (category) => {
    const icons = {
        visa: GlobeAltIcon,
        profile: UserIcon,
        document: DocumentTextIcon,
        application: ClipboardDocumentListIcon,
        assessment: StarIcon,
    };
    return icons[category] || LightBulbIcon;
};

const completeSuggestion = (suggestionId) => {
    useForm({}).post(route('suggestions.complete', suggestionId));
};

const dismissSuggestion = (suggestionId) => {
    useForm({}).post(route('suggestions.dismiss', suggestionId));
};

const refreshSuggestions = () => {
    useForm({}).post(route('suggestions.refresh'));
};

const getStrengthColor = (score) => {
    if (score >= 80) return 'text-green-600';
    if (score >= 60) return 'text-yellow-600';
    return 'text-red-600';
};

const getStrengthLabel = (score) => {
    if (score >= 80) return 'Strong';
    if (score >= 60) return 'Good';
    if (score >= 40) return 'Fair';
    return 'Needs Improvement';
};
</script>

<template>
    <Head title="Smart Suggestions" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="bg-blue-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-700 rounded-xl border-2 border-blue-800">
                            <SparklesIcon class="h-8 w-8 text-white" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Smart Suggestions</h1>
                            <p class="text-blue-100 text-sm mt-1">
                                Personalized recommendations to strengthen your profile
                            </p>
                        </div>
                    </div>
                    <button
                        @click="refreshSuggestions"
                        class="inline-flex items-center gap-2 bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors font-medium"
                    >
                        <ArrowPathIcon class="h-5 w-5" />
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Total Suggestions -->
                <div class="bg-white rounded-xl border-2 border-gray-200 p-4 hover:border-indigo-400 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <LightBulbIcon class="h-6 w-6 text-indigo-600" />
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ counts.total }}</p>
                    <p class="text-xs text-gray-600">Total Active</p>
                </div>

                <!-- Urgent -->
                <div class="bg-white rounded-xl border-2 border-red-200 p-4 hover:border-red-400 hover:bg-red-50 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <FireIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <p class="text-2xl font-bold text-red-600">{{ counts.urgent }}</p>
                    <p class="text-xs text-gray-600">Urgent</p>
                </div>

                <!-- High Priority -->
                <div class="bg-white rounded-xl border-2 border-orange-200 p-4 hover:border-orange-400 hover:bg-orange-50 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <BellAlertIcon class="h-6 w-6 text-orange-600" />
                    </div>
                    <p class="text-2xl font-bold text-orange-600">{{ counts.high }}</p>
                    <p class="text-xs text-gray-600">High Priority</p>
                </div>

                <!-- Medium Priority -->
                <div class="bg-white rounded-xl border-2 border-yellow-200 p-4 hover:border-yellow-400 hover:bg-yellow-50 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <InformationCircleIcon class="h-6 w-6 text-yellow-600" />
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">{{ counts.medium }}</p>
                    <p class="text-xs text-gray-600">Medium</p>
                </div>

                <!-- Profile Strength -->
                <div class="bg-purple-50 rounded-xl border-2 border-purple-200 p-4 hover:border-purple-400 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <StarIcon class="h-6 w-6 text-purple-600" />
                    </div>
                    <p class="text-2xl font-bold" :class="getStrengthColor(profileStrength)">
                        {{ profileStrength }}/100
                    </p>
                    <p class="text-xs text-gray-600">Profile Strength</p>
                </div>
            </div>

            <!-- Urgent Suggestions -->
            <div v-if="urgentSuggestions.length > 0" class="bg-red-50 border-2 border-red-300 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <FireIcon class="h-5 w-5 text-red-600" />
                    </div>
                    <h2 class="text-xl font-bold text-red-900">Urgent Actions Required</h2>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="suggestion in urgentSuggestions"
                        :key="suggestion.id"
                        class="bg-white rounded-xl border-2 border-red-300 p-4 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <component 
                                        :is="getCategoryIcon(suggestion.category)" 
                                        class="h-5 w-5 text-red-600" 
                                    />
                                    <h3 class="text-lg font-bold text-gray-900">{{ suggestion.title }}</h3>
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-lg">
                                        URGENT
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-3">{{ suggestion.description }}</p>
                                <div class="flex items-center gap-3">
                                    <Link
                                        :href="suggestion.action_url"
                                        class="inline-flex items-center px-4 py-2 bg-red-50 border-2 border-red-300 text-red-700 font-semibold rounded-lg hover:bg-red-100 transition-colors"
                                    >
                                        Take Action
                                    </Link>
                                    <button
                                        @click="completeSuggestion(suggestion.id)"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                    >
                                        <CheckCircleIcon class="h-4 w-4" />
                                        Mark Complete
                                    </button>
                                    <button
                                        @click="dismissSuggestion(suggestion.id)"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                        Dismiss
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- High Priority Suggestions -->
            <div v-if="highPrioritySuggestions.length > 0">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <BellAlertIcon class="h-5 w-5 text-orange-600" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">High Priority Suggestions</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="suggestion in highPrioritySuggestions"
                        :key="suggestion.id"
                        class="bg-white rounded-xl border-2 border-orange-200 p-5 hover:shadow-md hover:border-orange-400 transition-all"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <component 
                                    :is="getCategoryIcon(suggestion.category)" 
                                    class="h-5 w-5 text-orange-600" 
                                />
                                <h3 class="font-bold text-gray-900">{{ suggestion.title }}</h3>
                            </div>
                            <span :class="getPriorityBadgeColor(suggestion.priority)" class="px-2 py-1 text-xs font-semibold rounded-lg border">
                                {{ (suggestion.priority || '').toUpperCase() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mb-4">{{ suggestion.description }}</p>
                        <div class="flex items-center gap-2">
                            <Link
                                :href="suggestion.action_url"
                                class="flex-1 text-center px-3 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors text-sm"
                            >
                                Take Action
                            </Link>
                            <button
                                @click="completeSuggestion(suggestion.id)"
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                            >
                                <CheckCircleIcon class="h-5 w-5" />
                            </button>
                            <button
                                @click="dismissSuggestion(suggestion.id)"
                                class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                            >
                                <XMarkIcon class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medium & Low Priority Suggestions -->
            <div v-if="mediumPrioritySuggestions.length > 0 || lowPrioritySuggestions.length > 0">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Other Suggestions</h2>
                <div class="space-y-3">
                    <div
                        v-for="suggestion in [...mediumPrioritySuggestions, ...lowPrioritySuggestions]"
                        :key="suggestion.id"
                        class="bg-white rounded-xl border-2 border-gray-200 p-4 hover:shadow-md hover:border-indigo-400 transition-all"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <component 
                                    :is="getCategoryIcon(suggestion.category)" 
                                    class="h-5 w-5 text-gray-600" 
                                />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-900">{{ suggestion.title }}</h3>
                                        <span :class="getPriorityBadgeColor(suggestion.priority)" class="px-2 py-0.5 text-xs font-medium rounded-lg border">
                                            {{ suggestion.priority }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ suggestion.description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <Link
                                    :href="suggestion.action_url"
                                    class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                                >
                                    View
                                </Link>
                                <button
                                    @click="completeSuggestion(suggestion.id)"
                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                </button>
                                <button
                                    @click="dismissSuggestion(suggestion.id)"
                                    class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="suggestions.length === 0" class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <div class="p-4 bg-green-100 rounded-xl inline-flex mb-4">
                    <CheckCircleIcon class="h-12 w-12 text-green-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">All Caught Up!</h3>
                <p class="text-gray-600 mb-4">
                    You've addressed all suggestions. Keep your profile updated for new recommendations.
                </p>
                <Link
                    :href="route('profile.show')"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    View Profile
                </Link>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
