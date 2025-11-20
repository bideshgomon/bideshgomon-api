<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ChartBarIcon,
    DocumentCheckIcon,
    GlobeAltIcon,
    ExclamationTriangleIcon,
    SparklesIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    assessment: Object,
});

const refreshing = ref(false);

const scoreColor = computed(() => {
    const score = props.assessment.overall_score;
    if (score >= 80) return 'text-green-600';
    if (score >= 60) return 'text-yellow-600';
    if (score >= 40) return 'text-orange-600';
    return 'text-red-600';
});

const scoreBgColor = computed(() => {
    const score = props.assessment.overall_score;
    if (score >= 80) return 'bg-green-100';
    if (score >= 60) return 'bg-yellow-100';
    if (score >= 40) return 'bg-orange-100';
    return 'bg-red-100';
});

const riskBadgeColor = computed(() => {
    const risk = props.assessment.risk_level;
    if (risk === 'low') return 'bg-green-100 text-green-800';
    if (risk === 'medium') return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
});

const refreshAssessment = () => {
    refreshing.value = true;
    router.post(route('profile.assessment.generate'), {}, {
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const getScoreWidth = (score) => {
    return `${score}%`;
};

const getScoreBarColor = (score) => {
    if (score >= 80) return 'bg-green-500';
    if (score >= 60) return 'bg-yellow-500';
    if (score >= 40) return 'bg-orange-500';
    return 'bg-red-500';
};

const getPriorityColor = (priority) => {
    if (priority === 'critical') return 'text-red-600 border-red-200';
    if (priority === 'high') return 'text-orange-600 border-orange-200';
    return 'text-yellow-600 border-yellow-200';
};
</script>

<template>
    <Head title="Profile Assessment" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">AI Profile Assessment</h1>
                            <p class="mt-2 text-gray-600">
                                Comprehensive analysis of your visa application readiness
                            </p>
                        </div>
                        <button
                            @click="refreshAssessment"
                            :disabled="refreshing"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <ArrowPathIcon class="h-5 w-5 mr-2" :class="{ 'animate-spin': refreshing }" />
                            {{ refreshing ? 'Refreshing...' : 'Refresh Assessment' }}
                        </button>
                    </div>
                </div>

                <!-- Overall Score Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="p-8">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-4">
                                    <SparklesIcon class="h-8 w-8 text-indigo-600 mr-3" />
                                    <h2 class="text-2xl font-bold text-gray-900">Overall Assessment</h2>
                                </div>
                                <p class="text-gray-600 mb-6">{{ assessment.ai_summary }}</p>
                                <div class="flex items-center space-x-4">
                                    <span :class="riskBadgeColor" class="px-4 py-2 rounded-full text-sm font-semibold uppercase">
                                        {{ assessment.risk_level }} Risk
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        Last assessed: {{ assessment.assessed_at }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-8 flex flex-col items-center">
                                <div :class="[scoreBgColor, scoreColor]" class="w-32 h-32 rounded-full flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-4xl font-bold">{{ Math.round(assessment.overall_score) }}</div>
                                        <div class="text-sm font-medium">out of 100</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center mb-4">
                            <ChartBarIcon class="h-6 w-6 text-blue-600 mr-3" />
                            <h3 class="text-lg font-semibold text-gray-900">Profile Completeness</h3>
                        </div>
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ Math.round(assessment.profile_completeness) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    :class="getScoreBarColor(assessment.profile_completeness)"
                                    class="h-2 rounded-full transition-all"
                                    :style="{ width: getScoreWidth(assessment.profile_completeness) }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center mb-4">
                            <DocumentCheckIcon class="h-6 w-6 text-purple-600 mr-3" />
                            <h3 class="text-lg font-semibold text-gray-900">Document Readiness</h3>
                        </div>
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ Math.round(assessment.document_readiness) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    :class="getScoreBarColor(assessment.document_readiness)"
                                    class="h-2 rounded-full transition-all"
                                    :style="{ width: getScoreWidth(assessment.document_readiness) }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center mb-4">
                            <GlobeAltIcon class="h-6 w-6 text-green-600 mr-3" />
                            <h3 class="text-lg font-semibold text-gray-900">Visa Eligibility</h3>
                        </div>
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ Math.round(assessment.visa_eligibility) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    :class="getScoreBarColor(assessment.visa_eligibility)"
                                    class="h-2 rounded-full transition-all"
                                    :style="{ width: getScoreWidth(assessment.visa_eligibility) }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Scores -->
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Section Breakdown</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="(score, section) in assessment.section_scores" :key="section">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700 capitalize">
                                        {{ section.replace('_', ' ') }}
                                    </span>
                                    <span class="text-sm font-bold" :class="getScoreBarColor(score).replace('bg-', 'text-')">
                                        {{ Math.round(score) }}/100
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        :class="getScoreBarColor(score)"
                                        class="h-2 rounded-full transition-all"
                                        :style="{ width: getScoreWidth(score) }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Strengths -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center">
                                <CheckCircleIcon class="h-6 w-6 text-green-600 mr-3" />
                                <h2 class="text-xl font-bold text-gray-900">Strengths</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <ul v-if="assessment.strengths.length > 0" class="space-y-3">
                                <li v-for="(strength, index) in assessment.strengths" :key="index" class="flex items-start">
                                    <CheckCircleIcon class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" />
                                    <span class="text-gray-700">{{ strength }}</span>
                                </li>
                            </ul>
                            <p v-else class="text-gray-500 text-center py-4">
                                Complete your profile to see your strengths
                            </p>
                        </div>
                    </div>

                    <!-- Weaknesses -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center">
                                <XCircleIcon class="h-6 w-6 text-red-600 mr-3" />
                                <h2 class="text-xl font-bold text-gray-900">Areas for Improvement</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <ul v-if="assessment.weaknesses.length > 0" class="space-y-3">
                                <li v-for="(weakness, index) in assessment.weaknesses" :key="index" class="flex items-start">
                                    <XCircleIcon class="h-5 w-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" />
                                    <span class="text-gray-700">{{ weakness }}</span>
                                </li>
                            </ul>
                            <p v-else class="text-green-600 text-center py-4 font-medium">
                                Great! No major weaknesses identified
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <ExclamationTriangleIcon class="h-6 w-6 text-orange-600 mr-3" />
                            <h2 class="text-xl font-bold text-gray-900">Actionable Recommendations</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="assessment.recommendations.length > 0" class="space-y-4">
                            <div
                                v-for="(rec, index) in assessment.recommendations"
                                :key="index"
                                :class="getPriorityColor(rec.priority)"
                                class="border-l-4 p-4 bg-gray-50 rounded"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold">{{ rec.action }}</h4>
                                    <span class="text-xs uppercase font-bold px-2 py-1 rounded">{{ rec.priority }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ rec.benefit }}</p>
                                <Link
                                    v-if="rec.route"
                                    :href="route(rec.route)"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Take Action →
                                </Link>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-4">
                            No specific recommendations at this time
                        </p>
                    </div>
                </div>

                <!-- Missing Documents -->
                <div v-if="assessment.missing_documents.length > 0" class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Missing Documents</h2>
                    </div>
                    <div class="p-6">
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <li v-for="(doc, index) in assessment.missing_documents" :key="index" class="flex items-center">
                                <DocumentCheckIcon class="h-5 w-5 text-gray-400 mr-3" />
                                <span class="text-gray-700">{{ doc }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recommended Visa Types -->
                <div v-if="assessment.recommended_visa_types.length > 0" class="bg-white rounded-lg shadow mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Recommended Visa Types</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="(visa, index) in assessment.recommended_visa_types"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition"
                            >
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ visa.type }}</h4>
                                    <span class="text-sm font-bold text-indigo-600">{{ visa.suitability }}%</span>
                                </div>
                                <p class="text-sm text-gray-600">{{ visa.reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Eligible Countries -->
                <div v-if="assessment.eligible_countries.length > 0" class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Eligible Countries</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="(country, index) in assessment.eligible_countries"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4 text-center hover:border-indigo-300 transition"
                            >
                                <h4 class="font-semibold text-gray-900 mb-2">{{ country.name }}</h4>
                                <div class="text-2xl font-bold text-indigo-600">{{ country.probability }}%</div>
                                <div class="text-xs text-gray-500 mt-1">Success Probability</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
