<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import { Clock, Flag, ListChecks, CalendarDays, FileText, Tag } from 'lucide-vue-next'

const route = useRoute()
const loading = ref(true)

type Topic = {
    id: number,
    name: string,
}

type Subtask = {
    id: number,
    deadline_id: number,
    title: string,
    content: string,
    due_date: Date,
    status: number,
}

type Deadline = {
    id: number,
    title: string,
    description: string,
    status: number,
    priority: number,
    topic_id: number,
    topic?: Topic,
    subtasks: Subtask[],
}

// Sample data
const deadline = ref<Deadline[]>([])

// ===== Helpers =====
const getPriorityClass = (priority: number) => {
    const classes: Record<number, string> = {
        1: 'bg-green-100 text-green-800',
        2: 'bg-yellow-100 text-yellow-800',
        3: 'bg-red-100 text-red-800',
    }
    return classes[priority] || classes[1]
}

const getPriorityText = (priority: number) => {
    const texts: Record<number, string> = {
        1: 'Low',
        2: 'Medium',
        3: 'High',
    }
    return texts[priority] || texts[1]
}

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        1: 'bg-blue-100 text-blue-800',
        2: 'bg-green-100 text-green-800',
        3: 'bg-gray-100 text-gray-800',
        4: 'bg-red-100 text-red-800',
    }
    return classes[status] || classes[1]
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: 'In Progress',
        2: 'Completed',
        3: 'Cancelled',
        4: 'Overdue',
    }
    return texts[status] || texts[1]
}

// ===== Progress Calculation =====
const progress = computed(() => {
    if (!deadline.value || !deadline.value.subtasks) return 0
    const validSubtasks = deadline.value.subtasks.filter((s: any) => s.status !== 3) // exclude cancelled
    const total = validSubtasks.length
    if (total === 0) return 0
    const done = validSubtasks.filter((s: any) => s.status === 2).length
    return Math.round((done / total) * 100)
})

// ===== Fetch API =====
onMounted(async () => {
    try {
        const id = route.params.id
        const res = await axios.get(`/api/deadlines/${id}`)
        deadline.value = res.data.data
    } catch (err) {
        console.error('Error while fetching deadline data:', err)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="max-w-4xl mx-auto py-8">
        <div v-if="loading" class="text-center text-gray-500">Loading data...</div>

        <div
            v-else-if="deadline"
            class="bg-white shadow-lg rounded-xl p-6 space-y-6 border border-gray-200"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-800">{{ deadline.title }}</h2>
                <div class="flex space-x-2">
                    <span
                        class="px-3 py-1 text-sm font-medium rounded-full"
                        :class="getPriorityClass(deadline.priority)"
                    >
                        <Flag class="inline w-4 h-4 mr-1" /> {{ getPriorityText(deadline.priority) }}
                    </span>
                    <span
                        class="px-3 py-1 text-sm font-medium rounded-full"
                        :class="getStatusClass(deadline.status)"
                    >
                        <Clock class="inline w-4 h-4 mr-1" /> {{ getStatusText(deadline.status) }}
                    </span>
                </div>
            </div>

            <!-- General Info -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div class="flex items-center space-x-2">
                    <Tag class="w-4 h-4 text-gray-500" />
                    <span>Topic: <b>{{ deadline.topic?.name || '—' }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <CalendarDays class="w-4 h-4 text-gray-500" />
                    <span>Nearest Due Date: <b>{{ deadline.subtasks_min_due_date || '—' }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <ListChecks class="w-4 h-4 text-gray-500" />
                    <span>Progress: <b>{{ progress }}%</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <Clock class="w-4 h-4 text-gray-500" />
                    <span>Created: <b>{{ deadline.created_at?.slice(0, 10) }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <Clock class="w-4 h-4 text-gray-500" />
                    <span>Updated: <b>{{ deadline.updated_at?.slice(0, 10) }}</b></span>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex items-center space-x-2 mb-2 text-gray-700 font-medium">
                    <FileText class="w-4 h-4 text-gray-500" /> <span>Description</span>
                </div>
                <p class="text-gray-700 whitespace-pre-line">{{ deadline.description || 'No description available' }}</p>
            </div>

            <!-- Progress Bar -->
            <div v-if="deadline.status !== 3" class="space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Completion Progress</span>
                    <span>{{ progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div
                        class="h-3 transition-all duration-500"
                        :class="{
                            'bg-blue-500': deadline.status === 1,
                            'bg-green-500': deadline.status === 2,
                            'bg-red-500': deadline.status === 4,
                        }"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>
            </div>
            <div v-else class="text-gray-500 italic text-sm">
                This task has been cancelled — no progress available.
            </div>

            <!-- Subtasks -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-800">Subtasks</h3>
                <ul class="space-y-2">
                    <li
                        v-for="subtask in deadline.subtasks"
                        :key="subtask.id"
                        class="bg-gray-50 p-3 rounded-md border border-gray-200"
                    >
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-800">{{ subtask.title }}</span>
                            <span
                                class="px-3 py-1 text-xs rounded-full"
                                :class="getStatusClass(subtask.status)"
                            >
                                {{ getStatusText(subtask.status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ subtask.content || '—' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Due: {{ subtask.due_date || '—' }}</p>
                    </li>
                </ul>
            </div>
        </div>

        <div v-else class="text-center text-gray-500">No deadline found.</div>
    </div>
</template>
