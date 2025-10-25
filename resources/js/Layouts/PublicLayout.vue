<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicFooter from '@/Components/PublicFooter.vue'; // <-- Import the new component

// Assuming logos are in /public/images/ (adjust if needed)
const logoUrl = '/images/bideshgomonlogo.png';

// Access page props for authentication status
const page = usePage();
const user = computed(() => page.props.auth.user);

</script>

<template>
    <div class="public-layout">
        <nav class="navbar">
            <Link :href="route('welcome')">
                <img
                    :src="logoUrl"
                    alt="Bidesh Gomon"
                    class="navbar-logo-img"
                    style="max-height: 35px; width: auto; display: block;"
                />
            </Link>
            <div class="nav-links">
                <Link :href="route('public.universities')">Universities</Link>
                <Link :href="route('public.courses')">Courses</Link>
                <Link :href="route('public.jobs')">Jobs</Link>
                </div>
            <div class="nav-auth">
                <Link v-if="!user" :href="route('login')" class="btn btn-outline" style="margin-right: 0.5rem;">Login</Link>
                <Link v-if="!user" :href="route('register')" class="btn btn-primary">Register</Link>
                <Link v-else :href="route('dashboard')" class="btn btn-secondary">Dashboard</Link>
            </div>
        </nav>

        <main>
            <slot />
        </main>

        <PublicFooter /> {/* <-- Use the imported component */}

    </div>
</template>

<style scoped>
/* Scoped styles remain the same */
.public-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: var(--brand-light);
}
main { flex-grow: 1; }
.nav-links a { font-weight: 600; }
.nav-auth { display: flex; align-items: center; }
</style>