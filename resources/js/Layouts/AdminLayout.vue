<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NotificationBell from '@/Components/NotificationBell.vue'
import GlobalSearch from '@/Components/GlobalSearch.vue'
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue'
import {
  HomeIcon,
  Bars3Icon,
  XMarkIcon,
  AcademicCapIcon,
  GlobeAltIcon,
  MapIcon,
  UsersIcon,
  BriefcaseIcon,
  ClipboardDocumentListIcon,
  DocumentTextIcon,
  NewspaperIcon,
  ChartBarIcon,
  MegaphoneIcon,
  Cog6ToothIcon,
  BuildingLibraryIcon,
  UserGroupIcon,
  FolderIcon,
  TruckIcon,
  ShieldCheckIcon,
  GiftIcon,
  BanknotesIcon,
  RectangleStackIcon,
  TableCellsIcon,
  EnvelopeIcon,
  ClockIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  ChatBubbleLeftRightIcon,
  CurrencyDollarIcon,
  SparklesIcon,
  ChatBubbleBottomCenterTextIcon,
  QuestionMarkCircleIcon,
  BellIcon,
  MoonIcon,
  SunIcon,
  CommandLineIcon,
} from '@heroicons/vue/24/outline'

const showingNavigationDropdown = ref(false)
const showMobileSearch = ref(false)
const collapsedSections = ref({})
const sidebarCollapsed = ref(false)
const darkMode = ref(false)
const showCommandPalette = ref(false)
const page = usePage()
const user = computed(() => page.props.auth.user)

// Toggle dark mode
const toggleDarkMode = () => {
  darkMode.value = !darkMode.value
  localStorage.setItem('darkMode', darkMode.value.toString())
  if (darkMode.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

// Load preferences and setup event listeners
onMounted(() => {
  // Load dark mode preference
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode !== null) {
    darkMode.value = savedDarkMode === 'true'
    if (darkMode.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  // Load sidebar collapsed state
  const savedSidebarState = localStorage.getItem('sidebarCollapsed')
  if (savedSidebarState !== null) {
    sidebarCollapsed.value = savedSidebarState === 'true'
  }

  // Command Palette keyboard shortcut (Cmd+K / Ctrl+K)
  const handleKeyPress = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault()
      showCommandPalette.value = !showCommandPalette.value
    }
    // ESC to close command palette
    if (e.key === 'Escape' && showCommandPalette.value) {
      showCommandPalette.value = false
    }
  }
  window.addEventListener('keydown', handleKeyPress)

  // Cleanup
  return () => {
    window.removeEventListener('keydown', handleKeyPress)
  }
})

// Dynamic profile route based on user role
const profileRoute = computed(() => {
  const roleSlug = user.value?.role?.slug
  if (roleSlug === 'agency') {
    return 'agency.profile.show'
  }
  if (roleSlug === 'consultant') {
    return 'consultant.profile.show'
  }
  return 'admin.profile.show'
})

const toggleSection = sectionName => {
  collapsedSections.value[sectionName] = !collapsedSections.value[sectionName]
}

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString())
}

