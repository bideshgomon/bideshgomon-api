<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SocialQRCode from '@/Components/Profile/SocialQRCode.vue';

const props = defineProps({
    socialLinks: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['saved']);

const showQRModal = ref(false);
const qrPlatform = ref('');
const qrValue = ref('');

const socialPlatforms = [
    { 
        key: 'linkedin', 
        label: 'LinkedIn', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        placeholder: 'https://linkedin.com/in/username',
        color: 'bg-blue-600'
    },
    { 
        key: 'github', 
        label: 'GitHub', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>',
        placeholder: 'https://github.com/username',
        color: 'bg-gray-900'
    },
    { 
        key: 'facebook', 
        label: 'Facebook', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        placeholder: 'https://facebook.com/username',
        color: 'bg-blue-500'
    },
    { 
        key: 'twitter', 
        label: 'Twitter/X', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        placeholder: 'https://twitter.com/username',
        color: 'bg-gray-800'
    },
    { 
        key: 'instagram', 
        label: 'Instagram', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        placeholder: 'https://instagram.com/username',
        color: 'bg-pink-500'
    },
    { 
        key: 'youtube', 
        label: 'YouTube', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        placeholder: 'https://youtube.com/@username',
        color: 'bg-red-600'
    },
    { 
        key: 'tiktok', 
        label: 'TikTok', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
        placeholder: 'https://tiktok.com/@username',
        color: 'bg-black'
    },
    { 
        key: 'whatsapp', 
        label: 'WhatsApp', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
        placeholder: '+8801XXXXXXXXX',
        color: 'bg-green-500'
    },
    { 
        key: 'telegram', 
        label: 'Telegram', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        placeholder: '@username',
        color: 'bg-blue-400'
    },
    { 
        key: 'wechat', 
        label: 'WeChat', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.16 10.16 0 0 0 2.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178A1.17 1.17 0 0 1 4.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178 1.17 1.17 0 0 1-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.722.722 0 0 1 .598.082l1.584.926a.272.272 0 0 0 .14.047c.134 0 .24-.111.24-.247 0-.06-.023-.12-.038-.177l-.327-1.233a.582.582 0 0 1-.023-.156.49.49 0 0 1 .201-.398C23.024 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-6.656-6.088V8.89c-.135-.01-.27-.027-.407-.03zm-2.53 3.274c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.969-.982zm4.844 0c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.969-.982z"/></svg>',
        placeholder: 'WeChat ID',
        color: 'bg-green-600'
    },
    { 
        key: 'skype', 
        label: 'Skype', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.069 18.874c-4.023 0-5.82-1.979-5.82-3.464 0-.765.561-1.296 1.333-1.296 1.723 0 1.273 2.477 4.487 2.477 1.641 0 2.55-.895 2.55-1.811 0-.551-.269-1.16-1.354-1.427l-3.576-.895c-2.88-.724-3.403-2.286-3.403-3.751 0-3.047 2.861-4.191 5.549-4.191 2.471 0 5.393 1.373 5.393 3.199 0 .784-.688 1.24-1.453 1.24-1.469 0-1.198-2.037-4.164-2.037-1.469 0-2.292.664-2.292 1.617s1.153 1.258 2.157 1.487l2.637.587c2.891.649 3.624 2.346 3.624 3.944 0 2.476-1.902 4.324-5.722 4.324m11.084-4.882l-.029.135-.044-.24c.015.045.044.074.059.12.12-.675.181-1.363.181-2.052 0-1.529-.301-3.012-.898-4.42-.569-1.348-1.395-2.562-2.427-3.596-1.049-1.033-2.247-1.856-3.595-2.426-1.318-.631-2.801-.931-4.328-.931-.72 0-1.444.07-2.143.204l.119.06-.239-.033.119-.025C8.91.274 7.829 0 6.731 0c-1.789 0-3.47.698-4.736 1.967C.729 3.235.032 4.923.032 6.716c0 1.143.292 2.265.844 3.258l.02-.124.041.239-.06-.115c-.114.645-.172 1.299-.172 1.955 0 1.53.3 3.017.884 4.416.568 1.362 1.378 2.576 2.427 3.609 1.034 1.05 2.247 1.857 3.595 2.442 1.394.6 2.877.898 4.404.898.659 0 1.334-.06 1.977-.179l-.119-.062.24.046-.135.03c1.002.569 2.126.871 3.294.871 1.783 0 3.459-.69 4.733-1.963 1.259-1.259 1.962-2.951 1.962-4.749 0-1.138-.299-2.262-.853-3.266"/></svg>',
        placeholder: 'live:username',
        color: 'bg-blue-400'
    },
    { 
        key: 'discord', 
        label: 'Discord', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
        placeholder: 'username#1234',
        color: 'bg-indigo-600'
    },
    { 
        key: 'medium', 
        label: 'Medium', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.54 12a6.8 6.8 0 01-6.77 6.82A6.8 6.8 0 010 12a6.8 6.8 0 016.77-6.82A6.8 6.8 0 0113.54 12zM20.96 12c0 3.54-1.51 6.42-3.38 6.42-1.87 0-3.39-2.88-3.39-6.42s1.52-6.42 3.39-6.42 3.38 2.88 3.38 6.42M24 12c0 3.17-.53 5.75-1.19 5.75-.66 0-1.19-2.58-1.19-5.75s.53-5.75 1.19-5.75C23.47 6.25 24 8.83 24 12z"/></svg>',
        placeholder: 'https://medium.com/@username',
        color: 'bg-gray-700'
    },
    { 
        key: 'behance', 
        label: 'Behance', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M0 7.5v9c0 .401.325.75.75.75h6.75c3.176 0 5.25-1.555 5.25-4.146 0-1.545-.934-2.715-2.496-3.216C11.565 9.38 12 8.433 12 7.259c0-2.149-1.688-3.509-4.5-3.509H.75c-.425 0-.75.35-.75.75zm3 1.5h3.75c1.313 0 2.25.653 2.25 1.714 0 1.06-.937 1.786-2.25 1.786H3V9zm0 5.25h4.5c1.491 0 2.344.75 2.344 2.063 0 1.312-.853 2.187-2.344 2.187H3v-4.25zM15 3.75h9v1.5h-9v-1.5zm-1.5 6.938c0-3.573 2.438-6.188 6.188-6.188 3.75 0 6.063 2.663 6.063 6.188 0 .337-.024.675-.075 1.012H16.95c.15 2.137 1.35 3.3 3.15 3.3 1.35 0 2.325-.525 3.075-1.65l2.55 1.35c-1.2 1.95-3.225 2.925-5.625 2.925-3.9 0-6.6-2.7-6.6-6.937zm3.225-.562h7.05c-.225-1.838-1.425-2.888-3.375-2.888-1.875 0-3.225 1.05-3.675 2.888z"/></svg>',
        placeholder: 'https://behance.net/username',
        color: 'bg-blue-600'
    },
    { 
        key: 'dribbble', 
        label: 'Dribbble', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.375 0 0 5.375 0 12s5.375 12 12 12 12-5.375 12-12S18.625 0 12 0zm8.24 5.568c1.504 1.832 2.432 4.16 2.528 6.688-1.216-.224-2.688-.448-4.224-.448-1.024 0-2.048.064-3.008.192-.192-.448-.384-.896-.576-1.344 2.592-1.088 4.656-2.656 5.28-5.088zM12 2.272c2.24 0 4.288.832 5.888 2.176-.544 2.24-2.368 3.648-4.864 4.672-1.344-2.464-2.816-4.544-4.032-6.144A9.68 9.68 0 0112 2.272zM6.784 3.968c1.152 1.6 2.624 3.68 3.968 6.08-3.136.896-6.656 1.28-8.96 1.344.608-3.36 2.88-6.144 5.92-7.424zm-4.512 9.76v-.448c2.432-.064 6.272-.448 9.664-1.536.192.384.384.768.576 1.152-4.096 1.28-7.488 4.352-9.28 7.296A9.631 9.631 0 012.272 12zm11.2 9.728c-2.048 0-3.968-.64-5.568-1.728 1.6-2.688 4.672-5.504 8.576-6.656 1.024 2.688 1.728 5.504 2.048 7.616a9.758 9.758 0 01-5.056 1.344z"/></svg>',
        placeholder: 'https://dribbble.com/username',
        color: 'bg-pink-600'
    },
    { 
        key: 'website', 
        label: 'Personal Website', 
        icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm9.5 12c0 1.194-.232 2.333-.654 3.38l-4.41-1.357A7.471 7.471 0 0 0 16.5 12c0-1.007-.198-1.968-.558-2.847l4.404-1.355c.425 1.068.654 2.226.654 3.427v.775zm-9.5 9.5c-1.194 0-2.333-.232-3.38-.654l1.357-4.41A7.471 7.471 0 0 0 12 16.5c1.007 0 1.968-.198 2.847-.558l1.355 4.404A9.456 9.456 0 0 1 12 21.5zm-7.119-4.38A9.456 9.456 0 0 1 2.5 12c0-1.194.232-2.333.654-3.38l4.41 1.357A7.471 7.471 0 0 0 7.5 12c0 1.007.198 1.968.558 2.847l-4.404 1.355zm8.966-14.62c1.068.425 2.226.654 3.427.654h.775A9.474 9.474 0 0 1 12 2.5c-1.194 0-2.333.232-3.38.654l1.357 4.41A7.471 7.471 0 0 0 12 7.5c1.007 0 1.968.198 2.847.558l-1.355-4.404zM12 9c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z"/></svg>',
        placeholder: 'https://yourwebsite.com',
        color: 'bg-gray-600'
    },
];

const form = useForm({
    social_links: { ...props.socialLinks }
});

const submit = () => {
    form.post(route('profile.social-links.update'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved');
        },
    });
};

