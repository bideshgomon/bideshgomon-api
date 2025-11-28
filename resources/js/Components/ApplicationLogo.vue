<script setup>
defineProps({
  useWhiteLogo: {
    type: Boolean,
    default: false,
  },
  useTextLogo: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <!-- Text Logo Version - Clean & Simple -->
  <div v-if="useTextLogo" class="flex items-center">
    <span class="text-2xl font-bold flex items-center">
      <span :class="useWhiteLogo ? 'text-red-300' : 'text-brand-red'">
        Bidesh
      </span>
      <span :class="useWhiteLogo ? 'text-green-300' : 'text-brand-green'" class="ml-1">
        Gomon
      </span>
    </span>
  </div>

  <!-- Image Logo Version -->
  <img
    v-else-if="useWhiteLogo"
    src="/images/bideshgomonlogowhite.png"
    alt="Bidesh Gomon - Going Abroad"
    class="h-10 w-auto"
  />
  <img
    v-else
    src="/images/bideshgomonlogo.png"
    alt="Bidesh Gomon - Going Abroad"
    class="h-10 w-auto"
  />
</template>
