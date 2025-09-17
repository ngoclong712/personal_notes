<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Bell, Sun, Moon, Languages, Home } from 'lucide-vue-next'

// Dark mode toggle with persistence
const darkMode = ref(false)

function applyDarkClass(isDark) {
    if (isDark) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

function toggleDarkMode() {
    darkMode.value = !darkMode.value
    applyDarkClass(darkMode.value)
    localStorage.setItem('theme', darkMode.value ? 'dark' : 'light')
}

// Language switch with dropdown and persistence
const language = ref('EN')
const showLanguage = ref(false)
const languageMenuRef = ref(null)
const availableLanguages = [
    { code: 'EN', label: 'English' },
    { code: 'VN', label: 'Tiếng Việt' }
]
function selectLanguage(code) {
    language.value = code
    localStorage.setItem('lang', code)
    showLanguage.value = false
}

// Notifications dropdown
const showNotifications = ref(false)
const notificationsMenuRef = ref(null)
const notificationItems = ref([
    { id: 1, text: 'Welcome to Personal Notes!', read: false },
    { id: 2, text: 'Your task "Math HW" is due tomorrow.', read: false },
    { id: 3, text: 'Backup completed successfully.', read: true }
])
const unreadCount = computed(() => notificationItems.value.filter(n => !n.read).length)
function markAllAsRead() {
    notificationItems.value = notificationItems.value.map(n => ({ ...n, read: true }))
}

// Global handlers: click-outside and Escape to close menus
function onDocumentClick(e) {
    const target = e.target
    if (showLanguage.value && languageMenuRef.value && !languageMenuRef.value.contains(target)) {
        showLanguage.value = false
    }
    if (showNotifications.value && notificationsMenuRef.value && !notificationsMenuRef.value.contains(target)) {
        showNotifications.value = false
    }
}
function onEscape(e) {
    if (e.key === 'Escape') {
        showLanguage.value = false
        showNotifications.value = false
    }
}

onMounted(() => {
    // Initialize theme from localStorage or system
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        darkMode.value = true
    } else {
        darkMode.value = false
    }
    applyDarkClass(darkMode.value)

    // Initialize language
    const savedLang = localStorage.getItem('lang')
    if (savedLang) language.value = savedLang

    document.addEventListener('click', onDocumentClick)
    document.addEventListener('keydown', onEscape)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick)
    document.removeEventListener('keydown', onEscape)
})
</script>

<template>
    <header class="bg-white text-gray-800 px-6 py-3 flex items-center justify-between border-b border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800">
        <!-- Left: Logo / Home -->
        <RouterLink to="/" class="flex items-center gap-2 text-lg font-semibold hover:opacity-80">
            <Home class="w-5 h-5" />
            <span>Personal Notes</span>
        </RouterLink>

        <!-- Right: Action buttons -->
        <div class="flex items-center space-x-2">
            <!-- Language -->
            <div class="relative" ref="languageMenuRef">
                <button @click="showLanguage = !showLanguage" class="px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors flex items-center gap-2">
                    <Languages class="w-4 h-4" />
                    <span>{{ language }}</span>
                </button>
                <div v-if="showLanguage" class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 z-20">
                    <ul class="py-1">
                        <li v-for="opt in availableLanguages" :key="opt.code">
                            <button @click="selectLanguage(opt.code)" class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" :class="{ 'font-semibold': opt.code === language }">
                                {{ opt.label }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Notifications -->
            <div class="relative" ref="notificationsMenuRef">
                <button @click="showNotifications = !showNotifications" class="relative px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <Bell class="w-5 h-5" />
                    <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ unreadCount }}</span>
                </button>
                <div v-if="showNotifications" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 z-20">
                    <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="font-semibold">Notifications</span>
                        <button class="text-sm text-sky-600 hover:underline" @click="markAllAsRead">Mark all as read</button>
                    </div>
                    <ul class="max-h-64 overflow-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="n in notificationItems" :key="n.id" class="px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50" :class="{ 'opacity-70': n.read }">
                            {{ n.text }}
                        </li>
                        <li v-if="notificationItems.length === 0" class="px-3 py-4 text-center text-sm text-gray-500">No notifications</li>
                    </ul>
                </div>
            </div>

            <!-- Dark mode -->
            <button @click="toggleDarkMode" class="px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                <Moon v-if="!darkMode" class="w-5 h-5" />
                <Sun v-else class="w-5 h-5" />
            </button>
        </div>
    </header>
    
</template>
