import { computed } from 'vue'

export function useProfileCompletion(user, userProfile) {
    const calculateCompletion = computed(() => {
        let score = 0
        const sections = {}

        // 1. Basic Information (10 points)
        sections.basic = { score: 0, max: 10, items: [] }
        if (user.value?.name && user.value?.email) {
            sections.basic.score = 10
            sections.basic.items.push('Name & Email')
        }

        // 2. Profile Details (10 points)
        sections.profile = { score: 0, max: 10, items: [] }
        if (userProfile.value) {
            const profileFields = [
                userProfile.value.dob,
                userProfile.value.gender,
                userProfile.value.nationality,
                userProfile.value.nid,
                userProfile.value.present_address_line
            ]
            const filled = profileFields.filter(v => v != null && v !== '').length
            sections.profile.score = Math.round((filled / 5) * 10)
            if (filled > 0) sections.profile.items.push(`${filled}/5 fields`)
        }

        // 3. Education & Qualifications (10 points)
        sections.education = { score: 0, max: 10, items: [] }
        const educationCount = user.value?.educations?.length || 0
        if (educationCount > 0) {
            sections.education.score = 10
            sections.education.items.push(`${educationCount} education(s)`)
        }

        // 4. Work Experience (10 points)
        sections.work = { score: 0, max: 10, items: [] }
        const workCount = user.value?.work_experiences?.length || 0
        if (workCount > 0) {
            sections.work.score = 10
            sections.work.items.push(`${workCount} work experience(s)`)
        }

        // 5. Skills & Expertise (10 points)
        sections.skills = { score: 0, max: 10, items: [] }
        const skillsCount = user.value?.skills?.length || 0
        if (skillsCount > 0) {
            sections.skills.score = 10
            sections.skills.items.push(`${skillsCount} skill(s)`)
        }

        // 6. Travel History (5 points)
        sections.travel = { score: 0, max: 5, items: [] }
        const travelCount = user.value?.travel_history?.length || 0
        if (travelCount > 0) {
            sections.travel.score = 5
            sections.travel.items.push(`${travelCount} travel(s)`)
        }

        // 7. Family Information (5 points)
        sections.family = { score: 0, max: 5, items: [] }
        const familyCount = user.value?.family_members?.length || 0
        if (familyCount > 0) {
            sections.family.score = 5
            sections.family.items.push(`${familyCount} family member(s)`)
        }

        // 8. Financial Information (10 points)
        sections.financial = { score: 0, max: 10, items: [] }
        if (userProfile.value) {
            const financialFields = [
                userProfile.value.monthly_income_bdt,
                userProfile.value.employer_name,
                userProfile.value.bank_balance_bdt
            ]
            const filled = financialFields.filter(v => v != null && v !== '').length
            sections.financial.score = Math.round((filled / 3) * 10)
            if (filled > 0) sections.financial.items.push(`${filled}/3 fields`)
        }

        // 9. Language Proficiency (10 points)
        sections.languages = { score: 0, max: 10, items: [] }
        const languageCount = user.value?.languages?.length || 0
        if (languageCount > 0) {
            sections.languages.score = 10
            sections.languages.items.push(`${languageCount} language(s)`)
        }

        // 10. Background & Security (5 points)
        sections.security = { score: 0, max: 5, items: [] }
        const securityCount = user.value?.security_information?.length || 0
        if (securityCount > 0) {
            sections.security.score = 5
            sections.security.items.push('Security info added')
        }

        // 11. Phone Numbers (5 points)
        sections.phone = { score: 0, max: 5, items: [] }
        const phoneCount = user.value?.phone_numbers?.length || 0
        if (phoneCount > 0) {
            sections.phone.score = 5
            sections.phone.items.push(`${phoneCount} phone number(s)`)
        }

        // 12. Passport Information (10 points)
        sections.passport = { score: 0, max: 10, items: [] }
        if (userProfile.value?.passport_number) {
            sections.passport.score = 10
            sections.passport.items.push('Passport added')
        }

        // Calculate total score
        Object.values(sections).forEach(section => {
            score += section.score
        })

        const percentage = Math.min(100, score)
        const completed = Object.values(sections).filter(s => s.score === s.max).length
        const total = Object.keys(sections).length

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
        return 'text-orange-600'
    }

    const getCompletionBgColor = (percentage) => {
        if (percentage >= 80) return 'bg-green-600'
        if (percentage >= 50) return 'bg-yellow-600'
        return 'bg-orange-600'
    }

    return {
        completion: calculateCompletion,
        getCompletionColor,
        getCompletionBgColor,
    }
}
