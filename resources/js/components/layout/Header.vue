<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'

// Dark mode toggle
const darkMode = ref(false)

onMounted(() => {
    // Nếu có dark mode từ trước (reload trang)
    darkMode.value = document.documentElement.classList.contains('dark')
})

function toggleDarkMode() {
    darkMode.value = !darkMode.value
    console.log('Dark mode clicked! New value =', darkMode.value)

    if (darkMode.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

// Fake notifications count
const notifications = ref(3)

// Language switch
const language = ref('EN')
function toggleLanguage() {
    language.value = language.value === 'EN' ? 'VN' : 'EN'
}
</script>

<template>
    <header class="bg-sky-200 text-slate-800 px-6 py-3 flex items-center justify-between">
        <!-- Left: Logo / Home -->
        <RouterLink to="/" class="text-lg font-bold hover:text-gray-300">
            Personal Notes
        </RouterLink>

        <!-- Right: Action buttons -->
        <div class="flex items-center space-x-4">
            <!-- Language -->
            <button @click="toggleLanguage" class="px-2 py-1 rounded hover:bg-gray-700">
                {{ language }}
            </button>

            <!-- Notifications -->
            <button class="relative px-2 py-1 rounded hover:bg-gray-700">
                🔔
                <span
                    v-if="notifications > 0"
                    class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full w-5 h-5 flex items-center justify-center"
                >
          {{ notifications }}
        </span>
            </button>

            <!-- Dark mode -->
            <button @click="toggleDarkMode" class="px-2 py-1 rounded hover:bg-gray-700">
                <span v-if="!darkMode">🌙</span>
                <span v-else>☀️</span>
            </button>
        </div>
    </header>
</template>
