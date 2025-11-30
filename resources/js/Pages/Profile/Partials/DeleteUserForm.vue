<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shadow-rhythmic-md flex-shrink-0 border-2 border-red-200">
                <ExclamationTriangleIcon class="w-6 h-6 text-red-600 opacity-70" />
            </div>
            <div class="flex-1">
                <h2 class="font-display font-bold text-xl text-gray-800">
                    Delete Account
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Once your account is deleted, all of its resources and data will
                    be permanently deleted. Before deleting your account, please
                    download any data or information that you wish to retain.
                </p>
            </div>
        </header>

        <DangerButton @click="confirmUserDeletion" class="py-3 px-6 text-base touch-manipulation" style="min-height: 48px">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-4 md:p-6">
                <h2 class="text-base md:text-lg font-semibold text-gray-900">
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm md:text-base text-gray-600">
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full py-3 px-4 text-base rounded-lg touch-manipulation"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                    <SecondaryButton @click="closeModal" class="py-3 px-6 text-base touch-manipulation justify-center" style="min-height: 48px">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="py-3 px-6 text-base touch-manipulation justify-center"
                        style="min-height: 48px"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