const hasAnyLinks = computed(() => {
    return Object.values(form.social_links || {}).some(link => link && link.trim() !== '');
});

const openQRModal = (platform, value) => {
    qrPlatform.value = platform;
    
    // Format the value for QR code
    if (platform === 'whatsapp') {
        // Remove any non-numeric characters and ensure it starts with country code
        const cleanNumber = value.replace(/\D/g, '');
        qrValue.value = `https://wa.me/${cleanNumber}`;
    } else if (platform === 'telegram') {
        // Remove @ if present and create telegram link
        const username = value.replace('@', '');
        qrValue.value = `https://t.me/${username}`;
    }
    
    showQRModal.value = true;
};

const closeQRModal = () => {
    showQRModal.value = false;
    qrPlatform.value = '';
    qrValue.value = '';
};

const canGenerateQR = (platform) => {
    return ['whatsapp', 'telegram'].includes(platform) && form.social_links[platform];
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Social Media & Contact</h3>
            <p class="mt-1 text-sm text-gray-600">
                Add your social media profiles and additional contact methods. These help verify your identity and make it easier for agencies to reach you.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div v-for="platform in socialPlatforms" :key="platform.key" class="space-y-2">
                    <InputLabel :for="`social-${platform.key}`" class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span :class="[platform.color, 'inline-flex h-6 w-6 items-center justify-center rounded text-white']" v-html="platform.icon">
                            </span>
                            {{ platform.label }}
                        </span>
                        <button
                            v-if="canGenerateQR(platform.key)"
                            @click="openQRModal(platform.key, form.social_links[platform.key])"
                            type="button"
                            class="inline-flex items-center gap-1 rounded-md bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-200"
                            title="Generate QR Code"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            QR
                        </button>
                    </InputLabel>
                    <TextInput
                        :id="`social-${platform.key}`"
                        v-model="form.social_links[platform.key]"
                        type="text"
                        class="mt-1 block w-full"
                        :placeholder="platform.placeholder"
                        autocomplete="off"
                    />
                </div>
            </div>

            <!-- Preview Section -->
            <div v-if="hasAnyLinks" class="rounded-lg bg-gray-50 p-4">
                <h4 class="mb-3 text-sm font-medium text-gray-700">Preview</h4>
                <div class="flex flex-wrap gap-2">
                    <a
                        v-for="platform in socialPlatforms"
                        :key="platform.key"
                        v-show="form.social_links[platform.key]"
                        :href="form.social_links[platform.key]"
                        target="_blank"
                        rel="noopener noreferrer"
                        :class="[platform.color, 'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium text-white hover:opacity-90']"
                    >
                        <span class="w-4 h-4" v-html="platform.icon"></span>
                        <span>{{ platform.label }}</span>
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h4 class="mb-2 flex items-center gap-2 text-sm font-medium text-blue-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tips
                </h4>
                <ul class="space-y-1 text-sm text-blue-800">
                    <li>• <strong>LinkedIn:</strong> Helps verify your professional background</li>
                    <li>• <strong>WhatsApp/Telegram:</strong> Faster communication with agencies (click QR button to generate scannable code)</li>
                    <li>• <strong>Facebook:</strong> Common verification method in Bangladesh</li>
                    <li>• All links are optional - add only what you're comfortable sharing</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-4">
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                    ✓ Social links saved successfully!
                </p>
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Social Links' }}
                </PrimaryButton>
            </div>
        </form>

        <!-- QR Code Modal -->
        <Modal :show="showQRModal" @close="closeQRModal">
            <div class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ qrPlatform === 'whatsapp' ? '📱 WhatsApp' : '✈️ Telegram' }} QR Code
                    </h3>
                    <button
                        @click="closeQRModal"
                        type="button"
                        class="rounded-md text-gray-400 hover:text-gray-500"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <p class="mb-6 text-sm text-gray-600">
                    Share this QR code with agencies or save it to your device. They can scan it to quickly contact you via {{ qrPlatform === 'whatsapp' ? 'WhatsApp' : 'Telegram' }}.
                </p>

                <div class="flex justify-center">
                    <SocialQRCode
                        v-if="qrValue"
                        :value="qrValue"
                        :platform="qrPlatform"
                        :size="250"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeQRModal">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