// ========================================
// ADMIN NAVIGATION - Clean Organized Structure
// Based on actual routes and controllers available
// ========================================
const navigation = [
  // DASHBOARD
  {
    name: 'Dashboard',
    href: route('admin.dashboard'),
    icon: HomeIcon,
    current: route().current('admin.dashboard'),
    section: 'dashboard',
  },

  // USER MANAGEMENT
  {
    name: 'Users',
    href: route('admin.users.index'),
    icon: UsersIcon,
    current: route().current('admin.users.*'),
    section: 'users',
  },

  // JOBS & EMPLOYMENT
  {
    name: 'Job Postings',
    href: route('admin.jobs.index'),
    icon: BriefcaseIcon,
    current: route().current('admin.jobs.*'),
    section: 'jobs',
  },
  {
    name: 'Job Applications',
    href: route('admin.job-applications.index'),
    icon: ClipboardDocumentListIcon,
    current: route().current('admin.job-applications.*') || route().current('admin.applications.*'),
    section: 'jobs',
  },

  // VISA & TRAVEL SERVICES
  {
    name: 'Visa Applications',
    href: route('admin.visa-applications.index'),
    icon: DocumentTextIcon,
    current: route().current('admin.visa-applications.*'),
    section: 'travel',
  },
  {
    name: 'Visa Requirements',
    href: route('admin.visa-requirements.index'),
    icon: ClipboardDocumentListIcon,
    current: route().current('admin.visa-requirements.*'),
    section: 'travel',
  },
  {
    name: 'Hotels',
    href: route('admin.hotels.index'),
    icon: BuildingLibraryIcon,
    current: route().current('admin.hotels.*'),
    section: 'travel',
  },
  {
    name: 'Hotel Bookings',
    href: route('admin.hotel-bookings.index'),
    icon: ClockIcon,
    current: route().current('admin.hotel-bookings.*'),
    section: 'travel',
  },
  {
    name: 'Flight Requests',
    href: route('admin.flight-requests.index'),
    icon: TruckIcon,
    current: route().current('admin.flight-requests.*'),
    section: 'travel',
  },

  // AGENCY MANAGEMENT
  {
    name: 'Agency Assignments',
    href: route('admin.agency-assignments.index'),
    icon: UserGroupIcon,
    current: route().current('admin.agency-assignments.*'),
    section: 'agencies',
  },

  // FINANCIAL & REWARDS
  {
    name: 'Wallets',
    href: route('admin.wallets.index'),
    icon: BanknotesIcon,
    current: route().current('admin.wallets.*'),
    section: 'financial',
  },
  {
    name: 'Rewards',
    href: route('admin.rewards.index'),
    icon: GiftIcon,
    current: route().current('admin.rewards.*'),
    section: 'financial',
  },

  // CONTENT & MARKETING
  {
    name: 'Marketing Campaigns',
    href: route('admin.marketing-campaigns.index'),
    icon: MegaphoneIcon,
    current: route().current('admin.marketing-campaigns.*'),
    section: 'content',
  },
  // Note: Blog routes are nested resources and need refactoring
  // Temporarily disabled until routes are fixed
  // {
  //   name: 'Blog Posts',
  //   href: route('admin.blog.posts.index'),
  //   icon: NewspaperIcon,
  //   current: route().current('admin.blog.posts.*'),
  //   section: 'content',
  // },
  // {
  //   name: 'Blog Categories',
  //   href: route('admin.blog.categories.index'),
  //   icon: FolderIcon,
  //   current: route().current('admin.blog.categories.*'),
  //   section: 'content',
  // },
  // {
  //   name: 'Blog Tags',
  //   href: route('admin.blog.tags.index'),
  //   icon: TableCellsIcon,
  //   current: route().current('admin.blog.tags.*'),
  //   section: 'content',
  // },

  // SERVICES & MODULES
  {
    name: 'Service Management',
    href: route('admin.services.index'),
    icon: Cog6ToothIcon,
    current: route().current('admin.services.*'),
    section: 'services',
  },
  {
    name: 'Service Modules',
    href: route('admin.service-modules.index'),
    icon: RectangleStackIcon,
    current: route().current('admin.service-modules.*'),
    section: 'services',
  },

  // ADMIN TOOLS
  {
    name: 'Document Verification',
    href: route('admin.documents.verify.index'),
    icon: ShieldCheckIcon,
    current: route().current('admin.documents.verify.*'),
    section: 'tools',
  },
  {
    name: 'Notifications',
    href: route('admin.notifications.index'),
    icon: BellIcon,
    current: route().current('admin.notifications.*'),
    section: 'tools',
  },
  {
    name: 'Impersonation Logs',
    href: route('admin.impersonations.index'),
    icon: ClockIcon,
    current: route().current('admin.impersonations.*'),
    section: 'tools',
  },

  // ANALYTICS & REPORTS
  {
    name: 'Analytics',
    href: route('admin.analytics.index'),
    icon: ChartBarIcon,
    current: route().current('admin.analytics.*'),
    section: 'analytics',
  },

  // SETTINGS
  {
    name: 'General Settings',
    href: route('admin.settings.index'),
    icon: Cog6ToothIcon,
    current: route().current('admin.settings.index'),
    section: 'settings',
  },
  {
    name: 'SEO Settings',
    href: route('seo-settings.index'),
    icon: ChartBarIcon,
    current: route().current('seo-settings.*'),
    section: 'settings',
  },
]

