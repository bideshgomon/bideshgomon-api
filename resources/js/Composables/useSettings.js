/**
 * useSettings Composable
 * Global Standard: Safe access to site settings from any Vue component
 * 
 * Usage:
 * import { useSettings } from '@/Composables/useSettings'
 * const { getSetting, moduleEnabled, featureEnabled, settings } = useSettings()
 * 
 * Then in template:
 * {{ getSetting('site_name', 'Bidesh Gomon') }}
 * {{ settings.site_name }}
 */

import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useSettings() {
    const page = usePage()
    
    // Get all public settings from page props
    const settings = computed(() => {
        return page.props.settings || {}
    })

    /**
     * Get a setting value with fallback
     * @param {string} key - Setting key
     * @param {*} defaultValue - Fallback value
     * @returns {*}
     */
    const getSetting = (key, defaultValue = null) => {
        return settings.value[key] ?? defaultValue
    }

    /**
     * Check if a module is enabled
     * @param {string} module - Module name (jobs, blogs, directory, university, packages)
     * @returns {boolean}
     */
    const moduleEnabled = (module) => {
        return getSetting(`enable_${module}`, true) === true || getSetting(`enable_${module}`, true) === '1'
    }

    /**
     * Check if a feature flag is enabled
     * @param {string} feature - Feature name
     * @returns {boolean}
     */
    const featureEnabled = (feature) => {
        const value = getSetting(feature, true)
        return value === true || value === '1' || value === 1
    }

    /**
     * Check if maintenance mode is active
     * @returns {boolean}
     */
    const isMaintenanceMode = computed(() => {
        return featureEnabled('maintenance_mode')
    })

    /**
     * Get site branding
     * @returns {object}
     */
    const branding = computed(() => ({
        name: getSetting('site_name', 'Bidesh Gomon'),
        logo: getSetting('site_logo', '/images/logo.png'),
        favicon: getSetting('site_favicon', '/images/favicon.ico'),
        description: getSetting('site_description', 'Your Gateway to Global Opportunities'),
    }))

    /**
     * Get contact information
     * @returns {object}
     */
    const contact = computed(() => ({
        email: getSetting('contact_email', 'support@bideshgomon.com'),
        phone: getSetting('contact_phone', '+880 1234567890'),
        address: getSetting('contact_address', ''),
        supportHours: getSetting('support_hours', '9:00 AM - 6:00 PM (GMT+6)'),
    }))

    /**
     * Get social media links
     * @returns {object}
     */
    const social = computed(() => ({
        facebook: getSetting('facebook_url', ''),
        twitter: getSetting('twitter_url', ''),
        linkedin: getSetting('linkedin_url', ''),
        instagram: getSetting('instagram_url', ''),
        youtube: getSetting('youtube_url', ''),
        tiktok: getSetting('tiktok_url', ''),
    }))

    /**
     * Get homepage widget configuration
     * @returns {object}
     */
    const homepageWidgets = computed(() => ({
        showHeroSearch: featureEnabled('show_hero_search'),
        showFeaturedJobs: featureEnabled('show_featured_jobs'),
        featuredJobsCount: getSetting('featured_jobs_count', 6),
        showTopUniversities: featureEnabled('show_top_universities'),
        topUniversitiesCount: getSetting('top_universities_count', 8),
        showLatestBlogs: featureEnabled('show_latest_blogs'),
        latestBlogsCount: getSetting('latest_blogs_count', 3),
        showVisaPackages: featureEnabled('show_visa_packages'),
        visaPackagesCount: getSetting('visa_packages_count', 4),
    }))

    return {
        settings,
        getSetting,
        moduleEnabled,
        featureEnabled,
        isMaintenanceMode,
        branding,
        contact,
        social,
        homepageWidgets,
    }
}
