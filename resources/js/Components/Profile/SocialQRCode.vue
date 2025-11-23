<script setup>
import { ref, watch, onMounted } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
    value: {
        type: String,
        required: true
    },
    platform: {
        type: String,
        required: true
    },
    size: {
        type: Number,
        default: 200
    }
});

const qrCanvas = ref(null);

const generateQRCode = async () => {
    if (!qrCanvas.value || !props.value) return;
    
    try {
        await QRCode.toCanvas(qrCanvas.value, props.value, {
            width: props.size,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        });
    } catch (error) {
        console.error('QR Code generation error:', error);
    }
};

const downloadQR = () => {
    if (!qrCanvas.value) return;
    
    const link = document.createElement('a');
    link.download = `${props.platform}-qr-code.png`;
    link.href = qrCanvas.value.toDataURL();
    link.click();
};

watch(() => props.value, generateQRCode);
onMounted(generateQRCode);
</script>

<template>
    <div class="flex flex-col items-center space-y-3">
        <canvas ref="qrCanvas" class="rounded-lg border-2 border-gray-200"></canvas>
        <button
            @click="downloadQR"
            type="button"
            class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download QR Code
        </button>
    </div>
</template>