// Navigation Sections Grouping - Clean Professional Structure
// Dashboard is rendered separately at the top, so exclude it from sections
const navigationSections = {
  '👥 PEOPLE': navigation.filter(item => item.section === 'users'),
  '💼 EDUCATION & JOBS': navigation.filter(item => item.section === 'jobs'),
  '✈️ VISA & TRAVEL': navigation.filter(item => item.section === 'travel'),
  '🏢 AGENCIES': navigation.filter(item => item.section === 'agencies'),
  '💰 FINANCIAL': navigation.filter(item => item.section === 'financial'),
  '📝 CONTENT': navigation.filter(item => item.section === 'content'),
  '🛠️ SERVICES': navigation.filter(item => item.section === 'services'),
  '🔧 TOOLS': navigation.filter(item => item.section === 'tools'),
  '📊 ANALYTICS': navigation.filter(item => item.section === 'analytics'),
  '⚙️ SETTINGS': navigation.filter(item => item.section === 'settings'),
}
</script>

<template>
  <div class="dark:bg-gray-900 min-h-screen">
    <!-- Impersonation Banner -->
    <ImpersonationBanner />

    <!-- Command Palette (Cmd+K) -->
    <Teleport to="body">
      <div
        v-if="showCommandPalette"
        class="fixed inset-0 z-[100] overflow-y-auto"
        @click="showCommandPalette = false"
      >
        <div class="flex min-h-screen items-start justify-center px-4 pt-20">
          <div
            class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"
            aria-hidden="true"
          ></div>

          <div
            class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black/5 transition-all"
            @click.stop
          >
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
              <div class="flex items-center gap-3">
                <CommandLineIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  Quick Navigation
                </h3>
                <kbd
                  class="ml-auto px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded"
                >
                  ESC
                </kbd>
              </div>
            </div>

            <!-- Search -->
            <div class="p-4">
              <GlobalSearch />
            </div>

            <!-- Quick Links -->
            <div class="px-4 pb-4">
              <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Quick Access
              </h4>
              <div class="grid grid-cols-2 gap-2">
                <Link
                  v-for="item in navigation.slice(0, 6)"
                  :key="item.name"
                  :href="item.href"
                  class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group"
                  @click="showCommandPalette = false"
                >
                  <component
                    :is="item.icon"
                    class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400"
                  />
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">
                    {{ item.name }}
                  </span>
                </Link>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-3 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
              <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                Press <kbd class="px-1.5 py-0.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded">⌘K</kbd> or
                <kbd class="px-1.5 py-0.5 text-xs font-semibold bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded">Ctrl+K</kbd> anytime to open
              </p>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50/30 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
      <!-- Sidebar -->
      <nav
        :class="[
          'fixed inset-y-0 left-0 z-50 hidden md:flex flex-col bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-r border-gray-200/50 dark:border-gray-700/50 shadow-2xl transition-all duration-300 ease-in-out',
          sidebarCollapsed ? 'w-20' : 'w-64',
        ]"
      >
        <!-- Logo Section - Stunning Gradient Header -->
        <!-- Logo Section - Stunning Gradient Header -->
        <div
          class="flex flex-shrink-0 items-center justify-between px-5 h-16 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-500 dark:via-purple-500 dark:to-pink-500"
        >
          <Link :href="route('admin.dashboard')" class="flex items-center gap-3">
            <div class="relative">
              <div class="absolute inset-0 bg-white/20 rounded-xl blur-md"></div>
              <div class="relative w-10 h-10 rounded-xl bg-white/90 dark:bg-white flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
                <SparklesIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-600" />
              </div>
            </div>
            <div v-if="!sidebarCollapsed" class="flex flex-col">
              <span class="font-bold text-white text-base tracking-tight">BideshGomon</span>
              <span class="text-xs text-white/80 font-medium">Admin Panel</span>
            </div>
          </Link>
          
          <!-- Collapse Toggle Button -->
          <button
            v-if="!sidebarCollapsed"
            @click="toggleSidebar"
            class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all"
            title="Collapse Sidebar"
          >
            <ChevronDownIcon class="w-4 h-4 rotate-90" />
          </button>
        </div>

        <!-- Expand Button (When Collapsed) -->
        <button
          v-if="sidebarCollapsed"
          @click="toggleSidebar"
          class="mx-auto mt-4 p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-all"
          title="Expand Sidebar"
        >
          <ChevronRightIcon class="w-5 h-5" />
        </button>

        <!-- Navigation Sections - Beautiful Scrollable Area -->
        <div
          class="flex flex-1 flex-col overflow-y-auto px-3 py-4 custom-scrollbar"
        >
          <ul role="list" class="flex flex-1 flex-col gap-y-2">
            <!-- Dashboard - Standalone at Top -->
            <li class="mb-3">
              <Link
                :href="route('admin.dashboard')"
                :class="[
                  route().current('admin.dashboard')
                    ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/50 dark:shadow-indigo-500/30 scale-105'
                    : 'text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gradient-to-r hover:from-white hover:to-indigo-50/50 dark:hover:from-gray-800 dark:hover:to-indigo-900/20 hover:shadow-md hover:scale-[1.02]',
                  sidebarCollapsed ? 'justify-center px-3' : 'pl-3 pr-3',
                  'group flex items-center gap-x-3 rounded-xl py-3 text-sm font-semibold transition-all duration-200',
                ]"
                :title="sidebarCollapsed ? 'Dashboard' : ''"
              >
                <HomeIcon
                  :class="[
                    'h-5 w-5 shrink-0 transition-all duration-200',
                    'group-hover:scale-110 group-hover:rotate-3',
                    route().current('admin.dashboard') ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400',
                  ]"
                />
                <span v-if="!sidebarCollapsed" class="flex-1">Dashboard</span>
                <span
                  v-if="route().current('admin.dashboard') && !sidebarCollapsed"
                  class="w-2 h-2 bg-white rounded-full animate-pulse"
                ></span>
              </Link>
            </li>

            <!-- Divider -->
            <li class="mb-2">
              <div class="h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></div>
            </li>

            <!-- Other Sections -->
            <li v-for="(items, sectionName) in navigationSections" :key="sectionName">
              <div v-if="items.length > 0" class="mb-3">
                <!-- Collapsible Section Header - Ultra Modern -->
                <button
                  v-if="!sidebarCollapsed"
                  class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 rounded-xl transition-all backdrop-blur-sm group"
                  @click="toggleSection(sectionName)"
                >
                  <span class="uppercase tracking-wider group-hover:tracking-wide transition-all">{{ sectionName }}</span>
                  <ChevronDownIcon
                    :class="[
                      'w-4 h-4 transition-all duration-300 text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400',
                      collapsedSections[sectionName] ? '-rotate-90' : 'rotate-0',
                    ]"
                  />
                </button>

                <!-- Divider for Collapsed View -->
                <div v-if="sidebarCollapsed" class="h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent my-2"></div>

                <!-- Section Items - Stunning Cards with Animations -->
                <ul v-show="!collapsedSections[sectionName]" role="list" class="space-y-1 mt-2">
                  <li v-for="item in items" :key="item.name">
                    <Link
                      :href="item.disabled ? '#' : item.href"
                      :class="[
                        item.current
                          ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/50 dark:shadow-indigo-500/30 scale-105'
                          : 'text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gradient-to-r hover:from-white hover:to-indigo-50/50 dark:hover:from-gray-800 dark:hover:to-indigo-900/20 hover:shadow-md hover:scale-[1.02]',
                        sidebarCollapsed ? 'justify-center px-3' : 'pl-3 pr-3',
                        'group flex items-center gap-x-3 rounded-xl py-3 text-sm font-medium transition-all duration-200',
                        item.disabled ? 'opacity-50 cursor-not-allowed' : '',
                      ]"
                      :aria-disabled="item.disabled"
                      :tabindex="item.disabled ? -1 : undefined"
                      :title="sidebarCollapsed ? item.name : ''"
                    >
                      <component
                        :is="item.icon"
                        :class="[
                          'h-5 w-5 shrink-0 transition-all duration-200',
                          !item.disabled && 'group-hover:scale-110 group-hover:rotate-3',
                          item.current ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400',
                        ]"
                        aria-hidden="true"
                      />
                      <span v-if="!sidebarCollapsed" class="flex-1">{{ item.name }}</span>
                      <!-- Active Indicator Dot -->
                      <span
                        v-if="item.current && !sidebarCollapsed"
                        class="w-2 h-2 bg-white rounded-full animate-pulse"
                      ></span>
                      <!-- Badge Count (if any) -->
                      <span
                        v-if="item.badge && !sidebarCollapsed"
                        class="px-2 py-0.5 text-xs font-bold bg-white/20 text-white rounded-full"
                      >
                        {{ item.badge }}
                      </span>
                    </Link>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>

        <!-- Footer - Beautiful Gradient with System Status -->
        <div class="flex-shrink-0 border-t border-gray-200/50 dark:border-gray-700/50 p-4 bg-gradient-to-br from-gray-50/90 via-blue-50/50 to-indigo-50/30 dark:from-gray-800/90 dark:via-indigo-900/20 dark:to-purple-900/10 backdrop-blur-md">
          <div v-if="!sidebarCollapsed" class="space-y-3">
            <!-- Logout Button -->
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:shadow-md transition-all group"
            >
              <ArrowRightOnRectangleIcon class="w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-semibold">Logout</span>
            </Link>
            
            <!-- System Status -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="relative">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                  <div class="absolute inset-0 w-2 h-2 bg-emerald-500 rounded-full animate-ping opacity-75"></div>
                </div>
                <p class="text-xs font-semibold text-gray-900 dark:text-white">All Systems Operational</p>
              </div>
            </div>
            
            <!-- Version & Dark Mode Toggle -->
            <div class="flex items-center justify-between">
              <p class="text-xs text-gray-500 dark:text-gray-400">v1.0.0 Beta</p>
              <button
                @click="toggleDarkMode"
                class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-white/50 dark:hover:bg-gray-700/50 rounded-lg transition-all transform hover:scale-110"
                :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
              >
                <SunIcon v-if="darkMode" class="w-4 h-4" />
                <MoonIcon v-else class="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <!-- Collapsed View - Icons Only -->
          <div v-else class="flex flex-col items-center gap-3">
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="p-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
              title="Logout"
            >
              <ArrowRightOnRectangleIcon class="w-5 h-5" />
            </Link>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <button
              @click="toggleDarkMode"
              class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-white/50 dark:hover:bg-gray-700/50 rounded-lg transition-all"
              :title="darkMode ? 'Light Mode' : 'Dark Mode'"
            >
              <SunIcon v-if="darkMode" class="w-5 h-5" />
              <MoonIcon v-else class="w-5 h-5" />
            </button>
          </div>
        </div>
      </nav>

      <!-- Main Content Area -->
      <div
        :class="[
          'flex flex-col flex-1 transition-all duration-300',
          sidebarCollapsed ? 'md:pl-20' : 'md:pl-64',
        ]"
      >
        <!-- Top Header Bar - Ultra Modern Glassmorphism -->
        <div
          class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200/50 dark:border-gray-700/50 bg-white/70 dark:bg-gray-900/70 backdrop-blur-2xl px-4 lg:px-8 shadow-lg"
        >
          <!-- Mobile Menu Button -->
          <button
            type="button"
            class="p-2.5 flex items-center justify-center rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all transform hover:scale-105 md:hidden shadow-sm"
            @click="showingNavigationDropdown = !showingNavigationDropdown"
          >
            <span class="sr-only">Open sidebar</span>
            <Bars3Icon class="h-6 w-6" aria-hidden="true" />
          </button>

          <!-- Page Title with Beautiful Breadcrumb -->
          <div class="flex items-center gap-3 min-w-0">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/20 rounded-lg">
              <HomeIcon class="w-4 h-4 text-indigo-600 dark:text-indigo-400 flex-shrink-0" />
              <span class="text-gray-300 dark:text-gray-600 select-none">/</span>
              <h1 class="text-sm font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent truncate">
                {{ $page.props.title || 'Dashboard' }}
              </h1>
            </div>
          </div>

          <!-- Command Palette Hint -->
          <button
            @click="showCommandPalette = true"
            class="hidden lg:flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl transition-all group"
          >
            <CommandLineIcon class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
            <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">Quick search...</span>
            <kbd class="hidden xl:inline-block px-2 py-0.5 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded">
              ⌘K
            </kbd>
          </button>

          <!-- Spacer -->
          <div class="flex-1"></div>

          <!-- Right Actions - Stunning Icons -->
          <div class="flex items-center gap-2">
            <!-- Mobile Search Icon -->
            <button
              class="lg:hidden p-2.5 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl transition-all transform hover:scale-105"
              title="Search"
              @click="showMobileSearch = !showMobileSearch"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </button>

            <!-- Notification Bell -->
            <div class="relative">
              <NotificationBell />
            </div>

            <!-- User Menu - Premium Design -->
            <Dropdown align="right" width="64">
              <template #trigger>
                <button
                  type="button"
                  class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all group"
                >
                  <div class="relative">
                    <div
                      class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center text-white font-bold text-sm shadow-lg transform group-hover:scale-105 transition-transform"
                    >
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                  </div>
                  <div class="hidden lg:flex flex-col items-start min-w-0">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[140px]">
                      {{ user.name }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ user.role?.name || 'Admin' }}</span>
                  </div>
                  <ChevronDownIcon
                    class="hidden lg:block h-4 w-4 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-all"
                  />
                </button>
              </template>

              <template #content>
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-indigo-50/50 to-purple-50/30 dark:from-indigo-900/20 dark:to-purple-900/10">
                  <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                    {{ user.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                    {{ user.email }}
                  </p>
                  <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                    {{ user.role?.name || 'Administrator' }}
                  </div>
                </div>
                <div class="py-1">
                  <DropdownLink
                    :href="route(profileRoute)"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors"
                  >
                    <UsersIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <div class="flex flex-col">
                      <span class="font-medium">My Profile</span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">View and edit your profile</span>
                    </div>
                  </DropdownLink>
                  <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                  <DropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 w-full transition-colors"
                  >
                    <XMarkIcon class="w-5 h-5" />
                    <div class="flex flex-col items-start">
                      <span class="font-medium">Log Out</span>
                      <span class="text-xs text-gray-500 dark:text-gray-400">Sign out of your account</span>
                    </div>
                  </DropdownLink>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <!-- Mobile Search Modal - Enhanced Design -->
        <div
          v-if="showMobileSearch"
          class="fixed inset-0 z-50 bg-gray-900/80 backdrop-blur-sm lg:hidden"
          @click="showMobileSearch = false"
        >
          <div
            class="fixed inset-x-0 top-0 bg-white p-4 sm:p-6 shadow-2xl border-b-2 border-blue-500"
            @click.stop
          >
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div
                  class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-md"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Search Admin Panel</h3>
              </div>
              <button
                class="p-2 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-all"
                title="Close"
                @click="showMobileSearch = false"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <!-- Search Component -->
            <GlobalSearch />
          </div>
        </div>

        <header v-if="$slots.header" class="bg-white shadow">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header"></slot>
          </div>
        </header>

        <main class="flex-1">
          <slot></slot>
        </main>
      </div>

      <!-- Mobile Sidebar -->
      <div
        v-if="showingNavigationDropdown"
        class="relative z-50 md:hidden"
        role="dialog"
        aria-modal="true"
      >
        <div
          class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity duration-300"
          :class="showingNavigationDropdown ? 'opacity-100' : 'opacity-0'"
          @click="showingNavigationDropdown = false"
        ></div>

        <div class="fixed inset-0 flex">
          <div
            class="relative mr-16 flex w-full max-w-xs flex-1 transform transition ease-in-out duration-300"
            :class="showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full'"
            @click.stop
          >
            <!-- Close Button -->
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
              <button
                type="button"
                class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-brand-red-600 hover:bg-brand-red-700 transition-all duration-150 active:scale-95 shadow-lg"
                @click="showingNavigationDropdown = false"
              >
                <span class="sr-only">Close sidebar</span>
                <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
              </button>
            </div>

            <!-- Mobile Menu Content -->
            <div class="flex grow flex-col gap-y-3 overflow-y-auto bg-white pb-4">
              <!-- Mobile Logo -->
              <div
                class="flex h-16 shrink-0 items-center justify-center bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-4"
              >
                <Link :href="route('admin.dashboard')">
                  <ApplicationLogo class="block h-10 w-auto brightness-0 invert" />
                </Link>
              </div>

              <!-- Mobile Navigation -->
              <nav class="flex flex-1 flex-col px-2">
                <ul role="list" class="flex flex-1 flex-col gap-y-1">
                  <li v-for="(items, sectionName) in navigationSections" :key="sectionName">
                    <div v-if="items.length > 0" class="mb-2">
                      <!-- Section Header -->
                      <button
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        @click="toggleSection(sectionName)"
                      >
                        <span class="uppercase tracking-wide">{{ sectionName }}</span>
                        <ChevronDownIcon
                          :class="[
                            'w-4 h-4 transition-transform duration-200',
                            collapsedSections[sectionName] ? '-rotate-90' : '',
                          ]"
                        />
                      </button>

                      <!-- Section Items -->
                      <ul
                        v-show="!collapsedSections[sectionName]"
                        role="list"
                        class="space-y-1 mt-1"
                      >
                        <li v-for="item in items" :key="item.name">
                          <Link
                            :href="item.disabled ? '#' : item.href"
                            :class="[
                              item.current
                                ? 'bg-gradient-to-r from-brand-red-50 to-brand-red-100 text-brand-red-700 border-l-4 border-brand-red-600'
                                : 'text-gray-700 hover:text-brand-red-600 hover:bg-gray-50 border-l-4 border-transparent',
                              'group flex items-center px-3 py-2.5 text-sm font-medium rounded-r-lg transition-all duration-150',
                              item.disabled ? 'opacity-50 cursor-not-allowed' : '',
                            ]"
                            :aria-disabled="item.disabled"
                            :tabindex="item.disabled ? -1 : undefined"
                            @click="!item.disabled && (showingNavigationDropdown = false)"
                          >
                            <component
                              :is="item.icon"
                              :class="[
                                'h-6 w-6 shrink-0',
                                item.current ? 'text-brand-red-600' : '',
                              ]"
                              aria-hidden="true"
                            />
                            <span class="flex-1">{{ item.name }}</span>
                            <ChevronRightIcon
                              v-if="item.current && !item.disabled"
                              class="w-4 h-4 text-brand-red-600"
                            />
                          </Link>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </nav>

              <!-- Mobile Footer -->
              <div class="flex-shrink-0 border-t border-gray-200 p-3 bg-gray-50">
                <div class="text-xs text-gray-500 text-center">
                  <p class="font-semibold">BideshGomon Admin</p>
                  <p class="mt-1">v1.0.0</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom Scrollbar - Modern & Beautiful */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #818cf8 0%, #a855f7 100%);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #6366f1 0%, #9333ea 100%);
}

/* Dark mode scrollbar */
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #4f46e5 0%, #7c3aed 100%);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #6366f1 0%, #9333ea 100%);
}

/* Smooth Transitions */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.sidebar-item {
  animation: slideIn 0.3s ease-out;
}
</style>
