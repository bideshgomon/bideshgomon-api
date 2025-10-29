<script setup>
defineProps({
    useWhiteLogo: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <img
        v-if="useWhiteLogo"
        src="/images/bideshgomonlogowhite.png"
        alt="Bidesh Gomon"
    />
    <img
        v-else
        src="/images/bideshgomonlogo.png"
        alt="Bidesh Gomon"
    />
</template>