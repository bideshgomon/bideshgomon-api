<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Import components
import Modal from '@/Components/Modal.vue'
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue'
import FlowButton from '@/Components/Rhythmic/FlowButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import { SparklesIcon, PlusIcon, PencilSquareIcon, TrashIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    userProfile: Object,
})

// State
const userSkills = ref([])
const allSkills = ref([])
const isLoading = ref(false)
const showModal = ref(false)
const isEditMode = ref(false)
const currentSkillId = ref(null)
const searchQuery = ref('')
const selectedCategory = ref('All')

// Form
const form = useForm({
    skill_id: '',
    proficiency_level: 'Intermediate',
    years_of_experience: '0',
})

// Proficiency levels
const proficiencyLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert']

// Categories
const categories = computed(() => {
    const cats = ['All', ...new Set(allSkills.value.map(skill => skill.category))]
    return cats
})

// Filtered skills
const filteredSkills = computed(() => {
    let skills = allSkills.value

    if (selectedCategory.value !== 'All') {
        skills = skills.filter(skill => skill.category === selectedCategory.value)
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        skills = skills.filter(skill => 
            skill.name.toLowerCase().includes(query) ||
            skill.description?.toLowerCase().includes(query)
        )
    }

    return skills
})

// Grouped user skills
const groupedUserSkills = computed(() => {
    const grouped = {}
    userSkills.value.forEach(userSkill => {
        const category = userSkill.skill.category || 'Other'
        if (!grouped[category]) {
            grouped[category] = []
        }
        grouped[category].push(userSkill)
    })
    return grouped
})

// Fetch data
onMounted(() => {
    fetchUserSkills()
    fetchAllSkills()
})

const fetchUserSkills = async () => {
    isLoading.value = true
    try {
        const response = await axios.get(route('api.profile.skills.index'))
        userSkills.value = response.data
    } catch (error) {
        console.error('Error fetching user skills:', error)
    } finally {
        isLoading.value = false
    }
}

const fetchAllSkills = async () => {
    try {
        const response = await axios.get(route('api.skills.index'))
        allSkills.value = response.data
    } catch (error) {
        console.error('Error fetching all skills:', error)
    }
}

