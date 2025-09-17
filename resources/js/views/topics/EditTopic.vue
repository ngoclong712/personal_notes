<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Edit Topic</h1>
            <RouterLink :to="{ name: 'topic' }" class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100">Back</RouterLink>
        </div>

        <form class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                <input v-model="form.name" type="text" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <input v-model="form.description" type="text" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Save</button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import axios from 'axios'
import { useToast } from '@/lib/toast'

const route = useRoute()
const router = useRouter()
const id = route.params.id as string
const { success } = useToast()

const form = reactive({
    name: '',
    description: '' as string | null,
})

onMounted(async () => {
    const res = await axios.get(`/api/topics/${id}`)
    const current: { id: number; name: string; description: string | null } = res.data.data
    form.name = current.name
    form.description = current.description
})

async function submit() {
    await axios.put(`/api/topics/${id}`, form)
    success('Topic updated')
    router.push({ name: 'topic.detail', params: { id } })
}
</script>


