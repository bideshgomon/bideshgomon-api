<script setup>
import { ref, computed, watch } from 'vue';
import {
  ArrowUpTrayIcon,
  XMarkIcon,
  DocumentIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  requiredDocuments: {
    type: Array,
    default: () => [],
  },
  maxFileSize: {
    type: Number,
    default: 10, // MB
  },
  acceptedFormats: {
    type: Array,
    default: () => ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'],
  },
});

const emit = defineEmits(['update:modelValue']);

const files = ref([]);
const dragOver = ref(false);
const uploadProgress = ref({});

const fileInput = ref(null);

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue.length > 0) {
    files.value = newValue;
  }
}, { immediate: true });

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const getFileIcon = (file) => {
  if (file.type && file.type.includes('pdf')) {
    return '📄';
  } else if (file.type && file.type.includes('image')) {
    return '🖼️';
  }
  return '📎';
};

const isValidFile = (file) => {
  const maxSize = props.maxFileSize * 1024 * 1024; // Convert MB to bytes
  
  if (file.size > maxSize) {
    return { valid: false, message: `File size exceeds ${props.maxFileSize}MB` };
  }
  
  if (!props.acceptedFormats.includes(file.type)) {
    return { valid: false, message: 'File format not supported' };
  }
  
  return { valid: true };
};

const handleFiles = (fileList) => {
  const newFiles = Array.from(fileList);
  
  newFiles.forEach((file) => {
    const validation = isValidFile(file);
    
    if (!validation.valid) {
      alert(`${file.name}: ${validation.message}`);
      return;
    }
    
    // Create file object with metadata
    const fileObj = {
      file: file,
      name: file.name,
      size: file.size,
      type: file.type,
      documentType: '', // To be set by user
      preview: null,
      id: Date.now() + Math.random(), // Temporary ID
    };
    
    // Generate preview for images
    if (file.type.includes('image')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        fileObj.preview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
    
    files.value.push(fileObj);
  });
  
  emit('update:modelValue', files.value);
};

const handleDrop = (e) => {
  dragOver.value = false;
  const droppedFiles = e.dataTransfer.files;
  handleFiles(droppedFiles);
};

const handleFileInput = (e) => {
  const selectedFiles = e.target.files;
  handleFiles(selectedFiles);
  // Reset input
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const removeFile = (fileId) => {
  files.value = files.value.filter(f => f.id !== fileId);
  emit('update:modelValue', files.value);
};

const openFileDialog = () => {
  fileInput.value?.click();
};

const getDocumentTypeLabel = (docType) => {
  return docType || 'Not specified';
};

const uploadedDocumentTypes = computed(() => {
  return files.value.map(f => f.documentType).filter(t => t);
});

const missingDocuments = computed(() => {
  return props.requiredDocuments.filter(
    doc => !uploadedDocumentTypes.value.includes(doc)
  );
});

const allRequiredUploaded = computed(() => {
  return missingDocuments.value.length === 0 && files.value.length > 0;
});
</script>

<template>
  <div class="space-y-4">
    <!-- Required Documents Checklist -->
    <div v-if="requiredDocuments.length > 0" class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
      <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
        <DocumentIcon class="h-5 w-5 text-blue-600" />
        Required Documents
      </h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <div
          v-for="doc in requiredDocuments"
          :key="doc"
          class="flex items-center gap-2 text-sm"
        >
          <CheckCircleIcon
            :class="[
              'h-4 w-4',
              uploadedDocumentTypes.includes(doc)
                ? 'text-green-600'
                : 'text-gray-400 dark:text-gray-600'
            ]"
          />
          <span
            :class="[
              uploadedDocumentTypes.includes(doc)
                ? 'text-green-700 dark:text-green-400 font-medium'
                : 'text-gray-600 dark:text-gray-400'
            ]"
          >
            {{ doc }}
          </span>
        </div>
      </div>
    </div>

    <!-- Upload Area -->
    <div
      @drop.prevent="handleDrop"
      @dragover.prevent="dragOver = true"
      @dragleave.prevent="dragOver = false"
      @click="openFileDialog"
      :class="[
        'border-2 border-dashed rounded-xl p-8 transition-all cursor-pointer',
        dragOver
          ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 scale-[1.02]'
          : 'border-gray-300 dark:border-gray-600 hover:border-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50'
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        :accept="acceptedFormats.join(',')"
        @change="handleFileInput"
        class="hidden"
      />
      
      <div class="text-center">
        <ArrowUpTrayIcon class="h-12 w-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
        <p class="text-gray-900 dark:text-white font-medium mb-1">
          Drop files here or click to browse
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Max {{ maxFileSize }}MB per file • PDF, JPG, PNG supported
        </p>
      </div>
    </div>

    <!-- Uploaded Files List -->
    <div v-if="files.length > 0" class="space-y-3">
      <div class="flex items-center justify-between">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
          Uploaded Documents ({{ files.length }})
        </h4>
        <div v-if="allRequiredUploaded" class="flex items-center gap-1 text-green-600 text-sm font-medium">
          <CheckCircleIcon class="h-4 w-4" />
          All required documents uploaded
        </div>
        <div v-else-if="missingDocuments.length > 0" class="flex items-center gap-1 text-amber-600 text-sm font-medium">
          <ExclamationCircleIcon class="h-4 w-4" />
          {{ missingDocuments.length }} document(s) missing
        </div>
      </div>

      <div class="space-y-2">
        <div
          v-for="fileObj in files"
          :key="fileObj.id"
          class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow"
        >
          <div class="flex items-start gap-4">
            <!-- File Preview/Icon -->
            <div class="flex-shrink-0">
              <div v-if="fileObj.preview" class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                <img :src="fileObj.preview" :alt="fileObj.name" class="w-full h-full object-cover" />
              </div>
              <div v-else class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center text-3xl">
                {{ getFileIcon(fileObj) }}
              </div>
            </div>

            <!-- File Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ fileObj.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatFileSize(fileObj.size) }}
                  </p>
                </div>
                <button
                  @click.stop="removeFile(fileObj.id)"
                  class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                >
                  <XMarkIcon class="h-5 w-5" />
                </button>
              </div>

              <!-- Document Type Selector -->
              <select
                v-model="fileObj.documentType"
                @click.stop
                class="w-full text-xs px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white"
              >
                <option value="">Select document type...</option>
                <option v-for="docType in requiredDocuments" :key="docType" :value="docType">
                  {{ docType }}
                </option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8">
      <DocumentIcon class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
      <p class="text-sm text-gray-500 dark:text-gray-400">
        No documents uploaded yet
      </p>
    </div>
  </div>
</template>