// Modal functions
const openModal = (userSkill = null) => {
    if (userSkill) {
        isEditMode.value = true
        currentSkillId.value = userSkill.id
        form.skill_id = userSkill.skill_id
        form.proficiency_level = userSkill.proficiency_level
        form.years_of_experience = userSkill.years_of_experience
    } else {
        isEditMode.value = false
        currentSkillId.value = null
        form.reset()
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

const submit = () => {
    if (isEditMode.value) {
        form.processing = true
        axios.put(route('api.profile.skills.update', currentSkillId.value), {
            proficiency_level: form.proficiency_level,
            years_of_experience: form.years_of_experience,
        })
            .then(() => {
                form.processing = false
                closeModal()
                fetchUserSkills()
            })
            .catch(error => {
                form.processing = false
                if (error.response?.data?.errors) {
                    Object.keys(error.response.data.errors).forEach(key => {
                        form.errors[key] = error.response.data.errors[key][0]
                    })
                }
            })
    } else {
        form.processing = true
        axios.post(route('api.profile.skills.store'), {
            skill_id: form.skill_id,
            proficiency_level: form.proficiency_level,
            years_of_experience: form.years_of_experience,
        })
            .then(() => {
                form.processing = false
                closeModal()
                fetchUserSkills()
            })
            .catch(error => {
                form.processing = false
                if (error.response?.data?.errors) {
                    Object.keys(error.response.data.errors).forEach(key => {
                        form.errors[key] = error.response.data.errors[key][0]
                    })
                }
            })
    }
}

const deleteSkill = (userSkillId) => {
    if (confirm('Are you sure you want to remove this skill?')) {
        axios.delete(route('api.profile.skills.destroy', userSkillId))
            .then(() => {
                fetchUserSkills()
            })
            .catch(error => {
                console.error('Error deleting skill:', error)
                alert('Failed to delete skill')
            })
    }
}

// Get skill name by ID
const getSkillName = (skillId) => {
    const skill = allSkills.value.find(s => s.id === skillId)
    return skill ? skill.name : ''
}

// Get proficiency color
const getProficiencyColor = (level) => {
    const colors = {
        'Beginner': 'bg-gray-100 text-gray-800',
        'Intermediate': 'bg-blue-100 text-blue-800',
        'Advanced': 'bg-blue-100 text-blue-800',
        'Expert': 'bg-green-100 text-green-800',
    }
    return colors[level] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <section>
        <header class="mb-rhythm-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center shadow-sm">
                        <SparklesIcon class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-xl text-gray-800">Skills & Expertise</h2>
                        <p class="text-xs text-gray-500">
                            Professional skills and proficiency levels
                        </p>
                    </div>
                </div>
                <FlowButton @click="openModal()" variant="primary">
                    <template #icon-left><PlusIcon class="w-4 h-4" /></template>
                    <span class="hidden sm:inline">Add Skill</span>
                    <span class="sm:hidden">Add</span>
                </FlowButton>
            </div>
        </header>

        <div class="space-y-6">

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="userSkills.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <SparklesIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No skills added</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first skill.</p>
                <div class="mt-6">
                    <PrimaryButton @click="openModal()">
                        <PlusIcon class="w-5 h-5 md:w-6 md:h-6 mr-2" />
                        Add Your First Skill
                    </PrimaryButton>
                </div>
            </div>

            <!-- Skills Grid by Category -->
            <div v-else class="space-y-4">
                <div 
                    v-for="userSkill in userSkills"
                    :key="userSkill.id"
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="h-1 bg-gradient-to-r from-blue-500 to-green-500"></div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900">{{ userSkill.skill.name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ userSkill.skill.category || 'Skill' }}</p>
                            </div>
                            <span :class="getProficiencyColor(userSkill.proficiency_level)" class="px-2.5 py-1 text-xs font-medium rounded-full flex-shrink-0">
                                {{ userSkill.proficiency_level }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1.5">
                                <span>Proficiency</span>
                                <span class="font-semibold">{{ userSkill.years_of_experience > 0 ? userSkill.years_of_experience : 0 }} years</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-300"
                                    :style="{ width: (userSkill.proficiency_level === 'Expert' ? '100' : userSkill.proficiency_level === 'Advanced' ? '75' : userSkill.proficiency_level === 'Intermediate' ? '50' : '25') + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                        <div class="flex gap-2">
                            <button
                                @click="openModal(userSkill)"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium text-sm transition-colors touch-manipulation"
                            >
                                <PencilSquareIcon class="h-5 w-5" />
                                <span>Edit</span>
                            </button>
                            <button
                                @click="deleteSkill(userSkill.id)"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-300 text-red-600 hover:bg-red-50 rounded-lg font-medium text-sm transition-colors touch-manipulation"
                            >
                                <TrashIcon class="h-5 w-5" />
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Add More Button -->
                <button
                    @click="openModal()"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-base"
                >
                    <PlusIcon class="h-5 w-5" />
                    <span>ADD MORE SKILLS</span>
                </button>
            </div>
        </div>

        <!-- Add/Edit Skill Modal -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? 'Edit Skill' : 'Add New Skill' }}
                </h3>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Search and Filter -->
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="search" value="Search Skills" />
                            <div class="relative mt-1">
                                <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                <TextInput
                                    id="search"
                                    v-model="searchQuery"
                                    type="text"
                                    class="pl-10 w-full"
                                    placeholder="Search by name..."
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="category-filter" value="Filter by Category" />
                            <select
                                id="category-filter"
                                v-model="selectedCategory"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="cat in categories" :key="cat" :value="cat">
                                    {{ cat }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Skill Selection -->
                    <div>
                        <InputLabel for="skill_id" value="Select Skill *" />
                        <select
                            id="skill_id"
                            v-model="form.skill_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :disabled="isEditMode"
                        >
                            <option value="">Choose a skill...</option>
                            <optgroup v-for="category in categories.filter(c => c !== 'All')" :key="category" :label="category">
                                <option
                                    v-for="skill in filteredSkills.filter(s => s.category === category)"
                                    :key="skill.id"
                                    :value="skill.id"
                                >
                                    {{ skill.name }}
                                </option>
                            </optgroup>
                        </select>
                        <InputError class="mt-2" :message="form.errors?.skill_id" />
                    </div>

                    <!-- Proficiency Level -->
                    <div>
                        <InputLabel for="proficiency_level" value="Proficiency Level *" />
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                            <label
                                v-for="level in proficiencyLevels"
                                :key="level"
                                :class="[
                                    'relative flex items-center justify-center p-3 cursor-pointer rounded-lg border-2 transition',
                                    form.proficiency_level === level
                                        ? 'border-indigo-600 bg-indigo-50'
                                        : 'border-gray-300 hover:border-gray-400'
                                ]"
                            >
                                <input
                                    type="radio"
                                    :value="level"
                                    v-model="form.proficiency_level"
                                    class="sr-only"
                                />
                                <span :class="form.proficiency_level === level ? 'text-indigo-900 font-semibold' : 'text-gray-900'">
                                    {{ level }}
                                </span>
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors?.proficiency_level" />
                    </div>

                    <!-- Years of Experience -->
                    <div>
                        <InputLabel for="years_of_experience" value="Years of Experience" />
                        <TextInput
                            id="years_of_experience"
                            v-model="form.years_of_experience"
                            type="number"
                            min="0"
                            max="50"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors?.years_of_experience" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t">
                        <SecondaryButton @click="closeModal" type="button" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing" class="w-full sm:w-auto py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
                            {{ editMode ? 'Update Skill' : 'Add Skill' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>


