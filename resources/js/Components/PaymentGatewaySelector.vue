<template>
    <div class="space-y-rhythm-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-rhythm-md">
            <!-- SSLCommerz Option -->
            <RhythmicCard
                @click="selectGateway('sslcommerz')"
                :variant="selectedGateway === 'sslcommerz' ? 'ocean' : 'light'"
                size="md"
                :hoverable="true"
                class="cursor-pointer transition-all duration-300 animate-fadeIn"
                :class="selectedGateway === 'sslcommerz' ? 'ring-2 ring-ocean-500' : ''"
            >
                <template #icon>
                    <CreditCardIcon class="w-6 h-6" />
                </template>
                <template #title>SSLCommerz</template>
                <template #description>Cards & Banking</template>
                <template #default>
                    <p class="text-sm text-gray-600 mb-rhythm-xs">
                        Credit/Debit cards, Mobile & Internet Banking
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Fee: 1.5% + ৳{{ calculateFee('sslcommerz').toFixed(2) }}
                        </p>
                        <input
                            type="radio"
                            name="gateway"
                            :checked="selectedGateway === 'sslcommerz'"
                            class="text-ocean-600 focus:ring-ocean-500"
                        />
                    </div>
                </template>
            </RhythmicCard>

            <!-- bKash Option -->
            <RhythmicCard
                @click="selectGateway('bkash')"
                :variant="selectedGateway === 'bkash' ? 'heritage' : 'light'"
                size="md"
                :hoverable="true"
                class="cursor-pointer transition-all duration-300 animate-fadeIn"
                :class="selectedGateway === 'bkash' ? 'ring-2 ring-heritage-500' : ''"
                style="animation-delay: 100ms;"
            >
                <template #icon>
                    <DevicePhoneMobileIcon class="w-6 h-6" />
                </template>
                <template #title>bKash</template>
                <template #description>Mobile Wallet</template>
                <template #default>
                    <p class="text-sm text-gray-600 mb-rhythm-xs">
                        Pay instantly with your bKash mobile wallet
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Fee: 1.8% + ৳{{ calculateFee('bkash').toFixed(2) }}
                        </p>
                        <input
                            type="radio"
                            name="gateway"
                            :checked="selectedGateway === 'bkash'"
                            class="text-heritage-600 focus:ring-heritage-500"
                        />
                    </div>
                </template>
            </RhythmicCard>

            <!-- Nagad Option -->
            <RhythmicCard
                @click="selectGateway('nagad')"
                :variant="selectedGateway === 'nagad' ? 'sunrise' : 'light'"
                size="md"
                :hoverable="true"
                class="cursor-pointer transition-all duration-300 animate-fadeIn"
                :class="selectedGateway === 'nagad' ? 'ring-2 ring-sunrise-500' : ''"
                style="animation-delay: 200ms;"
            >
                <template #icon>
                    <DevicePhoneMobileIcon class="w-6 h-6" />
                </template>
                <template #title>Nagad</template>
                <template #description>Mobile Wallet</template>
                <template #default>
                    <p class="text-sm text-gray-600 mb-rhythm-xs">
                        Pay securely with your Nagad mobile wallet
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            Fee: 1.6% + ৳{{ calculateFee('nagad').toFixed(2) }}
                        </p>
                        <input
                            type="radio"
                            name="gateway"
                            :checked="selectedGateway === 'nagad'"
                            class="text-sunrise-600 focus:ring-sunrise-500"
                        />
                    </div>
                </template>
            </RhythmicCard>
        </div>

        <!-- Payment Summary -->
        <RhythmicCard 
            v-if="selectedGateway && amount > 0" 
            variant="gradient" 
            size="md"
            class="animate-fadeIn"
        >
            <template #title>Payment Summary</template>
            <template #default>
                <div class="space-y-rhythm-sm">
                    <div class="flex justify-between text-sm">
                        <span class="text-white/80">Amount</span>
                        <span class="font-medium text-white">৳{{ amount.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-white/80">Gateway Fee</span>
                        <span class="font-medium text-white">৳{{ calculateFee(selectedGateway).toFixed(2) }}</span>
                    </div>
                    <div class="border-t border-white/20 pt-rhythm-sm flex justify-between">
                        <span class="font-semibold text-white">Total</span>
                        <span class="font-bold text-lg text-white">৳{{ totalAmount.toFixed(2) }}</span>
                    </div>
                </div>
            </template>
        </RhythmicCard>

        <!-- Terms and Conditions -->
        <div class="flex items-start gap-rhythm-xs">
            <input
                type="checkbox"
                id="terms"
                v-model="agreeToTerms"
                class="mt-1 rounded border-gray-300 dark:border-gray-600 text-ocean-600 focus:ring-ocean-500"
            />
            <label for="terms" class="text-sm text-gray-600 dark:text-gray-400">
                I agree to the <a href="#" class="text-ocean-600 hover:underline font-medium">Terms and Conditions</a> and understand that payment gateway fees are non-refundable.
            </label>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { CreditCardIcon, DevicePhoneMobileIcon } from '@heroicons/vue/24/outline';
import RhythmicCard from '@/Components/Rhythmic/RhythmicCard.vue';

const props = defineProps({
    amount: {
        type: Number,
        required: true,
        default: 0
    },
    modelValue: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'update:agreeToTerms']);

const selectedGateway = ref(props.modelValue);
const agreeToTerms = ref(false);

const gatewayFees = {
    sslcommerz: 0.015, // 1.5%
    bkash: 0.018,      // 1.8%
    nagad: 0.016       // 1.6%
};

const calculateFee = (gateway) => {
    if (!gateway || props.amount <= 0) return 0;
    return props.amount * gatewayFees[gateway];
};

const totalAmount = computed(() => {
    if (!selectedGateway.value || props.amount <= 0) return 0;
    return props.amount + calculateFee(selectedGateway.value);
});

const selectGateway = (gateway) => {
    selectedGateway.value = gateway;
    emit('update:modelValue', gateway);
};

// Watch for changes in agreeToTerms
watch(agreeToTerms, (value) => {
    emit('update:agreeToTerms', value);
});
</script>
