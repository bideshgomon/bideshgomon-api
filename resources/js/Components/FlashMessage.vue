<script setup>
import { computed, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon }from '@heroicons/vue/24/solid';

const page = usePage();
const show = ref(false);
const message = ref('');
const messageType = ref('success'); // 'success' or 'error'

const flash = computed(() => page.props.flash);

watch(flash, (newFlash) => {
    if (newFlash) {
        if (newFlash.success) {
            message.value = newFlash.success;
            messageType.value = 'success';
            show.value = true;
        } else if (newFlash.error) {
            message.value = newFlash.error;
            messageType.value = 'error';
            show.value = true;
        }

        // Auto-hide after 5 seconds
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
}, { deep: true });

</script>

<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <div
            v-if="show"
            class="fixed top-5 right-5 z-50 max-w-sm w-full"
        >
            <div 
                :class="{
                    'bg-green-50 border-green-400': messageType === 'success',
                    'bg-red-50 border-red-400': messageType === 'error'
                }"
                class="rounded-md border p-4 shadow-lg"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon 
                            v-if="messageType === 'success'" 
                            class="h-5 w-5 text-green-400" 
                            aria-hidden="true" 
                        />
                        <XCircleIcon 
                            v-if="messageType === 'error'" 
                            class="h-5 w-5 text-red-400" 
                            aria-hidden="true" 
                        />
                    </div>
                    <div class="ml-3">
                        <p 
                            :class="{
                                'text-green-800': messageType === 'success',
                                'text-red-800': messageType === 'error'
                            }"
                            class="text-sm font-medium"
                        >
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                @click="show = false"
                                type="button"
                                :class="{
                                    'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600': messageType === 'success',
                                    'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600': messageType === 'error'
                                }"
                                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50"
                            >
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>