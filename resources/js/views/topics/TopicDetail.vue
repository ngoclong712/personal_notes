<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Topic Detail</h1>
            <div class="flex gap-2">
                <RouterLink :to="{ name: 'topic.edit', params: { id: topic?.id } }" class="px-3 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white">Edit</RouterLink>
                <RouterLink :to="{ name: 'topic' }" class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100">Back</RouterLink>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div v-if="loading" class="text-gray-500 dark:text-gray-400">Loading...</div>
            <div v-else-if="!topic" class="text-gray-500 dark:text-gray-400">Topic not found.</div>
            <div v-else class="space-y-2">
                <div><span class="font-medium">ID:</span> {{ topic.id }}</div>
                <div><span class="font-medium">Name:</span> {{ topic.name }}</div>
                <div><span class="font-medium">Description:</span> {{ topic.description || '—' }}</div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import axios from 'axios'

type Topic = { id: number; name: string; description: string | null }

const route = useRoute()
const loading = ref(true)
const topic = ref<Topic | null>(null)

onMounted(async () => {
    try {
        const id = route.params.id
        const res = await axios.get(`/api/topics`)
        const items: Topic[] = res.data
        topic.value = items.find(t => String(t.id) === String(id)) ?? null
    } finally {
        loading.value = false
    }
})
</script>


