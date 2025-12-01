<script setup>
import { computed } from 'vue'
import { ChevronDownIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        default: null
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    },
    error: {
        type: String,
        default: null
    },
    hint: {
        type: String,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    valueKey: {
        type: String,
        default: 'value'
    },
    labelKey: {
        type: String,
        default: 'label'
    },
    emptyText: {
        type: String,
        default: 'No options available'
    }
})

const emit = defineEmits(['update:modelValue', 'change'])

const selectClasses = computed(() => {
    const classes = [
        'block w-full rounded-rhythm-md transition-all duration-300',
        'border-2 focus:ring-4 focus:ring-offset-0',
        'disabled:bg-gray-100 disabled:cursor-not-allowed',
        'appearance-none pr-10 bg-white'
    ]
    
    // Size variants
    const sizeMap = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-3 text-base',
        lg: 'px-5 py-4 text-lg'
    }
    classes.push(sizeMap[props.size])
    
    // Error state
    if (props.error) {
        classes.push('border-red-400 focus:border-red-500 focus:ring-red-200')
    } else {
        classes.push('border-gray-300 focus:border-ocean-500 focus:ring-ocean-200')
    }
    
    return classes.join(' ')
})

const handleChange = (event) => {
    const value = event.target.value
    emit('update:modelValue', value)
    emit('change', value)
}

const getOptionValue = (option) => {
    if (typeof option === 'object' && option !== null) {
        return option[props.valueKey]
    }
    return option
}

const getOptionLabel = (option) => {
    if (typeof option === 'object' && option !== null) {
        return option[props.labelKey]
    }
    return option
}

const isOptionDisabled = (option) => {
    if (typeof option === 'object' && option !== null) {
        return option.disabled === true
    }
    return false
}
</script>

<template>
    <div class="w-full">
        <!-- Label -->
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 mb-2"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        
        <!-- Select wrapper -->
        <div class="relative">
            <!-- Select element -->
            <select
                :value="modelValue"
                :disabled="disabled"
                :required="required"
                :class="selectClasses"
                @change="handleChange"
            >
                <!-- Placeholder option -->
                <option value="" disabled>{{ placeholder }}</option>
                
                <!-- Empty state -->
                <option v-if="options.length === 0" value="" disabled>
                    {{ emptyText }}
                </option>
                
                <!-- Options -->
                <option
                    v-for="(option, index) in options"
                    :key="index"
                    :value="getOptionValue(option)"
                    :disabled="isOptionDisabled(option)"
                >
                    {{ getOptionLabel(option) }}
                </option>
            </select>
            
            <!-- Chevron icon -->
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <ChevronDownIcon class="h-5 w-5 text-gray-400" />
            </div>
            
            <!-- Error icon -->
            <div
                v-if="error"
                class="absolute inset-y-0 right-0 pr-10 flex items-center pointer-events-none"
            >
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" />
            </div>
        </div>
        
        <!-- Error message -->
        <p
            v-if="error"
            class="mt-2 text-sm text-red-600 flex items-center gap-1"
        >
            <ExclamationCircleIcon class="h-4 w-4 flex-shrink-0" />
            {{ error }}
        </p>
        
        <!-- Hint text -->
        <p
            v-else-if="hint"
            class="mt-2 text-sm text-gray-500"
        >
            {{ hint }}
        </p>
    </div>
</template>
