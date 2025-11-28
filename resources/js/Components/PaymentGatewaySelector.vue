<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- SSLCommerz Option -->
            <div
                @click="selectGateway('sslcommerz')"
                :class="[
                    'relative cursor-pointer rounded-lg border-2 p-6 transition-all',
                    selectedGateway === 'sslcommerz'
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700'
                ]"
            >
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                            <CreditCardIcon class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">SSLCommerz</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Cards & Banking</p>
                        </div>
                    </div>
                    <input
                        type="radio"
                        name="gateway"
                        :checked="selectedGateway === 'sslcommerz'"
                        class="text-blue-600 focus:ring-blue-500"
                    />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    Pay with Credit/Debit cards, Mobile Banking, or Internet Banking
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Fee: 1.5% + ৳{{ calculateFee('sslcommerz').toFixed(2) }}
                </p>
            </div>

            <!-- bKash Option -->
            <div
                @click="selectGateway('bkash')"
                :class="[
                    'relative cursor-pointer rounded-lg border-2 p-6 transition-all',
                    selectedGateway === 'bkash'
                        ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-pink-300 dark:hover:border-pink-700'
                ]"
            >
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                            <DevicePhoneMobileIcon class="w-6 h-6 text-pink-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">bKash</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mobile Wallet</p>
                        </div>
                    </div>
                    <input
                        type="radio"
                        name="gateway"
                        :checked="selectedGateway === 'bkash'"
                        class="text-pink-600 focus:ring-pink-500"
                    />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    Pay instantly with your bKash mobile wallet
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Fee: 1.8% + ৳{{ calculateFee('bkash').toFixed(2) }}
                </p>
            </div>

            <!-- Nagad Option -->
            <div
                @click="selectGateway('nagad')"
                :class="[
                    'relative cursor-pointer rounded-lg border-2 p-6 transition-all',
                    selectedGateway === 'nagad'
                        ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-700'
                ]"
            >
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                            <DevicePhoneMobileIcon class="w-6 h-6 text-orange-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Nagad</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mobile Wallet</p>
                        </div>
                    </div>
                    <input
                        type="radio"
                        name="gateway"
                        :checked="selectedGateway === 'nagad'"
                        class="text-orange-600 focus:ring-orange-500"
                    />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    Pay securely with your Nagad mobile wallet
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Fee: 1.6% + ৳{{ calculateFee('nagad').toFixed(2) }}
                </p>
            </div>
        </div>

        <!-- Payment Summary -->
        <div v-if="selectedGateway && amount > 0" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 space-y-3">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h4>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Amount</span>
                <span class="font-medium text-gray-900 dark:text-white">৳{{ amount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Gateway Fee</span>
                <span class="font-medium text-gray-900 dark:text-white">৳{{ calculateFee(selectedGateway).toFixed(2) }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                <span class="font-bold text-lg text-gray-900 dark:text-white">৳{{ totalAmount.toFixed(2) }}</span>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start gap-2">
            <input
                type="checkbox"
                id="terms"
                v-model="agreeToTerms"
                class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
            />
            <label for="terms" class="text-sm text-gray-600 dark:text-gray-400">
                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> and understand that payment gateway fees are non-refundable.
            </label>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { CreditCardIcon, DevicePhoneMobileIcon } from '@heroicons/vue/24/outline';

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
import { watch } from 'vue';
watch(agreeToTerms, (value) => {
    emit('update:agreeToTerms', value);
});
</script>
