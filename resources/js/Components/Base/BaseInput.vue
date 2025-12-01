<script setup>
import { computed, ref } from 'vue'
import { EyeIcon, EyeSlashIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: null
    },
    placeholder: {
        type: String,
        default: ''
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
    icon: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

const showPassword = ref(false)
const inputRef = ref(null)

const inputType = computed(() => {
    if (props.type === 'password' && showPassword.value) {
        return 'text'
    }
    return props.type
})

const inputClasses = computed(() => {
    const classes = [
        'block w-full rounded-rhythm-md transition-all duration-300',
        'border-2 focus:ring-4 focus:ring-offset-0',
        'disabled:bg-gray-100 disabled:cursor-not-allowed'
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
    
    // Icon padding
    if (props.icon) {
        classes.push('pl-11')
    }
    
    if (props.type === 'password') {
        classes.push('pr-11')
    }
    
    return classes.join(' ')
})

const handleInput = (event) => {
    emit('update:modelValue', event.target.value)
}

const handleBlur = (event) => {
    emit('blur', event)
}

const handleFocus = (event) => {
    emit('focus', event)
}

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const focusInput = () => {
    inputRef.value?.focus()
}

defineExpose({ focusInput })
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
        
        <!-- Input wrapper -->
        <div class="relative">
            <!-- Leading icon -->
            <div
                v-if="icon"
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
                <component
                    :is="icon"
                    class="h-5 w-5 text-gray-400"
                />
            </div>
            
            <!-- Input field -->
            <input
                ref="inputRef"
                :type="inputType"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :class="inputClasses"
                @input="handleInput"
                @blur="handleBlur"
                @focus="handleFocus"
            />
            
            <!-- Password toggle -->
            <button
                v-if="type === 'password'"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                @click="togglePasswordVisibility"
            >
                <EyeIcon v-if="!showPassword" class="h-5 w-5" />
                <EyeSlashIcon v-else class="h-5 w-5" />
            </button>
            
            <!-- Error icon -->
            <div
                v-if="error"
                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                :class="{ 'pr-11': type === 'password' }"
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
