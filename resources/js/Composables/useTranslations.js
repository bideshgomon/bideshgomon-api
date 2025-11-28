import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Composable for accessing translations in Vue components
 */
export function useTranslations() {
    const page = usePage();
    
    const locale = computed(() => page.props.locale || 'en');
    const translations = computed(() => page.props.translations || {});
    
    /**
     * Get translation by key
     * @param {string} key - Translation key (e.g., 'ui.dashboard', 'auth.failed')
     * @param {object} replace - Object of replacements for placeholders
     * @returns {string} Translated string
     */
    const trans = (key, replace = {}) => {
        const parts = key.split('.');
        let value = translations.value;
        
        for (const part of parts) {
            if (value && typeof value === 'object' && part in value) {
                value = value[part];
            } else {
                return key; // Return key if translation not found
            }
        }
        
        if (typeof value === 'string') {
            // Replace placeholders
            Object.keys(replace).forEach(placeholder => {
                value = value.replace(`:${placeholder}`, replace[placeholder]);
            });
            return value;
        }
        
        return key;
    };
    
    /**
     * Get translation with choice (pluralization)
     * @param {string} key - Translation key
     * @param {number} count - Number for pluralization
     * @param {object} replace - Replacements
     * @returns {string} Translated string
     */
    const transChoice = (key, count, replace = {}) => {
        const translation = trans(key);
        
        if (typeof translation === 'object' && 'numeric' in translation) {
            return translation.numeric.replace(':count', count);
        }
        
        return translation.replace(':count', count);
    };
    
    /**
     * Check if translation exists
     * @param {string} key - Translation key
     * @returns {boolean}
     */
    const hasTranslation = (key) => {
        const parts = key.split('.');
        let value = translations.value;
        
        for (const part of parts) {
            if (value && typeof value === 'object' && part in value) {
                value = value[part];
            } else {
                return false;
            }
        }
        
        return value !== undefined;
    };
    
    return {
        locale,
        trans,
        transChoice,
        hasTranslation,
        t: trans, // Alias for convenience
    };
}

/**
 * Global translation helper (can be used in templates)
 */
export function setupTranslations(app) {
    app.config.globalProperties.$t = (key, replace = {}) => {
        const { trans } = useTranslations();
        return trans(key, replace);
    };
}
