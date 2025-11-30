<template>
    <div class="relative w-full" ref="searchContainer">
        <!-- Search Input - No Icon Inside -->
        <div class="relative">
            <input
                v-model="searchQuery"
                @input="handleInput"
                @focus="showSuggestions = true"
                @keydown.enter="performSearch"
                @keydown.down.prevent="navigateSuggestions(1)"
                @keydown.up.prevent="navigateSuggestions(-1)"
                @keydown.esc="closeSuggestions"
                type="text"
                :placeholder="placeholder"
                class="block w-full px-4 pr-32 sm:pr-36 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm truncate"
            />
            <div class="absolute inset-y-0 right-1 flex items-center gap-1">
                <!-- Filter Button -->
                <button
                    @click="toggleFilters"
                    type="button"
                    class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 flex-shrink-0"
                    title="Advanced Filters"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
                <!-- Search Button -->
                <button
                    @click="performSearch"
                    type="button"
                    :disabled="!searchQuery || searching"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-xs sm:text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex-shrink-0"
                >
                    <svg v-if="!searching" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <svg v-else class="animate-spin h-3.5 w-3.5 sm:h-4 sm:w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="hidden sm:inline">{{ searching ? 'Searching...' : 'Search' }}</span>
                </button>
            </div>
        </div>

        <!-- Suggestions Dropdown -->
        <div
            v-if="showSuggestions && (suggestions.popular?.length || suggestions.users?.length || suggestions.blog?.length)"
            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto"
        >
            <!-- Popular Searches -->
            <div v-if="suggestions.popular?.length" class="p-2 border-b border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-3 py-2">Popular Searches</p>
                <button
                    v-for="(query, index) in suggestions.popular"
                    :key="'popular-' + index"
                    @click="selectSuggestion(query)"
                    class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                >
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ query }}</span>
                    </div>
                </button>
            </div>

            <!-- User Matches -->
            <div v-if="suggestions.users?.length" class="p-2 border-b border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-3 py-2">Users</p>
                <button
                    v-for="(user, index) in suggestions.users"
                    :key="'user-' + index"
                    @click="selectSuggestion(user)"
                    class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                >
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ user }}</span>
                    </div>
                </button>
            </div>

            <!-- Blog Matches -->
            <div v-if="suggestions.blog?.length" class="p-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-3 py-2">Blog Posts</p>
                <button
                    v-for="(post, index) in suggestions.blog"
                    :key="'blog-' + index"
                    @click="selectSuggestion(post)"
                    class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                >
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ post }}</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Search Filters Panel -->
        <SearchFilters
            v-if="showFilters"
            v-model="filters"
            @close="showFilters = false"
            @apply="applyFilters"
        />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import SearchFilters from './SearchFilters.vue'

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search...'
    },
    autoFocus: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['search', 'results'])

const searchQuery = ref('')
const searchContainer = ref(null)
const showSuggestions = ref(false)
const showFilters = ref(false)
const searching = ref(false)
const suggestions = ref({
    popular: [],
    users: [],
    blog: []
})
const filters = ref({})
let suggestionTimeout = null

// Fetch suggestions with debounce
const handleInput = () => {
    clearTimeout(suggestionTimeout)
    
    if (searchQuery.value.length >= 2) {
        suggestionTimeout = setTimeout(async () => {
            try {
                const response = await axios.get(route('api.search.suggestions'), {
                    params: { query: searchQuery.value }
                })
                suggestions.value = response.data.suggestions
                showSuggestions.value = true
            } catch (error) {
                console.error('Failed to fetch suggestions:', error)
            }
        }, 300) // 300ms debounce
    } else {
        suggestions.value = { popular: [], users: [], blog: [] }
    }
}

// Perform search
const performSearch = async () => {
    if (!searchQuery.value || searching.value) return

    searching.value = true
    showSuggestions.value = false

    try {
        const response = await axios.get(route('api.search.search'), {
            params: {
                query: searchQuery.value,
                filters: filters.value
            }
        })

        emit('search', searchQuery.value)
        emit('results', response.data)
    } catch (error) {
        console.error('Search failed:', error)
    } finally {
        searching.value = false
    }
}

// Select suggestion
const selectSuggestion = (suggestion) => {
    searchQuery.value = suggestion
    showSuggestions.value = false
    performSearch()
}

// Toggle filters
const toggleFilters = () => {
    showFilters.value = !showFilters.value
}

// Apply filters
const applyFilters = (newFilters) => {
    filters.value = newFilters
    showFilters.value = false
    if (searchQuery.value) {
        performSearch()
    }
}

// Close suggestions
const closeSuggestions = () => {
    showSuggestions.value = false
}

// Navigate suggestions with keyboard
const navigateSuggestions = (direction) => {
    // TODO: Implement keyboard navigation
}

// Close suggestions when clicking outside
const handleClickOutside = (event) => {
    if (searchContainer.value && !searchContainer.value.contains(event.target)) {
        showSuggestions.value = false
    }
}

// Setup
if (props.autoFocus) {
    setTimeout(() => {
        searchContainer.value?.querySelector('input')?.focus()
    }, 100)
}

document.addEventListener('click', handleClickOutside)

// Cleanup
import { onUnmounted } from 'vue'
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    clearTimeout(suggestionTimeout)
})
</script>
