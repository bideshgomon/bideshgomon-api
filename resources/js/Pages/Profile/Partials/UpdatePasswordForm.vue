<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';
import FlowButton from '@/Components/Rhythmic/FlowButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import { KeyIcon } from '@heroicons/vue/24/solid';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const saveError = ref('');
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Password strength indicator
const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { level: 0, text: '', color: '' };
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    if (strength <= 2) return { level: 1, text: 'Weak', color: 'bg-red-500' };
    if (strength === 3) return { level: 2, text: 'Fair', color: 'bg-yellow-500' };
    if (strength === 4) return { level: 3, text: 'Good', color: 'bg-blue-500' };
    return { level: 4, text: 'Strong', color: 'bg-green-500' };
});

const updatePassword = () => {
    saveError.value = '';
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            saveError.value = 'Failed to update password. Please check your current password and try again.';
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold-500 to-heritage-500 flex items-center justify-center shadow-rhythmic-md">
                    <KeyIcon class="h-6 w-6 text-white" />
                </div>
                <div>
                    <h2 class="font-display font-bold text-xl text-gray-800">Update Password</h2>
                    <p class="text-xs text-gray-500">Keep your account secure</p>
                </div>
            </div>
        </header>

        <form @submit.prevent="updatePassword" class="mt-4 md:mt-6">
            <RhythmicCard variant="light" class="space-y-rhythm-md">
                <div>
                    <InputLabel for="current_password" value="Current Password" class="text-sm md:text-base" />
                    <div class="relative">
                        <TextInput
                            id="current_password"
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            :type="showCurrentPassword ? 'text' : 'password'"
                            class="mt-1 block w-full pr-12 py-3 px-4 text-base rounded-lg touch-manipulation"
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 touch-manipulation"
                            style="min-height: 48px"
                        >
                        <component :is="showCurrentPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                    </button>
                </div>
                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

                <div>
                    <InputLabel for="password" value="New Password" class="text-sm md:text-base" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="mt-1 block w-full pr-12 py-3 px-4 text-base rounded-lg touch-manipulation"
                            autocomplete="new-password"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 touch-manipulation"
                            style="min-height: 48px"
                        >
                        <component :is="showPassword ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div v-if="form.password" class="mt-2">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                class="h-full transition-all duration-300" 
                                :class="passwordStrength.color"
                                :style="{ width: (passwordStrength.level * 25) + '%' }"
                            ></div>
                        </div>
                        <span class="text-xs font-medium" :class="{
                            'text-red-600': passwordStrength.level === 1,
                            'text-yellow-600': passwordStrength.level === 2,
                            'text-blue-600': passwordStrength.level === 3,
                            'text-green-600': passwordStrength.level === 4
                        }">{{ passwordStrength.text }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Use 8+ characters with mix of letters, numbers & symbols</p>
                </div>

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                        class="text-sm md:text-base"
                    />
                    <div class="relative">
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            class="mt-1 block w-full pr-12 py-3 px-4 text-base rounded-lg touch-manipulation"
                            autocomplete="new-password"
                        />
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 touch-manipulation"
                            style="min-height: 48px"
                        >
                        <component :is="showPasswordConfirmation ? EyeSlashIcon : EyeIcon" class="w-5 h-5" />
                    </button>
                </div>
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

                <div class="space-y-4">
                    <!-- Error Message -->
                    <div v-if="saveError" class="rounded-lg bg-red-50 p-3 md:p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ saveError }}</h3>
                        </div>
                        <button @click="saveError = ''" class="ml-auto inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>

                </div>

            <!-- Desktop Save Button -->
            <div class="hidden md:flex items-center gap-4 mt-rhythm-lg">
                <FlowButton 
                    type="submit"
                    :disabled="form.processing"
                    :loading="form.processing"
                    variant="primary"
                >
                    Update Password
                </FlowButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">
                        ✓ Password updated successfully!
                    </p>
                </Transition>
            </div>
            </RhythmicCard>

            <!-- Mobile Sticky Save Button -->
            <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-2xl z-30 safe-area-bottom">
                <button
                    @click="updatePassword"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-200 active:scale-98 touch-manipulation flex items-center justify-center gap-2"
                    type="button"
                >
                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ form.processing ? 'Saving...' : (form.recentlySuccessful ? '✓ Saved!' : 'Update Password') }}</span>
                </button>
            </div>

            <!-- Mobile Bottom Spacer -->
            <div class="md:hidden h-24"></div>
        </form>
    </section>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
}

.active\:scale-98:active {
    transform: scale(0.98);
}
</style>
