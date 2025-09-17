<template>
    <div class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Header -->
        <Header />

        <!-- Body -->
        <div class="flex flex-1">
            <Sidebar />
            <main class="flex-1 p-6">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm p-4">
                    <RouterView />
                </div>
            </main>
        </div>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script setup>
import Header from '@/components/layout/Header.vue'
import Sidebar from '@/components/layout/Sidebar.vue'
import Footer from '@/components/layout/Footer.vue'
</script>
