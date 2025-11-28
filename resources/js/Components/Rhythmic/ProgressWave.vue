<template>
    <div class="relative">
        <!-- Progress bar container -->
        <div class="flex items-center justify-between mb-rhythm-lg">
            <div 
                v-for="(step, index) in steps" 
                :key="index"
                class="flex-1 relative"
            >
                <!-- Connector line -->
                <div 
                    v-if="index < steps.length - 1"
                    :class="[
                        'absolute top-1/2 left-1/2 w-full h-1 -translate-y-1/2 transition-all duration-600',
                        index < currentStep ? progressColorClass : 'bg-gray-200',
                    ]"
                    :style="{ zIndex: 0 }"
                ></div>

                <!-- Step circle -->
                <div class="relative flex flex-col items-center" :style="{ zIndex: 1 }">
                    <div 
                        :class="[
                            'w-12 h-12 rounded-full flex items-center justify-center',
                            'font-bold text-sm transition-all duration-400',
                            'border-4 border-white shadow-rhythm',
                            getStepClass(index),
                        ]"
                    >
                        <!-- Checkmark for completed steps -->
                        <svg 
                            v-if="index < currentStep" 
                            class="w-6 h-6 text-white" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <!-- Step number for incomplete steps -->
                        <span v-else :class="index === currentStep ? 'text-white' : 'text-gray-400'">
                            {{ index + 1 }}
                        </span>
                    </div>

                    <!-- Step label -->
                    <div class="mt-3 text-center">
                        <p 
                            :class="[
                                'text-sm font-semibold transition-colors duration-300',
                                index <= currentStep ? stepLabelActiveClass : 'text-gray-400',
                            ]"
                        >
                            {{ step.label }}
                        </p>
                        <p 
                            v-if="step.description && showDescriptions" 
                            class="text-xs text-gray-500 mt-1 max-w-[120px]"
                        >
                            {{ step.description }}
                        </p>
                    </div>

                    <!-- Pulse animation for current step -->
                    <div 
                        v-if="index === currentStep && animated"
                        :class="[
                            'absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full',
                            'animate-ping opacity-75',
                            pulseBgClass,
                        ]"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Optional progress percentage -->
        <div v-if="showPercentage" class="text-center mt-rhythm-md">
            <p class="text-sm text-gray-600">
                Progress: 
                <span :class="['font-bold text-lg', progressTextColorClass]">
                    {{ progressPercentage }}%
                </span>
            </p>
            <!-- Visual progress bar -->
            <div class="w-full h-2 bg-gray-200 rounded-full mt-2 overflow-hidden">
                <div 
                    :class="[
                        'h-full transition-all duration-600 ease-out',
                        progressColorClass,
                    ]"
                    :style="{ width: `${progressPercentage}%` }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    steps: {
        type: Array,
        required: true,
        // Format: [{ label: 'Step 1', description: 'Optional' }, ...]
    },
    currentStep: {
        type: Number,
        default: 0,
    },
    variant: {
        type: String,
        default: 'ocean', // ocean, growth, sunrise
    },
    animated: {
        type: Boolean,
        default: true,
    },
    showDescriptions: {
        type: Boolean,
        default: true,
    },
    showPercentage: {
        type: Boolean,
        default: false,
    },
});

const progressPercentage = computed(() => {
    if (props.steps.length === 0) return 0;
    return Math.round((props.currentStep / (props.steps.length - 1)) * 100);
});

const progressColorClass = computed(() => {
    const variants = {
        ocean: 'bg-ocean-500',
        growth: 'bg-growth-500',
        sunrise: 'bg-sunrise-500',
    };
    return variants[props.variant] || variants.ocean;
});

const progressTextColorClass = computed(() => {
    const variants = {
        ocean: 'text-ocean-600',
        growth: 'text-growth-600',
        sunrise: 'text-sunrise-600',
    };
    return variants[props.variant] || variants.ocean;
});

const stepLabelActiveClass = computed(() => {
    const variants = {
        ocean: 'text-ocean-700',
        growth: 'text-growth-700',
        sunrise: 'text-sunrise-700',
    };
    return variants[props.variant] || variants.ocean;
});

const pulseBgClass = computed(() => {
    const variants = {
        ocean: 'bg-ocean-400',
        growth: 'bg-growth-400',
        sunrise: 'bg-sunrise-400',
    };
    return variants[props.variant] || variants.ocean;
});

const getStepClass = (index) => {
    if (index < props.currentStep) {
        // Completed steps
        const variants = {
            ocean: 'bg-ocean-500',
            growth: 'bg-growth-500',
            sunrise: 'bg-sunrise-500',
        };
        return variants[props.variant] || variants.ocean;
    } else if (index === props.currentStep) {
        // Current step
        const variants = {
            ocean: 'bg-ocean-500 scale-110 shadow-glow-ocean',
            growth: 'bg-growth-500 scale-110 shadow-glow-growth',
            sunrise: 'bg-sunrise-500 scale-110 shadow-glow-sunrise',
        };
        return variants[props.variant] || variants.ocean;
    } else {
        // Future steps
        return 'bg-gray-200';
    }
};
</script>
