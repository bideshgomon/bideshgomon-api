<template>
  <Head title="Edit Service Category" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Service Category</h1>
        <p class="mt-1 text-sm text-gray-600">Update category information</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <!-- Module Count Info -->
        <div v-if="serviceCategory.modules_count > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-3">
          <p class="text-sm text-blue-800">
            This category has <strong>{{ serviceCategory.modules_count }}</strong> service module(s) associated with it.
          </p>
        </div>

        <form @submit.prevent="submit">
          <!-- Name -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Category Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.name ? 'border-red-500' : 'border-gray-300'" placeholder="e.g., Visa Services"/>
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Slug -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Slug (URL-friendly identifier)
            </label>
            <input v-model="form.slug" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., visa-services"/>
            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
            <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
          </div>

          <!-- Icon -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Icon Class
            </label>
            <input v-model="form.icon" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., DocumentTextIcon, fa-plane"/>
            <p class="mt-1 text-xs text-gray-500">Heroicons class name or Font Awesome class</p>
            <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
          </div>

          <!-- Description -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Description
            </label>
            <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Brief description of this category"></textarea>
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- Color -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Color (Hex Code)
            </label>
            <div class="flex gap-2">
              <input v-model="form.color" type="color" class="h-10 w-16 border border-gray-300 rounded-md"/>
              <input v-model="form.color" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="#3B82F6"/>
            </div>
            <p v-if="form.errors.color" class="mt-1 text-sm text-red-600">{{ form.errors.color }}</p>
          </div>

          <!-- Sort Order -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Display Order
            </label>
            <input v-model.number="form.sort_order" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="0"/>
            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
            <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
          </div>

          <!-- Status -->
          <div class="mb-6">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"/>
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3">
            <Link :href="route('admin.data.service-categories.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
              {{ form.processing ? 'Updating...' : 'Update Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  serviceCategory: Object,
});

const form = useForm({
  name: props.serviceCategory.name,
  slug: props.serviceCategory.slug,
  icon: props.serviceCategory.icon,
  description: props.serviceCategory.description,
  color: props.serviceCategory.color,
  sort_order: props.serviceCategory.sort_order,
  is_active: props.serviceCategory.is_active,
});

const submit = () => {
  form.put(route('admin.data.service-categories.update', props.serviceCategory.id));
};
</script>
