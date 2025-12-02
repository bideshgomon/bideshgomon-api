<template>
    <Head title="Global Directory - Embassies, Consulates & Services Worldwide" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Header />
        
        <!-- Modern Hero Section with Background Pattern -->
        <div class="relative bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
                <div class="text-center max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-xs sm:text-sm font-medium mb-4 sm:mb-6">
                        <BuildingOffice2Icon class="h-4 w-4 sm:h-5 sm:w-5" />
                        <span>{{ directories.total || 0 }}+ Verified Organizations</span>
                    </div>
                    
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 leading-tight">
                        Global Directory
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg text-blue-100 mb-6 sm:mb-8 max-w-2xl mx-auto px-4">
                        Find embassies, airlines, training centers, and essential services worldwide
                    </p>
                    
                    <!-- Enhanced Search Bar -->
                    <div class="max-w-2xl mx-auto px-2 sm:px-0">
                        <div class="relative flex items-center bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl overflow-hidden">
                            <MagnifyingGlassIcon class="absolute left-4 sm:left-5 h-5 w-5 sm:h-6 sm:w-6 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                @input="debouncedSearch"
                                type="text"
                                placeholder="Search by name, location, or service..."
                                class="w-full pl-12 sm:pl-14 pr-12 sm:pr-14 py-3 sm:py-4 text-sm sm:text-base text-gray-900 placeholder-gray-400 focus:outline-none bg-transparent"
                            />
                            <button 
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-4 p-1 hover:bg-gray-100 rounded-full transition"
                            >
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="flex items-center justify-center gap-4 sm:gap-6 lg:gap-8 mt-6 sm:mt-8">
                        <div class="text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold">{{ categories.length }}+</div>
                            <div class="text-xs sm:text-sm text-blue-200">Categories</div>
                        </div>
                        <div class="h-8 sm:h-10 w-px bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold">{{ directories.total || 0 }}</div>
                            <div class="text-xs sm:text-sm text-blue-200">Organizations</div>
                        </div>
                        <div class="h-8 sm:h-10 w-px bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold">50+</div>
                            <div class="text-xs sm:text-sm text-blue-200">Countries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Mobile Category Pills (visible on mobile/tablet only) -->
            <div class="lg:hidden mb-6 -mx-4 px-4 overflow-x-auto scrollbar-hide">
                <div class="flex gap-2 pb-2">
                    <button
                        @click="filterByCategory(null)"
                        class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all"
                        :class="!selectedCategory ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200'"
                    >
                        All Categories
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="filterByCategory(category.slug)"
                        class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all whitespace-nowrap"
                        :class="selectedCategory === category.slug ? 'text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200'"
                        :style="selectedCategory === category.slug ? { backgroundColor: categoryColors[category.slug]?.bg || '#6B7280' } : {}"
                    >
                        {{ category.name }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Enhanced Sidebar Filters (desktop only) -->
                <div class="lg:col-span-1 hidden lg:block">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto scrollbar-hide">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Refine Search
                            </h3>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Category
                                </span>
                            </label>
                            <select
                                v-model="selectedCategory"
                                @change="filterByCategory(selectedCategory)"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option :value="null">All Categories</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.slug"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Country Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center gap-2">
                                    <GlobeAltIcon class="h-4 w-4 text-gray-400" />
                                    Country
                                </span>
                            </label>
                            <select
                                v-model="form.country"
                                @change="applyFilters"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option value="">All Countries</option>
                                <option value="1">Bangladesh</option>
                                <!-- Add more countries dynamically -->
                            </select>
                        </div>

                        <!-- City Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center gap-2">
                                    <MapPinIcon class="h-4 w-4 text-gray-400" />
                                    City / Region
                                </span>
                            </label>
                            <input
                                v-model="form.city"
                                @input="debouncedFilter"
                                type="text"
                                placeholder="e.g., Dhaka, London..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            />
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    Sort By
                                </span>
                            </label>
                            <select
                                v-model="form.sort_by"
                                @change="applyFilters"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option value="name">Name (A-Z)</option>
                                <option value="views">Most Popular</option>
                                <option value="recent">Recently Added</option>
                            </select>
                        </div>

                        <!-- Clear Filters Button -->
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset All Filters
                        </button>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-900 mb-1">Verified Information</p>
                                    <p class="text-xs text-blue-700">All directory listings are verified and regularly updated for accuracy.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Directory Grid -->
                <div class="lg:col-span-3">
                    <!-- Results Header with View Toggle -->
                    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="text-sm text-gray-600">
                                Showing <span class="font-semibold text-gray-900">{{ directories.data.length }}</span> 
                                of <span class="font-semibold text-gray-900">{{ directories.total }}</span> results
                            </p>
                            <p v-if="searchQuery || selectedCategory || form.city" class="text-xs text-gray-500 mt-1">
                                Filtered results
                            </p>
                        </div>
                        
                        <!-- View Toggle (Grid/List) -->
                        <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm border border-gray-200">
                            <button
                                @click="viewMode = 'grid'"
                                :class="[
                                    'px-3 py-2 rounded-md text-sm font-medium transition-all',
                                    viewMode === 'grid' 
                                        ? 'bg-blue-600 text-white shadow-sm' 
                                        : 'text-gray-600 hover:bg-gray-100'
                                ]"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="[
                                    'px-3 py-2 rounded-md text-sm font-medium transition-all',
                                    viewMode === 'list' 
                                        ? 'bg-blue-600 text-white shadow-sm' 
                                        : 'text-gray-600 hover:bg-gray-100'
                                ]"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modern Directory Cards - Grid View -->
                    <div v-if="directories.data.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 lg:gap-6">
                        <Link
                            v-for="directory in directories.data"
                            :key="directory.id"
                            :href="route('directory.show', directory.slug)"
                            class="group bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1 active:scale-95 flex flex-col h-[360px] sm:h-[380px]"
                        >
                            <!-- Image with Overlay -->
                            <div class="relative h-48 overflow-hidden flex-shrink-0">
                                <!-- Category-specific gradient overlay -->
                                <div 
                                    class="absolute inset-0 bg-gradient-to-br opacity-90" 
                                    :style="{ 
                                        background: `linear-gradient(135deg, ${categoryColors[directory.category?.slug]?.bg || '#6B7280'}dd, ${categoryColors[directory.category?.slug]?.bg || '#6B7280'}99)` 
                                    }"
                                ></div>
                                
                                <img
                                    v-if="directory.featured_image"
                                    :src="directory.featured_image"
                                    :alt="directory.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <BuildingOffice2Icon class="h-20 w-20 text-white/30" />
                                </div>
                                
                                <!-- Category Icon in Banner (Left) -->
                                <div v-if="directory.category" class="absolute top-4 left-4 p-2.5 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg">
                                    <!-- Embassy Icon -->
                                    <BuildingOffice2Icon v-if="directory.category.slug === 'embassy'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#6B7280' }" />
                                    <!-- Airline Icon -->
                                    <svg v-else-if="directory.category.slug === 'airline'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#3B82F6' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <!-- Training Center Icon -->
                                    <svg v-else-if="directory.category.slug === 'training-center'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#10B981' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                    </svg>
                                    <!-- Medical Center Icon -->
                                    <svg v-else-if="directory.category.slug === 'medical-center'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#EF4444' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <!-- Bank Icon -->
                                    <svg v-else-if="directory.category.slug === 'bank'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#F59E0B' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <!-- Legal Services Icon -->
                                    <svg v-else-if="directory.category.slug === 'legal-services'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#6366F1' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                    <!-- School Icon -->
                                    <svg v-else-if="directory.category.slug === 'school'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#EC4899' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <!-- Library Icon -->
                                    <svg v-else-if="directory.category.slug === 'library'" class="h-6 w-6" :style="{ color: categoryColors[directory.category.slug]?.bg || '#14B8A6' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    <!-- Default Icon -->
                                    <BuildingOffice2Icon v-else class="h-6 w-6" :style="{ color: categoryColors[directory.category?.slug]?.bg || '#6B7280' }" />
                                </div>
                                
                                <!-- Verified Badge (Right) -->
                                <div v-if="directory.is_verified" class="absolute top-4 right-4">
                                    <div class="p-2 bg-green-500 rounded-full shadow-lg" title="Verified">
                                        <svg class="h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-grow">
                                <!-- Name -->
                                <h3 class="text-base font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight min-h-[48px]">
                                    {{ directory.name }}
                                </h3>

                                <!-- Location -->
                                <div v-if="directory.city" class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                    <MapPinIcon class="h-4 w-4 flex-shrink-0 text-gray-400" />
                                    <span class="truncate">{{ directory.city }}</span>
                                </div>

                                <!-- Phone -->
                                <div v-if="directory.phone" class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                    <PhoneIcon class="h-4 w-4 flex-shrink-0 text-gray-400" />
                                    <span class="truncate">{{ directory.phone }}</span>
                                </div>

                                <!-- Spacer to push footer to bottom -->
                                <div class="flex-grow"></div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                                    <div class="flex items-center gap-3">
                                        <!-- Category Badge - Solid Color to Match Banner -->
                                        <div v-if="directory.category">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap text-white shadow-sm"
                                                :style="{ 
                                                    backgroundColor: categoryColors[directory.category.slug]?.bg || '#6B7280'
                                                }"
                                            >
                                                <span class="leading-none">{{ directory.category.name }}</span>
                                            </span>
                                        </div>
                                        <!-- View Count -->
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                            <EyeIcon class="h-4 w-4" />
                                            <span class="font-medium">{{ formatNumber(directory.views_count || 0) }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 text-blue-600 text-sm font-semibold group-hover:gap-2 transition-all">
                                        Details
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- List View -->
                    <div v-if="directories.data.length > 0 && viewMode === 'list'" class="space-y-4 sm:space-y-5">
                        <Link
                            v-for="directory in directories.data"
                            :key="directory.id"
                            :href="route('directory.show', directory.slug)"
                            class="group bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200 flex flex-col sm:flex-row active:scale-[0.99]"
                        >
                            <!-- Thumbnail -->
                            <div class="relative w-full sm:w-48 h-40 sm:h-auto flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200">
                                <img
                                    v-if="directory.featured_image"
                                    :src="directory.featured_image"
                                    :alt="directory.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                                    <BuildingOffice2Icon class="h-12 w-12 text-white/30" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 p-4 sm:p-6 flex flex-col">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ directory.name }}
                                            </h3>
                                            <span
                                                v-if="directory.category"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium"
                                                :style="{ backgroundColor: directory.category.color + '20', color: directory.category.color }"
                                            >
                                                {{ directory.category.name }}
                                            </span>
                                        </div>
                                        
                                        <p v-if="directory.description" class="text-sm text-gray-600 line-clamp-2 mb-3">
                                            {{ stripHtml(directory.description) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 text-sm text-gray-600 mt-auto">
                                    <div v-if="directory.address" class="flex items-center gap-2">
                                        <MapPinIcon class="h-4 w-4 text-gray-400" />
                                        <span>{{ directory.city || directory.address }}</span>
                                    </div>
                                    <div v-if="directory.phone" class="flex items-center gap-2">
                                        <PhoneIcon class="h-4 w-4 text-gray-400" />
                                        <span>{{ directory.phone }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 ml-auto">
                                        <EyeIcon class="h-4 w-4 text-gray-400" />
                                        <span>{{ formatNumber(directory.views_count || 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Enhanced Empty State -->
                    <div v-if="directories.data.length === 0" class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                            <BuildingOffice2Icon class="h-10 w-10 text-gray-400" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No results found</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            We couldn't find any directories matching your criteria. Try adjusting your search or filters.
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <button
                                @click="clearFilters"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold shadow-lg shadow-blue-500/30"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Clear All Filters
                            </button>
                            <Link
                                :href="route('directory.index')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-semibold border border-gray-300"
                            >
                                View All Directories
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="directories.data.length > 0" class="mt-8">
                        <Pagination :links="directories.links" />
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import Pagination from '@/Components/Pagination.vue';
import {
    MagnifyingGlassIcon,
    BuildingOffice2Icon,
    MapPinIcon,
    PhoneIcon,
    EyeIcon,
    GlobeAltIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    directories: Object,
    categories: Array,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || null);
const viewMode = ref('grid'); // 'grid' or 'list'

const form = ref({
    search: props.filters.search || '',
    category: props.filters.category || '',
    country: props.filters.country || '',
    city: props.filters.city || '',
    sort_by: props.filters.sort_by || 'name',
    sort_order: props.filters.sort_order || 'asc',
});

// Helper Functions
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
};

const stripHtml = (html) => {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const clearSearch = () => {
    searchQuery.value = '';
    form.value.search = '';
    applyFilters();
};

let searchTimeout;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        form.value.search = searchQuery.value;
        applyFilters();
    }, 500);
};

let filterTimeout;
const debouncedFilter = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const filterByCategory = (slug) => {
    selectedCategory.value = slug;
    form.value.category = slug || '';
    applyFilters();
};

const applyFilters = () => {
    router.get(route('directory.index'), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    form.value = {
        search: '',
        category: '',
        country: '',
        city: '',
        sort_by: 'name',
        sort_order: 'asc',
    };
    applyFilters();
};

// Category color palette
const categoryColors = {
    embassy: { bg: '#8B5CF6', light: '#EDE9FE', border: '#C4B5FD' },
    airline: { bg: '#3B82F6', light: '#DBEAFE', border: '#93C5FD' },
    'training-center': { bg: '#10B981', light: '#D1FAE5', border: '#6EE7B7' },
    'medical-center': { bg: '#EF4444', light: '#FEE2E2', border: '#FCA5A5' },
    bank: { bg: '#F59E0B', light: '#FEF3C7', border: '#FCD34D' },
    'legal-services': { bg: '#6366F1', light: '#E0E7FF', border: '#A5B4FC' },
    school: { bg: '#EC4899', light: '#FCE7F3', border: '#F9A8D4' },
    library: { bg: '#14B8A6', light: '#CCFBF1', border: '#5EEAD4' },
};

const getCategoryStyle = (category, isSelected) => {
    const colors = categoryColors[category.slug] || { bg: '#6B7280', light: '#F3F4F6', border: '#D1D5DB' };
    
    if (isSelected) {
        return {
            backgroundColor: colors.bg,
            borderColor: colors.bg,
        };
    }
    
    return {
        backgroundColor: colors.light,
        borderColor: colors.border,
        color: colors.bg,
    };
};
</script>

<style scoped>
/* Hide scrollbar but keep functionality */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Smooth animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Grid hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Snap scroll for horizontal categories */
.snap-x {
    scroll-snap-type: x mandatory;
}
.snap-start {
    scroll-snap-align: start;
}
</style>
