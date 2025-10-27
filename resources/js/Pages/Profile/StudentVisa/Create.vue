<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3'; // Import router
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    destination_country_id: '',
    university_id: '',
    course_id: '',
    intended_intake_month: '',
    intended_intake_year: new Date().getFullYear() + 1,
    current_education_level: '',
    english_proficiency_test: '',
    english_proficiency_score: '',
    user_notes: '',
});

// --- Dynamic Loading State ---
const universities = ref([]);
const courses = ref([]);
const loadingUniversities = ref(false);
const loadingCourses = ref(false);

// --- Watchers for Dynamic Loading ---
watch(() => form.destination_country_id, async (newCountryId) => {
    form.university_id = '';
    form.course_id = '';
    universities.value = [];
    courses.value = [];
    if (newCountryId) {
        loadingUniversities.value = true;
        try {
            // ** Uses Public API endpoint **
            const response = await axios.get(route('api.public.universities.search'), { params: { country_id: newCountryId } });
            universities.value = response.data.data || response.data;
        } catch (error) { console.error("Error fetching universities:", error); }
        finally { loadingUniversities.value = false; }
    }
});

watch(() => form.university_id, async (newUniversityId) => {
    form.course_id = '';
    courses.value = [];
    if (newUniversityId) {
        loadingCourses.value = true;
        try {
            // ** Uses Public API endpoint **
            const response = await axios.get(route('api.public.courses.search'), { params: { university_id: newUniversityId } });
             courses.value = response.data.data || response.data;
        } catch (error) { console.error("Error fetching courses:", error); }
        finally { loadingCourses.value = false; }
    }
});
// --- End Dynamic Loading ---

// --- Form Submission ---
const submit = () => {
    // Use the correct API route name for storing user's application
    form.post(route('api.student-visa-applications.store'), {
        onSuccess: () => {
             // Redirect to index after successful submission
             router.visit(route('profile.student-visa.index'), {
                 // Optionally: Add a success flash message here if your layout supports it
                 // data: { success_message: 'Student Visa Application submitted successfully!' }
             });
        },
        onError: (errors) => {
            console.error("Error submitting student visa application:", errors);
            // Errors are automatically displayed by InputError components
            alert('Failed to submit application. Please check the form errors.'); // Simple fallback
        }
    });
};

// --- Static Data for Selects ---
const intakeYears = Array.from({length: 10}, (_, i) => new Date().getFullYear() + i);
const intakeMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const eduLevels = ["SSC", "HSC", "Diploma", "Bachelor's Degree", "Master's Degree", "PhD"];
const engTests = ["IELTS", "TOEFL", "PTE", "Duolingo", "Other", "None Required"];

</script>

<template>
    <Head title="Apply for Student Visa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apply for Student Visa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                         <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="destination_country_id" value="Destination Country *" />
                                <select id="destination_country_id" v-model="form.destination_country_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>-- Select Country --</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.destination_country_id" />
                            </div>

                             <div>
                                <InputLabel for="university_id" value="Preferred University (Optional)" />
                                <select id="university_id" v-model="form.university_id" :disabled="!form.destination_country_id || loadingUniversities" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <option value="" v-if="!form.destination_country_id">Select Country First</option>
                                    <option value="" v-else-if="loadingUniversities">Loading...</option>
                                    <option value="" v-else>-- Select University (Optional) --</option>
                                    <option v-for="uni in universities" :key="uni.id" :value="uni.id">{{ uni.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.university_id" />
                            </div>

                             <div>
                                <InputLabel for="course_id" value="Preferred Course (Optional)" />
                                <select id="course_id" v-model="form.course_id" :disabled="!form.university_id || loadingCourses" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                     <option value="" v-if="!form.university_id">Select University First</option>
                                    <option value="" v-else-if="loadingCourses">Loading...</option>
                                    <option value="" v-else>-- Select Course (Optional) --</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.course_id" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="intended_intake_month" value="Intended Intake Month" />
                                    <select id="intended_intake_month" v-model="form.intended_intake_month" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Select Month --</option>
                                        <option v-for="month in intakeMonths" :key="month" :value="month">{{ month }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.intended_intake_month" />
                                </div>
                                <div>
                                    <InputLabel for="intended_intake_year" value="Intended Intake Year" />
                                    <select id="intended_intake_year" v-model="form.intended_intake_year" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Select Year --</option>
                                        <option v-for="year in intakeYears" :key="year" :value="year">{{ year }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.intended_intake_year" />
                                </div>
                            </div>

                             <div>
                                <InputLabel for="current_education_level" value="Highest Completed Education Level" />
                                <select id="current_education_level" v-model="form.current_education_level" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Select Level --</option>
                                     <option v-for="level in eduLevels" :key="level" :value="level">{{ level }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.current_education_level" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="english_proficiency_test" value="English Test Taken" />
                                    <select id="english_proficiency_test" v-model="form.english_proficiency_test" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Select Test --</option>
                                         <option v-for="test in engTests" :key="test" :value="test">{{ test }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.english_proficiency_test" />
                                </div>
                                <div>
                                    <InputLabel for="english_proficiency_score" value="Overall Score/Band" />
                                    <TextInput id="english_proficiency_score" type="text" class="mt-1 block w-full" v-model="form.english_proficiency_score" placeholder="e.g., 7.5 or 100"/>
                                    <InputError class="mt-2" :message="form.errors.english_proficiency_score" />
                                </div>
                            </div>

                             <div>
                                <InputLabel for="user_notes" value="Additional Notes / Questions (Optional)" />
                                <TextareaInput id="user_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.user_notes" rows="4" placeholder="Mention specific requirements, ask questions, etc."/>
                                <InputError class="mt-2" :message="form.errors.user_notes" />
                            </div>

                            <div class="flex items-center justify-end pt-4 border-t border-gray-200 mt-6">
                                <Link :href="route('profile.student-visa.index')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </Link>

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Submit Application
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>