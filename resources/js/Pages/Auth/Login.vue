<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'; // Added logo
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="text-center mb-4">
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 inline-block text-gray-500" />
            </Link>
        </div>

        <div class="card w-full sm:max-w-md">

            <div v-if="status" class="alert alert-success mb-4">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="block mt-4">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm hover:text-gray-900 rounded-md"
                        style="color: var(--brand-primary); text-decoration: none;"
                    >
                        Forgot your password?
                    </Link>

                    <button
                        type="submit"
                        class="btn btn-primary ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </button>
                </div>

                 <div class="mt-4 text-center">
                    <Link 
                        :href="route('register')" 
                        class="text-sm"
                        style="color: var(--brand-dark); text-decoration: none;"
                    >
                        Don't have an account? 
                        <span style="color: var(--brand-primary); font-weight: 600;">Register</span>
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>