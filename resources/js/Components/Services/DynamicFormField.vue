<script setup>
import { computed } from 'vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import DateInput from '@/Components/DateInput.vue'
import { useBangladeshFormat } from '@/Composables/useBangladeshFormat'

const { formatDate, parseDateToISO } = useBangladeshFormat()

const props = defineProps({
  field: {
    type: Object,
    required: true
  },
  modelValue: {
    default: null
  },
  error: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:modelValue'])

const fieldValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Parse options JSON if available
const fieldOptions = computed(() => {
  if (!props.field.options) return []
  
  try {
    if (typeof props.field.options === 'string') {
      return JSON.parse(props.field.options)
    }
    return props.field.options
  } catch (e) {
    console.error('Error parsing field options:', e)
    return []
  }
})

// Get placeholder text
const placeholder = computed(() => {
  return props.field.placeholder || `Enter ${props.field.field_label.toLowerCase()}`
})

// Handle file upload
const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    emit('update:modelValue', file)
  }
}
</script>

<template>
  <div :class="`col-span-12 md:col-span-${field.column_width || 12}`">
    <!-- Text Input -->
    <div v-if="field.field_type === 'text'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="text"
        :placeholder="placeholder"
        :required="field.is_required"
        :maxlength="field.max_length"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Email Input -->
    <div v-else-if="field.field_type === 'email'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="email"
        :placeholder="placeholder"
        :required="field.is_required"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Tel Input -->
    <div v-else-if="field.field_type === 'tel'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="tel"
        :placeholder="placeholder || '01712-345678'"
        :required="field.is_required"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Number Input -->
    <div v-else-if="field.field_type === 'number'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="number"
        :placeholder="placeholder"
        :required="field.is_required"
        :min="field.min_value"
        :max="field.max_value"
        :step="field.step || 1"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Date Input -->
    <div v-else-if="field.field_type === 'date'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <DateInput
        :id="field.field_name"
        v-model="fieldValue"
        :required="field.is_required"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Textarea -->
    <div v-else-if="field.field_type === 'textarea'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <textarea
        :id="field.field_name"
        v-model="fieldValue"
        :placeholder="placeholder"
        :required="field.is_required"
        :maxlength="field.max_length"
        :rows="field.rows || 4"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Select Dropdown -->
    <div v-else-if="field.field_type === 'select'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <select
        :id="field.field_name"
        v-model="fieldValue"
        :required="field.is_required"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
      >
        <option value="">{{ placeholder }}</option>
        <option
          v-for="option in fieldOptions"
          :key="option.value || option"
          :value="option.value || option"
        >
          {{ option.label || option }}
        </option>
      </select>
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Radio Buttons -->
    <div v-else-if="field.field_type === 'radio'" class="space-y-2">
      <InputLabel :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <div class="space-y-2">
        <label
          v-for="option in fieldOptions"
          :key="option.value || option"
          class="flex items-center space-x-2"
        >
          <input
            type="radio"
            :name="field.field_name"
            :value="option.value || option"
            v-model="fieldValue"
            :required="field.is_required"
            class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          />
          <span class="text-sm text-gray-700">{{ option.label || option }}</span>
        </label>
      </div>
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Checkbox (Single) -->
    <div v-else-if="field.field_type === 'checkbox'" class="space-y-2">
      <label class="flex items-center space-x-2">
        <input
          type="checkbox"
          :id="field.field_name"
          v-model="fieldValue"
          :required="field.is_required"
          class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
        />
        <span class="text-sm text-gray-700">
          {{ field.field_label }}
          <span v-if="field.is_required" class="text-red-500">*</span>
        </span>
      </label>
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Checkboxes (Multiple) -->
    <div v-else-if="field.field_type === 'checkboxes'" class="space-y-2">
      <InputLabel :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <div class="space-y-2">
        <label
          v-for="option in fieldOptions"
          :key="option.value || option"
          class="flex items-center space-x-2"
        >
          <input
            type="checkbox"
            :value="option.value || option"
            v-model="fieldValue"
            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          />
          <span class="text-sm text-gray-700">{{ option.label || option }}</span>
        </label>
      </div>
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- File Upload -->
    <div v-else-if="field.field_type === 'file'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <input
        :id="field.field_name"
        type="file"
        @change="handleFileChange"
        :required="field.is_required"
        :accept="field.accepted_file_types"
        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <p v-if="field.max_file_size" class="text-xs text-gray-500 mt-1">
        Maximum file size: {{ Math.round(field.max_file_size / 1024) }} KB
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- URL Input -->
    <div v-else-if="field.field_type === 'url'" class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="url"
        :placeholder="placeholder || 'https://example.com'"
        :required="field.is_required"
        class="mt-1 block w-full"
      />
      <p v-if="field.help_text" class="text-xs text-gray-500 mt-1">
        {{ field.help_text }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>

    <!-- Hidden Input -->
    <input
      v-else-if="field.field_type === 'hidden'"
      type="hidden"
      :id="field.field_name"
      v-model="fieldValue"
    />

    <!-- Divider -->
    <div v-else-if="field.field_type === 'divider'" class="col-span-12">
      <hr class="my-6 border-gray-200" />
    </div>

    <!-- Heading -->
    <div v-else-if="field.field_type === 'heading'" class="col-span-12">
      <h3 class="text-lg font-semibold text-gray-900 mb-2">
        {{ field.field_label }}
      </h3>
      <p v-if="field.help_text" class="text-sm text-gray-600">
        {{ field.help_text }}
      </p>
    </div>

    <!-- Unsupported field type fallback -->
    <div v-else class="space-y-1">
      <InputLabel :for="field.field_name" :required="field.is_required">
        {{ field.field_label }}
      </InputLabel>
      <TextInput
        :id="field.field_name"
        v-model="fieldValue"
        type="text"
        :placeholder="placeholder"
        :required="field.is_required"
        class="mt-1 block w-full"
      />
      <p class="text-xs text-orange-600">
        Unsupported field type: {{ field.field_type }}
      </p>
      <InputError :message="error" class="mt-1" />
    </div>
  </div>
</template>
