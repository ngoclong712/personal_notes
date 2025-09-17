<template>
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-4 right-4 space-y-2 w-[min(92vw,380px)]">
            <div v-for="t in toasts" :key="t.id" class="pointer-events-auto flex items-start gap-3 p-3 rounded-md shadow-lg border text-sm"
                 :class="bgClass(t.type)">
                <div class="flex-1 whitespace-pre-wrap break-words">{{ t.message }}</div>
                <button class="text-xs opacity-70 hover:opacity-100" @click="remove(t.id)">✕</button>
            </div>
        </div>
    </div>
    
</template>

<script setup>
import { useToast } from '@/lib/toast'

const { toasts, remove } = useToast()

function bgClass(type) {
    switch (type) {
        case 'success':
            return 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-900 dark:text-green-100'
        case 'error':
            return 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-900 dark:text-red-100'
        case 'warn':
            return 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800 text-yellow-900 dark:text-yellow-100'
        default:
            return 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100'
    }
}
</script>

<style scoped>
</style>


