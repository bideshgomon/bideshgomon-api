import { computed } from 'vue'

/**
 * Bangladeshi Localization Composable
 * Provides formatting functions for Bangladeshi context
 */
export function useBangladeshFormat() {
  
  /**
   * Format currency in BDT with symbol
   */
  const formatCurrency = (amount, showSymbol = true) => {
    if (amount === null || amount === undefined) return showSymbol ? '৳0.00' : '0.00'
    
    const formatted = Number(amount).toLocaleString('en-BD', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
    
    return showSymbol ? `৳${formatted}` : formatted
  }

  /**
   * Format phone number in Bangladesh format
   */
  const formatPhone = (phone) => {
    if (!phone) return ''
    
    // Remove all non-numeric characters
    let cleaned = phone.toString().replace(/\D/g, '')
    
    // Remove leading 880 or 0
    cleaned = cleaned.replace(/^(880|0)/, '')
    
    // Format as 01XXX-XXXXXX
    if (cleaned.length === 10 && cleaned[0] === '1') {
      return `0${cleaned.slice(0, 4)}-${cleaned.slice(4)}`
    }
    
    return phone
  }

  /**
   * Format date in DD/MM/YYYY format (Bangladeshi standard)
   */
  const formatDate = (date, includeTime = false) => {
    if (!date) return ''
    
    const d = new Date(date)
    if (isNaN(d.getTime())) return date
    
    const day = String(d.getDate()).padStart(2, '0')
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const year = d.getFullYear()
    
    let formatted = `${day}/${month}/${year}`
    
    if (includeTime) {
      let hours = d.getHours()
      const minutes = String(d.getMinutes()).padStart(2, '0')
      const ampm = hours >= 12 ? 'PM' : 'AM'
      hours = hours % 12 || 12
      formatted += ` ${hours}:${minutes} ${ampm}`
    }
    
    return formatted
  }

  /**
   * Parse DD/MM/YYYY format to YYYY-MM-DD for database/input[type=date]
   */
  const parseDateToISO = (dateStr) => {
    if (!dateStr) return ''
    
    // If already in YYYY-MM-DD format, return as is
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
      return dateStr
    }
    
    // Parse DD/MM/YYYY format
    const match = dateStr.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/)
    if (match) {
      const day = match[1].padStart(2, '0')
      const month = match[2].padStart(2, '0')
      const year = match[3]
      return `${year}-${month}-${day}`
    }
    
    return dateStr
  }

  /**
   * Format YYYY-MM-DD (from database) to DD/MM/YYYY for display
   */
  const formatDateFromISO = (isoDate) => {
    if (!isoDate) return ''
    
    // Parse YYYY-MM-DD format
    const match = isoDate.match(/^(\d{4})-(\d{2})-(\d{2})/)
    if (match) {
      const year = match[1]
      const month = match[2]
      const day = match[3]
      return `${day}/${month}/${year}`
    }
    
    return formatDate(isoDate)
  }

  /**
   * Format datetime for display
   */
  const formatDateTime = (date) => formatDate(date, true)

  /**
   * Format time only (HH:MM AM/PM)
   */
  const formatTime = (date) => {
    if (!date) return ''
    
    const d = new Date(date)
    if (isNaN(d.getTime())) return date
    
    let hours = d.getHours()
    const minutes = String(d.getMinutes()).padStart(2, '0')
    const ampm = hours >= 12 ? 'PM' : 'AM'
    hours = hours % 12 || 12
    
    return `${hours}:${minutes} ${ampm}`
  }

  /**
   * Get Bangladesh divisions
   */
  const divisions = computed(() => [
    'Dhaka',
    'Chittagong',
    'Rajshahi',
    'Khulna',
    'Barisal',
    'Sylhet',
    'Rangpur',
    'Mymensingh',
  ])

  /**
   * Get popular districts
   */
  const popularDistricts = computed(() => [
    'Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 'Rangpur',
    'Comilla', 'Gazipur', 'Narayanganj', 'Mymensingh', 'Bogura', 'Jessore',
  ])

  /**
   * Validate Bangladesh National ID
   */
  const validateNID = (nid) => {
    if (!nid) return false
    const cleaned = nid.toString().replace(/\D/g, '')
    return cleaned.length === 10 || cleaned.length === 17
  }

  /**
   * Validate Bangladesh mobile number
   */
  const validatePhone = (phone) => {
    if (!phone) return false
    const cleaned = phone.toString().replace(/\D/g, '')
    // Check if starts with 880 or 0 and has valid operator code
    return /^(?:880|0)?1[3-9]\d{8}$/.test(cleaned)
  }

  /**
   * Format NID number
   */
  const formatNID = (nid) => {
    if (!nid) return ''
    const cleaned = nid.toString().replace(/\D/g, '')
    
    if (cleaned.length === 10) {
      // Old format: XXXX-XXXXXX
      return `${cleaned.slice(0, 4)}-${cleaned.slice(4)}`
    } else if (cleaned.length === 17) {
      // New format: XXXX XXXX XXXX XXXXX
      return `${cleaned.slice(0, 4)} ${cleaned.slice(4, 8)} ${cleaned.slice(8, 12)} ${cleaned.slice(12)}`
    }
    
    return nid
  }

  /**
   * Detect mobile operator
   */
  const detectOperator = (phone) => {
    if (!phone) return null
    
    const cleaned = phone.toString().replace(/\D/g, '').replace(/^(880|0)/, '')
    
    if (cleaned.length >= 3) {
      const prefix = cleaned.slice(0, 3)
      
      const operators = {
        Grameenphone: ['017', '013'],
        Banglalink: ['019', '014'],
        Robi: ['018'],
        Airtel: ['016'],
        Teletalk: ['015'],
      }
      
      for (const [name, prefixes] of Object.entries(operators)) {
        if (prefixes.includes(prefix)) {
          return name
        }
      }
    }
    
    return null
  }

  /**
   * Get popular work destinations
   */
  const workDestinations = computed(() => [
    'Saudi Arabia', 'UAE', 'Qatar', 'Oman', 'Kuwait', 'Bahrain', 
    'Malaysia', 'Singapore'
  ])

  /**
   * Get popular education destinations
   */
  const educationDestinations = computed(() => [
    'United States', 'United Kingdom', 'Canada', 'Australia', 
    'Malaysia', 'Germany', 'Japan', 'South Korea'
  ])

  /**
   * Get popular tourism destinations
   */
  const tourismDestinations = computed(() => [
    'India', 'Thailand', 'Malaysia', 'Singapore', 'Turkey', 
    'UAE', 'Nepal', 'Sri Lanka'
  ])

  /**
   * Check if date is Bangladesh weekend (Friday/Saturday)
   */
  const isWeekend = (date = null) => {
    const d = date ? new Date(date) : new Date()
    const dayOfWeek = d.getDay() // 0 (Sunday) to 6 (Saturday)
    return dayOfWeek === 5 || dayOfWeek === 6 // Friday or Saturday
  }

  /**
   * Get Bangladesh working days
   */
  const workingDays = computed(() => [
    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'
  ])

  /**
   * Get weekend days
   */
  const weekendDays = computed(() => ['Friday', 'Saturday'])

  /**
   * Format passport number
   */
  const formatPassport = (passport) => {
    if (!passport) return ''
    return passport.toString().toUpperCase().trim()
  }

  /**
   * Get mobile operators
   */
  const mobileOperators = computed(() => [
    { name: 'Grameenphone', prefixes: ['017', '013'], color: '#00a651' },
    { name: 'Banglalink', prefixes: ['019', '014'], color: '#ea1b2e' },
    { name: 'Robi', prefixes: ['018'], color: '#e31e24' },
    { name: 'Airtel', prefixes: ['016'], color: '#e31e24' },
    { name: 'Teletalk', prefixes: ['015'], color: '#f90000' },
  ])

  /**
   * Convert to Bengali numerals
   */
  const toBengaliNumerals = (number) => {
    const bengali = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
    const english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
    
    let str = number.toString()
    english.forEach((digit, index) => {
      str = str.replace(new RegExp(digit, 'g'), bengali[index])
    })
    
    return str
  }

  /**
   * Get timezone info
   */
  const timezone = computed(() => ({
    name: 'Asia/Dhaka',
    abbr: 'BST',
    fullName: 'Bangladesh Standard Time',
    offset: '+06:00',
    utcOffset: 6,
  }))

  /**
   * Get currency info
   */
  const currency = computed(() => ({
    code: 'BDT',
    symbol: '৳',
    name: 'Bangladeshi Taka',
    nameBengali: 'টাকা',
  }))

  /**
   * Get current time in Bangladesh timezone (Asia/Dhaka, UTC+6)
   */
  const getBangladeshTime = () => {
    const now = new Date()
    // Convert to Bangladesh timezone (UTC+6)
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000)
    const bdTime = new Date(utc + (6 * 3600000)) // +6 hours
    return bdTime
  }

  return {
    // Formatting functions
    formatCurrency,
    formatPhone,
    formatDate,
    formatDateTime,
    formatTime,
    formatDateFromISO,
    parseDateToISO,
    formatNID,
    formatPassport,
    toBengaliNumerals,
    
    // Validation functions
    validateNID,
    validatePhone,
    
    // Helper functions
    detectOperator,
    isWeekend,
    getBangladeshTime,
    
    // Data arrays
    divisions,
    popularDistricts,
    workDestinations,
    educationDestinations,
    tourismDestinations,
    workingDays,
    weekendDays,
    mobileOperators,
    
    // Info objects
    timezone,
    currency,
  }
}
