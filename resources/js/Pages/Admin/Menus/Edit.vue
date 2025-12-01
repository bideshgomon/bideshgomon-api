<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    menu: Object,
    locations: Object,
    parentMenus: Array,
    availableRoutes: Array,
});

const form = useForm({
    location: props.menu.location,
    label: props.menu.label,
    url: props.menu.url || '',
    route_name: props.menu.route_name || '',
    icon: props.menu.icon || '',
    parent_id: props.menu.parent_id,
    order: props.menu.order,
    is_active: props.menu.is_active,
    is_external: props.menu.is_external,
    target: props.menu.target || '_self',
});

const submit = () => {
    form.put(route('menus.update', props.menu.id));
};
</script>

<template>
    <Head :title="`Edit Menu: ${menu.label}`" />

    <AdminLayout>
        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <Link
                        :href="route('menus.index')"
                        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <ArrowLeftIcon class="h-4 w-4 mr-2" />
                        Back to Menus
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Menu Item</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Editing: <strong>{{ menu.label }}</strong>
                    </p>
                </div>

                <!-- Form (Same as Create) -->
                <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Menu Location *
                        </label>
                        <select
                            v-model="form.location"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option v-for="(label, value) in locations" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>

                    <!-- Label -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Label *
                        </label>
                        <input
                            v-model="form.label"
                            type="text"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Home, Services, Contact, etc."
                            required
                        />
                        <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</p>
                    </div>

                    <!-- Link Type -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Route Name
                            </label>
                            <select
                                v-model="form.route_name"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Select a route...</option>
                                <option v-for="route in availableRoutes" :key="route" :value="route">
                                    {{ route }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Recommended: Use Laravel route name</p>
                            <p v-if="form.errors.route_name" class="mt-1 text-sm text-red-600">{{ form.errors.route_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Custom URL
                            </label>
                            <input
                                v-model="form.url"
                                type="text"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="/custom-page or https://example.com"
                            />
                            <p class="mt-1 text-xs text-gray-500">Only if route is not available</p>
                            <p v-if="form.errors.url" class="mt-1 text-sm text-red-600">{{ form.errors.url }}</p>
                        </div>
                    </div>

                    <!-- Icon -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Icon (Heroicon name)
                        </label>
                        <input
                            v-model="form.icon"
                            type="text"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="HomeIcon, BriefcaseIcon, etc."
                        />
                        <p class="mt-1 text-xs text-gray-500">Optional: Heroicons v2 outline icon name</p>
                        <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
                    </div>

                    <!-- Parent Menu & Order -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Parent Menu
                            </label>
                            <select
                                v-model="form.parent_id"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option :value="null">None (Top Level)</option>
                                <option v-for="parent in parentMenus" :key="parent.id" :value="parent.id">
                                    {{ parent.label }} ({{ parent.location }})
                                </option>
                            </select>
                            <p v-if="form.errors.parent_id" class="mt-1 text-sm text-red-600">{{ form.errors.parent_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Order
                            </label>
                            <input
                                v-model.number="form.order"
                                type="number"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                min="0"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            <p v-if="form.errors.order" class="mt-1 text-sm text-red-600">{{ form.errors.order }}</p>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Settings</h3>

                        <div class="flex items-center">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label class="ml-2 block text-sm text-gray-700">
                                Active (visible on website)
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="form.is_external"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label class="ml-2 block text-sm text-gray-700">
                                External Link
                            </label>
                        </div>

                        <div v-if="form.is_external">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Target
                            </label>
                            <select
                                v-model="form.target"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="_self">Same Window (_self)</option>
                                <option value="_blank">New Tab (_blank)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <Link
                            :href="route('menus.index')"
                            class="px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors"
                        >
                            <CheckIcon class="h-5 w-5 mr-2" />
                            {{ form.processing ? 'Saving...' : 'Update Menu Item' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

