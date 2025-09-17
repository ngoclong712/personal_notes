<template>
    <div class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col">
        <header class="border-b border-gray-200 dark:border-gray-800 px-4 py-3">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <h1 class="text-lg font-semibold">Personal Notes</h1>
                <slot name="header-actions" />
            </div>
        </header>

        <main class="flex-1 px-4 py-4">
            <div class="max-w-6xl mx-auto">
                <router-view />
            </div>
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-800 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
            <div class="max-w-6xl mx-auto">© {{ new Date().getFullYear() }} Personal Notes</div>
        </footer>
    </div>
</template>

<script setup lang="ts">
</script>


