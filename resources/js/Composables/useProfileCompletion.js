import { computed } from 'vue'

export function useProfileCompletion(user, userProfile) {
    const calculateCompletion = computed(() => {
        let completed = 0
        let total = 0
        const sections = {}

        // Basic Information (20 points)
        sections.basic = { completed: 0, total: 3, items: [] }
        if (user.value?.name) {
            sections.basic.completed++
            sections.basic.items.push('Name')
        }
        if (user.value?.email) {
            sections.basic.completed++
            sections.basic.items.push('Email')
        }
        if (userProfile.value?.bio) {
            sections.basic.completed++
            sections.basic.items.push('Bio')
        }
        sections.basic.total = 3

        // Profile Details (25 points)
        sections.profile = { completed: 0, total: 5, items: [] }
        if (userProfile.value?.dob) {
            sections.profile.completed++
            sections.profile.items.push('Date of Birth')
        }
        if (userProfile.value?.gender) {
            sections.profile.completed++
            sections.profile.items.push('Gender')
        }
        if (userProfile.value?.nationality) {
            sections.profile.completed++
            sections.profile.items.push('Nationality')
        }
        if (userProfile.value?.nid) {
            sections.profile.completed++
            sections.profile.items.push('NID')
        }
        if (userProfile.value?.present_address_line && userProfile.value?.present_division) {
            sections.profile.completed++
            sections.profile.items.push('Address')
        }
        sections.profile.total = 5

        // Documents (15 points)
        sections.documents = { completed: 0, total: 3, items: [] }
        if (userProfile.value?.passport_number) {
            sections.documents.completed++
            sections.documents.items.push('Passport Number')
        }
        if (userProfile.value?.passport_issue_date) {
            sections.documents.completed++
            sections.documents.items.push('Passport Issue Date')
        }
        if (userProfile.value?.passport_expiry_date) {
            sections.documents.completed++
            sections.documents.items.push('Passport Expiry Date')
        }
        sections.documents.total = 3

        // Calculate total
        Object.values(sections).forEach(section => {
            completed += section.completed
            total += section.total
        })

        const percentage = total > 0 ? Math.round((completed / total) * 100) : 0

        return {
            percentage,
            completed,
            total,
            sections,
            isComplete: percentage === 100,
        }
    })

    const getCompletionColor = (percentage) => {
        if (percentage >= 80) return 'text-green-600'
        if (percentage >= 50) return 'text-yellow-600'
        return 'text-red-600'
    }

    const getCompletionBgColor = (percentage) => {
        if (percentage >= 80) return 'bg-green-600'
        if (percentage >= 50) return 'bg-yellow-600'
        return 'bg-red-600'
    }

    return {
        completion: calculateCompletion,
        getCompletionColor,
        getCompletionBgColor,
    }
}
