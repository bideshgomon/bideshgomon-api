<template>
  <Head title="Edit Email Template" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Email Template</h1>
        <p class="mt-1 text-sm text-gray-600">Update template information</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
        <div v-if="template.campaigns_count > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-3">
          <p class="text-sm text-blue-800">This template is used in <strong>{{ template.campaigns_count }}</strong> campaign(s).</p>
        </div>

        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Template Name <span class="text-red-500">*</span>
              </label>
              <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.name ? 'border-red-500' : 'border-gray-300'"/>
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
              <input v-model="form.slug" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Auto-generated if empty"/>
              <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
              <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Email Subject <span class="text-red-500">*</span>
            </label>
            <input v-model="form.subject" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.subject ? 'border-red-500' : 'border-gray-300'"/>
            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Plain Text Body <span class="text-red-500">*</span>
            </label>
            <textarea v-model="form.body" rows="6" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono text-sm" :class="form.errors.body ? 'border-red-500' : 'border-gray-300'"/>
            <p v-if="form.errors.body" class="mt-1 text-sm text-red-600">{{ form.errors.body }}</p>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">HTML Body (Optional)</label>
            <textarea v-model="form.html_body" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"/>
            <p class="mt-1 text-xs text-gray-500">HTML version of the email</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Category <span class="text-red-500">*</span>
              </label>
              <select v-model="form.category" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.category ? 'border-red-500' : 'border-gray-300'">
                <option value="">Select Category</option>
                <option value="general">General</option>
                <option value="transactional">Transactional</option>
                <option value="marketing">Marketing</option>
                <option value="notification">Notification</option>
              </select>
              <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Variables (Optional)</label>
              <input v-model="form.variables" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder='["name", "email"]'/>
              <p class="mt-1 text-xs text-gray-500">JSON array of available variables</p>
              <p v-if="form.errors.variables" class="mt-1 text-sm text-red-600">{{ form.errors.variables }}</p>
            </div>
          </div>

          <div class="mb-6">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"/>
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>

          <div class="flex justify-end gap-3">
            <Link :href="route('admin.data.email-templates.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
              {{ form.processing ? 'Updating...' : 'Update Template' }}
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
  template: Object,
});

const form = useForm({
  name: props.template.name,
  slug: props.template.slug,
  subject: props.template.subject,
  body: props.template.body,
  html_body: props.template.html_body || '',
  category: props.template.category,
  variables: props.template.variables ? JSON.stringify(props.template.variables) : '',
  is_active: props.template.is_active,
});

const submit = () => {
  form.put(route('admin.data.email-templates.update', props.template.id));
};
</script>
