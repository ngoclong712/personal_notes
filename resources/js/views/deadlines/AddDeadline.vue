<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Trash2, X, CalendarCheck } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { useToast } from '@/lib/toast'

const router = useRouter()
const { success, error } = useToast()

// ===== Data =====
const form = ref({
    title: '',
    description: '',
    priority: 1,
    topic: null as any,
    topic_id: null as number | null,
    subtasks: [] as { title: string; content: string; due_date: string; status: number }[],
})

const topics = ref([])

const priorities = [
    { id: 1, text: 'Low' },
    { id: 2, text: 'Medium' },
    { id: 3, text: 'High' },
]

const loading = ref(false)
const showModal = ref(false)

const openModal = () => {
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    newSubtask.value = { title: '', content: '', due_date: '', status: 1 }
}

const newSubtask = ref({
    title: '',
    content: '',
    due_date: '',
    status: 1,
})

const addSubtask = () => {
    if (!newSubtask.value.title.trim()) return
    form.value.subtasks.push({ ...newSubtask.value })
    closeModal()
}

const removeSubtask = (index: number) => {
    form.value.subtasks.splice(index, 1)
}

const submit = async () => {
    loading.value = true
    try {
        form.value.topic_id = form.value.topic?.id || null
        await axios.post('/api/deadlines', form.value)
        success('Deadline created successfully!')
        setTimeout(() => router.push('/deadline'), 1000)
    } catch (err) {
        console.error(err)
        error('Failed to create deadline.')
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    try {
        const res = await axios.get('/api/topics')
        topics.value = res.data.data
    } catch (err) {
        console.error('Failed to fetch topics.')
    }
})

const formatDate = (dateString: string) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return dateString
    return date.toLocaleDateString('en-GB')
}
</script>

<template>
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-6 space-y-6 mt-8 relative">
        <div class="flex items-center w-full">
            <CalendarCheck class="w-7 h-7 mr-2" />
            <h2 class="text-2xl font-semibold text-gray-800">Add New Deadline</h2>
        </div>

        <!-- Deadline Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Enter deadline title"
                    class="w-full border rounded-md px-3 py-2"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select
                    v-model="form.priority"
                    class="w-full border rounded-md px-3 py-2"
                >
                    <option v-for="p in priorities" :key="p.id" :value="p.id">
                        {{ p.text }}
                    </option>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                <Multiselect
                    v-model="form.topic"
                    :options="topics"
                    label="name"
                    track-by="id"
                    placeholder="-- Select Topic --"
                    class="w-full border rounded-md"
                />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Enter description for the deadline"
                    class="w-full border rounded-md px-3 py-2"
                ></textarea>
            </div>
        </div>

        <!-- Subtask header -->
        <div class="flex justify-between items-center mt-6">
            <h3 class="text-lg font-semibold text-gray-800">Subtasks</h3>
            <button
                type="button"
                class="flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg"
                @click="openModal"
            >
                <Plus class="w-4 h-4" /> Add Subtask
            </button>
        </div>

        <!-- Subtask list -->
        <div v-if="form.subtasks.length" class="space-y-3 mt-3">
            <div
                v-for="(subtask, index) in form.subtasks"
                :key="index"
                class="border p-4 rounded-lg bg-gray-50 relative"
            >
                <button
                    type="button"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                    @click="removeSubtask(index)"
                >
                    <Trash2 class="w-4 h-4" />
                </button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-medium text-gray-800">{{ subtask.title }}</p>
                        <p class="text-sm text-gray-600">{{ subtask.content }}</p>
                    </div>
                    <div class="text-sm text-gray-500 text-right mt-2">
                        Due: <span class="font-medium text-gray-700">{{ formatDate(subtask.due_date) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end items-center space-x-4 pt-4 border-t">
            <button
                type="button"
                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                :disabled="loading"
                @click="submit"
            >
                {{ loading ? 'Saving...' : 'Save Deadline' }}
            </button>
        </div>

        <!-- Modal: Add Subtask -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                <button
                    @click="closeModal"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
                >
                    <X class="w-5 h-5" />
                </button>

                <h3 class="text-lg font-semibold mb-4">Add Subtask</h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input
                            v-model="newSubtask.title"
                            type="text"
                            class="w-full border rounded-md px-3 py-2"
                            placeholder="Enter subtask title"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea
                            v-model="newSubtask.content"
                            rows="2"
                            class="w-full border rounded-md px-3 py-2"
                            placeholder="Enter subtask content"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input
                            v-model="newSubtask.due_date"
                            type="date"
                            class="w-full border rounded-md px-3 py-2"
                        />
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button
                        @click="closeModal"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100"
                    >
                        Cancel
                    </button>
                    <button
                        @click="addSubtask"
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
