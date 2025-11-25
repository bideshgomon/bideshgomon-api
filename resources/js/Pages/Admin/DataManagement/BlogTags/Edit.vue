<template>
  <Head title="Edit Blog Tag" />
  <AdminLayout>
    <div class="p-6">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Blog Tag</h1>
        <p class="mt-1 text-sm text-gray-600">Update tag information</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <div v-if="blogTag.posts_count > 0" class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-3">
          <p class="text-sm text-blue-800">This tag is used in <strong>{{ blogTag.posts_count }}</strong> blog post(s).</p>
        </div>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Tag Name <span class="text-red-500">*</span>
            </label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" :class="form.errors.name ? 'border-red-500' : 'border-gray-300'"/>
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input v-model="form.slug" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"/>
            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
            <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
          </div>

          <div class="flex justify-end gap-3">
            <Link :href="route('admin.data.blog-tags.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
              {{ form.processing ? 'Updating...' : 'Update Tag' }}
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
  blogTag: Object,
});

const form = useForm({
  name: props.blogTag.name,
  slug: props.blogTag.slug,
});

const submit = () => {
  form.put(route('admin.data.blog-tags.update', props.blogTag.id));
};
</script>
