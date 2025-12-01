<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import BaseButton from '@/Components/Base/BaseButton.vue'
import { ExclamationTriangleIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const page = usePage()

const isImpersonating = computed(() => {
    return page.props.impersonating || false
})

const impersonatedUser = computed(() => {
    return page.props.auth?.user
})
</script>

<template>
    <div v-if="isImpersonating" class="bg-yellow-500 border-b-2 border-yellow-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <ExclamationTriangleIcon class="h-6 w-6 text-white" />
                    <div class="text-white">
                        <p class="font-semibold">Impersonation Mode Active</p>
                        <p class="text-sm text-yellow-100">
                            You are viewing the platform as 
                            <span class="font-medium">{{ impersonatedUser?.name }}</span>
                            ({{ impersonatedUser?.email }})
                        </p>
                    </div>
                </div>
                <Link :href="route('admin.impersonate.leave')" method="post" as="button">
                    <BaseButton variant="outline" size="sm" class="bg-white text-yellow-700 hover:bg-yellow-50 border-yellow-300">
                        <ArrowRightOnRectangleIcon class="h-5 w-5 mr-2" />
                        Stop Impersonating
                    </BaseButton>
                </Link>
            </div>
        </div>
    </div>
</template>
