/**
 * Global Date Formatting Helpers - DD MM YYYY Format
 * 
 * Usage in Vue components:
 * import { formatDate, formatDateTime, formatTime } from '@/Utils/dateFormat';
 * 
 * Or use globally:
 * {{ formatDate(application.created_at) }} → 27 Nov 2025
 */

/**
 * Format date to DD MM YYYY format
 * @param {string|Date|null} date - Date to format
 * @param {string} separator - Separator character (' ', '-', '/')
 * @param {boolean} monthName - Use month name (Nov) instead of number (11)
 * @returns {string} Formatted date
 * 
 * Examples:
 * formatDate('2025-11-27') → '27 11 2025'
 * formatDate('2025-11-27', '-') → '27-11-2025'
 * formatDate('2025-11-27', ' ', true) → '27 Nov 2025'
 */
export function formatDate(date, separator = ' ', monthName = false) {
    if (!date) return '';
    
    try {
        const dateObj = typeof date === 'string' ? new Date(date) : date;
        if (isNaN(dateObj.getTime())) return '';
        
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = dateObj.getMonth() + 1;
        const year = dateObj.getFullYear();
        
        if (monthName) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${day}${separator}${months[dateObj.getMonth()]}${separator}${year}`;
        }
        
        const monthStr = String(month).padStart(2, '0');
        return `${day}${separator}${monthStr}${separator}${year}`;
    } catch (error) {
        console.error('Date formatting error:', error);
        return '';
    }
}

/**
 * Format datetime to DD MM YYYY HH:MM format
 * @param {string|Date|null} datetime - Datetime to format
 * @param {boolean} monthName - Use month name
 * @returns {string} Formatted datetime
 * 
 * Examples:
 * formatDateTime('2025-11-27 14:30:00') → '27 11 2025 14:30'
 * formatDateTime('2025-11-27 14:30:00', true) → '27 Nov 2025 14:30'
 */
export function formatDateTime(datetime, monthName = false) {
    if (!datetime) return '';
    
    try {
        const dateObj = typeof datetime === 'string' ? new Date(datetime) : datetime;
        if (isNaN(dateObj.getTime())) return '';
        
        const datePart = formatDate(dateObj, ' ', monthName);
        const hours = String(dateObj.getHours()).padStart(2, '0');
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
        
        return `${datePart} ${hours}:${minutes}`;
    } catch (error) {
        console.error('DateTime formatting error:', error);
        return '';
    }
}

/**
 * Format time only to HH:MM AM/PM
 * @param {string|Date|null} datetime - Datetime to extract time from
 * @returns {string} Formatted time
 * 
 * Examples:
 * formatTime('2025-11-27 14:30:00') → '02:30 PM'
 * formatTime('2025-11-27 09:15:00') → '09:15 AM'
 */
export function formatTime(datetime) {
    if (!datetime) return '';
    
    try {
        const dateObj = typeof datetime === 'string' ? new Date(datetime) : datetime;
        if (isNaN(dateObj.getTime())) return '';
        
        let hours = dateObj.getHours();
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 should be 12
        const hoursStr = String(hours).padStart(2, '0');
        
        return `${hoursStr}:${minutes} ${ampm}`;
    } catch (error) {
        console.error('Time formatting error:', error);
        return '';
    }
}

/**
 * Parse DD MM YYYY format to Date object
 * @param {string} dateString - Date string in DD MM YYYY format
 * @param {string} separator - Separator used in the string
 * @returns {Date|null} Parsed date or null
 * 
 * Examples:
 * parseDateDDMMYYYY('27 11 2025') → Date object
 * parseDateDDMMYYYY('27-11-2025', '-') → Date object
 */
export function parseDateDDMMYYYY(dateString, separator = ' ') {
    if (!dateString) return null;
    
    try {
        // Normalize separators
        const normalized = (dateString || '').replace(/[\/\-\s]+/g, '-');
        const parts = normalized.split('-');
        
        if (parts.length === 3) {
            const [day, month, year] = parts;
            const dateObj = new Date(year, month - 1, day);
            
            if (!isNaN(dateObj.getTime())) {
                return dateObj;
            }
        }
        
        return null;
    } catch (error) {
        console.error('Date parsing error:', error);
        return null;
    }
}

/**
 * Format date for display in tables/lists (short version with month name)
 * @param {string|Date|null} date
 * @returns {string} '27 Nov 2025'
 */
export function formatDateShort(date) {
    return formatDate(date, ' ', true);
}

/**
 * Format datetime for display (short version with month name)
 * @param {string|Date|null} datetime
 * @returns {string} '27 Nov 2025 14:30'
 */
export function formatDateTimeShort(datetime) {
    return formatDateTime(datetime, true);
}

/**
 * Get relative time (e.g., "2 hours ago")
 * @param {string|Date|null} date
 * @returns {string}
 */
export function formatRelative(date) {
    if (!date) return '';
    
    try {
        const dateObj = typeof date === 'string' ? new Date(date) : date;
        if (isNaN(dateObj.getTime())) return '';
        
        const now = new Date();
        const diffInMs = now - dateObj;
        const diffInMinutes = Math.floor(diffInMs / 60000);
        const diffInHours = Math.floor(diffInMinutes / 60);
        const diffInDays = Math.floor(diffInHours / 24);
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
        if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
        if (diffInDays < 7) return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
        
        return formatDateShort(dateObj);
    } catch (error) {
        console.error('Relative time formatting error:', error);
        return '';
    }
}

/**
 * Format date for input fields (YYYY-MM-DD for HTML date input)
 * @param {string|Date|null} date
 * @returns {string} '2025-11-27'
 */
export function formatDateForInput(date) {
    if (!date) return '';
    
    try {
        const dateObj = typeof date === 'string' ? new Date(date) : date;
        if (isNaN(dateObj.getTime())) return '';
        
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    } catch (error) {
        console.error('Date input formatting error:', error);
        return '';
    }
}

// Vue composable for easy use in components
export function useDateFormat() {
    return {
        formatDate,
        formatDateTime,
        formatTime,
        formatDateShort,
        formatDateTimeShort,
        formatRelative,
        formatDateForInput,
        parseDateDDMMYYYY,
    };
}

// Default export
export default {
    formatDate,
    formatDateTime,
    formatTime,
    formatDateShort,
    formatDateTimeShort,
    formatRelative,
    formatDateForInput,
    parseDateDDMMYYYY,
    useDateFormat,
};
