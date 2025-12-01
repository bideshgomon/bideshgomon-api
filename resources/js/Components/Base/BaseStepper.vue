<script setup>
import { computed } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    activeStep: {
        type: Number,
        default: 0
    },
    variant: {
        type: String,
        default: 'horizontal',
        validator: (value) => ['horizontal', 'vertical'].includes(value)
    },
    clickable: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['step-click'])

const getStepStatus = (index) => {
    if (index < props.activeStep) return 'completed'
    if (index === props.activeStep) return 'active'
    return 'upcoming'
}

const handleStepClick = (index) => {
    if (props.clickable) {
        emit('step-click', index)
    }
}
</script>

<template>
    <div>
        <!-- Horizontal Stepper -->
        <nav v-if="variant === 'horizontal'" aria-label="Progress">
            <ol role="list" class="flex items-center">
                <li
                    v-for="(step, index) in items"
                    :key="index"
                    :class="[
                        'relative',
                        index !== items.length - 1 ? 'pr-8 sm:pr-20 flex-1' : ''
                    ]"
                >
                    <!-- Line connector -->
                    <div
                        v-if="index !== items.length - 1"
                        class="absolute top-4 left-4 -ml-px mt-0.5 h-0.5 w-full"
                        :class="getStepStatus(index) === 'completed' ? 'bg-ocean-500' : 'bg-gray-300'"
                    ></div>
                    
                    <!-- Step -->
                    <button
                        type="button"
                        :class="[
                            'group relative flex items-start',
                            clickable ? 'cursor-pointer' : 'cursor-default'
                        ]"
                        :disabled="!clickable"
                        @click="handleStepClick(index)"
                    >
                        <span class="flex h-9 items-center">
                            <span
                                :class="[
                                    'relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 transition-all duration-300',
                                    getStepStatus(index) === 'completed' 
                                        ? 'bg-ocean-500 border-ocean-500' 
                                        : getStepStatus(index) === 'active'
                                        ? 'border-ocean-500 bg-white'
                                        : 'border-gray-300 bg-white',
                                    clickable && 'group-hover:border-ocean-600'
                                ]"
                            >
                                <svg
                                    v-if="getStepStatus(index) === 'completed'"
                                    class="h-5 w-5 text-white"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span
                                    v-else
                                    :class="[
                                        'text-sm font-semibold',
                                        getStepStatus(index) === 'active' ? 'text-ocean-600' : 'text-gray-500'
                                    ]"
                                >
                                    {{ index + 1 }}
                                </span>
                            </span>
                        </span>
                        <span class="ml-3 flex min-w-0 flex-col">
                            <span
                                :class="[
                                    'text-sm font-medium transition-colors',
                                    getStepStatus(index) === 'active' ? 'text-ocean-600' : 'text-gray-900'
                                ]"
                            >
                                {{ step.title }}
                            </span>
                            <span v-if="step.description" class="text-sm text-gray-500 mt-0.5">
                                {{ step.description }}
                            </span>
                        </span>
                    </button>
                </li>
            </ol>
        </nav>
        
        <!-- Vertical Stepper -->
        <nav v-else aria-label="Progress">
            <ol role="list" class="overflow-hidden">
                <li
                    v-for="(step, index) in items"
                    :key="index"
                    :class="[
                        'relative',
                        index !== items.length - 1 ? 'pb-10' : ''
                    ]"
                >
                    <!-- Line connector -->
                    <div
                        v-if="index !== items.length - 1"
                        class="absolute left-4 top-4 -ml-px mt-0.5 h-full w-0.5"
                        :class="getStepStatus(index) === 'completed' ? 'bg-ocean-500' : 'bg-gray-300'"
                    ></div>
                    
                    <!-- Step -->
                    <button
                        type="button"
                        :class="[
                            'group relative flex items-start',
                            clickable ? 'cursor-pointer' : 'cursor-default'
                        ]"
                        :disabled="!clickable"
                        @click="handleStepClick(index)"
                    >
                        <span class="flex h-9 items-center">
                            <span
                                :class="[
                                    'relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 transition-all duration-300',
                                    getStepStatus(index) === 'completed' 
                                        ? 'bg-ocean-500 border-ocean-500' 
                                        : getStepStatus(index) === 'active'
                                        ? 'border-ocean-500 bg-white'
                                        : 'border-gray-300 bg-white',
                                    clickable && 'group-hover:border-ocean-600'
                                ]"
                            >
                                <svg
                                    v-if="getStepStatus(index) === 'completed'"
                                    class="h-5 w-5 text-white"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span
                                    v-else
                                    :class="[
                                        'text-sm font-semibold',
                                        getStepStatus(index) === 'active' ? 'text-ocean-600' : 'text-gray-500'
                                    ]"
                                >
                                    {{ index + 1 }}
                                </span>
                            </span>
                        </span>
                        <span class="ml-4 flex min-w-0 flex-col">
                            <span
                                :class="[
                                    'text-sm font-medium transition-colors',
                                    getStepStatus(index) === 'active' ? 'text-ocean-600' : 'text-gray-900'
                                ]"
                            >
                                {{ step.title }}
                            </span>
                            <span v-if="step.description" class="text-sm text-gray-500 mt-1">
                                {{ step.description }}
                            </span>
                        </span>
                    </button>
                </li>
            </ol>
        </nav>
    </div>
</template>
