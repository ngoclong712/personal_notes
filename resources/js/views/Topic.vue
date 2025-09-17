<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Topics</h1>
            <RouterLink
                :to="{ name: 'topic.add' }"
                class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white"
            >Add</RouterLink>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created At</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <tr v-for="topic in topics" :key="topic.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ topic.id }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">
                            <RouterLink :to="{ name: 'topic.detail', params: { id: topic.id } }" class="hover:underline">{{ topic.name }}</RouterLink>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ truncate(topic.description, 35) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ formatDate(topic.created_at) }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="inline-flex gap-2">
                                <RouterLink :to="{ name: 'topic.edit', params: { id: topic.id } }" class="px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white">Edit</RouterLink>
                                <button @click="onDelete(topic.id)" class="px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="topics.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No topics found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useToast } from '@/lib/toast'

type Topic = {
    id: number
    name: string
    description: string | null
    created_at: string
}

const topics = ref<Topic[]>([])
const loading = ref(false)
const { success } = useToast()

onMounted(async () => {
    loading.value = true
    try {
        const res = await axios.get('/api/topics')
        console.log('API data:', res.data)
        topics.value = Array.isArray(res.data?.data) ? res.data.data : []
    } catch (err) {
        console.error(err)
    } finally {
        loading.value = false
    }
})

async function onDelete(id: number) {
    try {
        await axios.delete(`/api/topics/${id}`)
        topics.value = topics.value.filter(t => t.id !== id)
        success('Topic deleted')
    } catch (err) {
        console.error(err)
    }
}

function truncate(text: string | null, maxLength = 80): string {
    if (!text) return ''
    return text.length > maxLength ? text.slice(0, maxLength - 1) + '…' : text
}

function formatDate(value: string): string {
    const d = new Date(value)
    return isNaN(d.getTime()) ? value : d.toLocaleString()
}
</script>
