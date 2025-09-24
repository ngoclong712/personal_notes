<template>
    <div class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col">
        <Header />

        <main class="flex-1 px-4 py-4">
            <div class="max-w-6xl mx-auto">
                <router-view />
            </div>
        </main>

        <Footer />

        <!-- Toasts -->
        <ToastHost />
    </div>
</template>

<script setup lang="ts">
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import ToastHost from '@/components/ToastHost.vue'
</script>


