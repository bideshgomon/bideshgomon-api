/**
 * DEFENSIVE FRONTEND STANDARD
 * Phase 2: Global Null Safety & Safe Data Access
 * 
 * This composable provides defensive utilities for safe data access,
 * eliminating the need for manual null checks in every component.
 * 
 * Usage:
 * import { useSafeData } from '@/Composables/useSafeData'
 * const { safeString, safeNumber, safeArray, safeObject, safeBoolean } = useSafeData()
 */

import { computed, ref } from 'vue'

export function useSafeData() {
  /**
   * Safely converts any value to a string
   * Never returns null or undefined
   */
  const safeString = (value, fallback = '') => {
    if (value === null || value === undefined) return fallback
    return String(value)
  }

  /**
   * Safely converts any value to a number
   * Returns 0 for invalid values
   */
  const safeNumber = (value, fallback = 0) => {
    if (value === null || value === undefined) return fallback
    const num = Number(value)
    return isNaN(num) ? fallback : num
  }

  /**
   * Safely returns an array, never null
   * Ensures all array methods are available
   */
  const safeArray = (value, fallback = []) => {
    if (!value) return fallback
    if (Array.isArray(value)) return value
    return fallback
  }

  /**
   * Safely returns an object, never null
   * Ensures object property access doesn't crash
   */
  const safeObject = (value, fallback = {}) => {
    if (!value || typeof value !== 'object') return fallback
    return value
  }

  /**
   * Safely converts any value to boolean
   * Handles string 'true'/'false', numbers, etc.
   */
  const safeBoolean = (value, fallback = false) => {
    if (value === null || value === undefined) return fallback
    if (typeof value === 'boolean') return value
    if (typeof value === 'string') {
      return value.toLowerCase() === 'true' || value === '1'
    }
    if (typeof value === 'number') return value !== 0
    return fallback
  }

  /**
   * Safely accesses nested object properties
   * Usage: safeGet(obj, 'user.profile.name', 'Unknown')
   */
  const safeGet = (obj, path, fallback = null) => {
    if (!obj || !path) return fallback
    
    const keys = path.split('.')
    let current = obj
    
    for (const key of keys) {
      if (current === null || current === undefined || !(key in current)) {
        return fallback
      }
      current = current[key]
    }
    
    return current ?? fallback
  }

  /**
   * Safely formats string with replacements
   * Never crashes on undefined/null
   */
  const safeReplace = (str, search, replace) => {
    return safeString(str).replace(search, replace)
  }

  /**
   * Safely splits a string
   * Returns empty array for null/undefined
   */
  const safeSplit = (str, delimiter = ',') => {
    return safeString(str).split(delimiter).filter(Boolean)
  }

  /**
   * Safely formats a date
   * Returns fallback for invalid dates
   */
  const safeDate = (value, fallback = 'N/A') => {
    if (!value) return fallback
    try {
      const date = new Date(value)
      if (isNaN(date.getTime())) return fallback
      return date.toLocaleDateString()
    } catch {
      return fallback
    }
  }

  /**
   * Safely formats currency
   * Handles null/undefined gracefully
   */
  const safeCurrency = (value, currency = '৳', fallback = '৳0.00') => {
    const num = safeNumber(value, null)
    if (num === null) return fallback
    return `${currency}${num.toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
  }

  /**
   * Check if value is truthy (not null, undefined, '', 0, false)
   */
  const isTruthy = (value) => {
    return !!value && value !== '' && value !== 0
  }

  /**
   * Check if value is empty (null, undefined, '', [], {})
   */
  const isEmpty = (value) => {
    if (value === null || value === undefined || value === '') return true
    if (Array.isArray(value)) return value.length === 0
    if (typeof value === 'object') return Object.keys(value).length === 0
    return false
  }

  return {
    // Core safe accessors
    safeString,
    safeNumber,
    safeArray,
    safeObject,
    safeBoolean,
    
    // Advanced utilities
    safeGet,
    safeReplace,
    safeSplit,
    safeDate,
    safeCurrency,
    
    // Validation helpers
    isTruthy,
    isEmpty,
  }
}

/**
 * Vue Plugin for global registration
 * Install in main.js/app.js:
 * 
 * import { SafeDataPlugin } from '@/Composables/useSafeData'
 * app.use(SafeDataPlugin)
 */
export const SafeDataPlugin = {
  install(app) {
    // Make available as global properties
    const safe = useSafeData()
    app.config.globalProperties.$safe = safe
    
    // Also provide for composition API
    app.provide('safe', safe)
  }
}

