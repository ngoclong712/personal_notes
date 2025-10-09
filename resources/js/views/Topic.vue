<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-4 border border-gray-200 rounded-lg p-3">
            <h1 class="text-xl font-semibold">Topics</h1>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Tìm theo tên..."
                    class="w-full md:w-64 px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                />
                <button @click="clearSearch" class="px-3 py-2 rounded border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">Xóa</button>
            </div>
            <div>
                <RouterLink
                    :to="{ name: 'topic.add' }"
                    class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white mr-1"
                >Add
                </RouterLink>
                <button
                    type="button"
                    @click="openImportModal"
                    class="px-3 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white"
                >Import CSV</button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <button class="inline-flex items-center gap-1" @click="toggleSort('id')">
                                <span>ID</span>
                                <span v-if="sortBy === 'id'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <button class="inline-flex items-center gap-1" @click="toggleSort('name')">
                                <span>Name</span>
                                <span v-if="sortBy === 'name'">{{ sortDir === 'asc' ? '▲' : '▼' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created At</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <tr v-for="topic in displayTopics" :key="topic.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ topic.id }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">
                            <RouterLink :to="{ name: 'topic.detail', params: { id: topic.id } }" class="hover:underline">{{ topic.name }}</RouterLink>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ truncate(topic.description, 35) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ formatDate(topic.created_at) }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="inline-flex gap-2">
                                <RouterLink :to="{ name: 'topic.edit', params: { id: topic.id } }" class="px-2 py-1 rounded bg-green-600 hover:bg-green-700 text-white">Edit</RouterLink>
                                <button @click="onDelete(topic.id)" class="px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white hover:cursor-pointer">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="topics.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No topics found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="closeImportModal"></div>
            <div class="relative w-full max-w-lg mx-4 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Topics</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400" @click="closeImportModal">✕</button>
                </div>
                <div class="p-4 space-y-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Chọn file CSV/XLSX với cột: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">name</code>, <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">description</code>
                    </div>
                    <div class=" justify-between flex items-center gap-2">
                        <button type="button" class="px-3 py-2 rounded-md bg-amber-500 hover:bg-amber-600 text-white" @click="downloadSampleCsv">
                            Xem mẫu CSV
                        </button>
                        <span v-if="selectedFileName" class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ selectedFileName }}</span>
                        <input
                            ref="fileInputRef"
                            type="file"
                            class="hidden"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            @change="onFileChange"
                        />
                        <button type="button" class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700" @click="triggerFilePicker">
                            Chọn file
                        </button>
                    </div>
                    <div v-if="importResult" class="text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 text-gray-800 dark:text-gray-100">
                        <div class="mb-1">Kết quả:</div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            <li>Tạo mới: {{ importResult.created }}</li>
                            <li>Cập nhật: {{ importResult.updated }}</li>
                            <li>Bỏ qua: {{ importResult.skipped }}</li>
                        </ul>
                        <div v-if="importResult.errors?.length" class="mt-2">
                            <div class="font-medium">Lỗi:</div>
                            <ul class="list-disc pl-5 space-y-0.5">
                                <li v-for="(err, idx) in importResult.errors" :key="idx">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-800">
                    <button type="button" class="px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800" @click="closeImportModal">Hủy</button>
                    <button type="button" class="px-3 py-2 rounded-md bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white" :disabled="!selectedFile || importing" @click="doImport">
                        <span v-if="importing">Đang import...</span>
                        <span v-else>Import</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
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

// Search & sort state
const search = ref('')
const sortBy = ref<'id' | 'name'>('id')
const sortDir = ref<'asc' | 'desc'>('asc')

// Client-side filtered & sorted data for instant UX
const filteredTopics = computed(() => {
    const q = search.value.trim().toLowerCase()
    if (!q) return topics.value
    return topics.value.filter(t => (t.name || '').toLowerCase().includes(q))
})

const displayTopics = computed(() => {
    const list = [...filteredTopics.value]
    list.sort((a, b) => {
        if (sortBy.value === 'id') {
            return sortDir.value === 'asc' ? a.id - b.id : b.id - a.id
        }
        const an = (a.name || '').toLowerCase()
        const bn = (b.name || '').toLowerCase()
        if (an === bn) return 0
        if (sortDir.value === 'asc') return an > bn ? 1 : -1
        return an < bn ? 1 : -1
    })
    return list
})

// Import modal state
const showImportModal = ref(false)
const importing = ref(false)
const selectedFile = ref<File | null>(null)
const selectedFileName = ref('')
const fileInputRef = ref<HTMLInputElement | null>(null)
const importResult = ref<{ created: number; updated: number; skipped: number; errors?: string[] } | null>(null)

onMounted(async () => {
    await reloadTopics()
})

function openImportModal() {
    showImportModal.value = true
    importResult.value = null
}

function closeImportModal() {
    showImportModal.value = false
    selectedFile.value = null
    selectedFileName.value = ''
    if (fileInputRef.value) fileInputRef.value.value = ''
}

function triggerFilePicker() {
    fileInputRef.value?.click()
}

function onFileChange(e: Event) {
    const input = e.target as HTMLInputElement
    const file = input.files && input.files[0] ? input.files[0] : null
    if (!file) {
        selectedFile.value = null
        selectedFileName.value = ''
        return
    }
    // Validate type by extension/MIME
    const allowed = [
        'text/csv',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ]
    if (!allowed.includes(file.type) && !/\.(csv|xlsx)$/i.test(file.name)) {
        alert('Vui lòng chọn file .csv hoặc .xlsx')
        input.value = ''
        return
    }
    selectedFile.value = file
    selectedFileName.value = file.name
}

function downloadSampleCsv() {
    const content = 'name,description\nTopic A,Description A\nTopic B,Description B\n'
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'topics_sample.csv'
    a.click()
    URL.revokeObjectURL(url)
}

async function doImport() {
    if (!selectedFile.value) return
    importing.value = true
    importResult.value = null
    try {
        const form = new FormData()
        form.append('file', selectedFile.value)
        const res = await axios.post('/api/topics/import', form, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        importResult.value = res.data?.data || null
        await reloadTopics()
        success('Import thành công')
    } catch (err: any) {
        console.error(err)
        alert(err?.response?.data?.message || 'Import thất bại')
    } finally {
        importing.value = false
    }
}

function toggleSort(column: 'id' | 'name') {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDir.value = 'asc'
    }
}

function applySearch() {
    // instant via computed
}

function clearSearch() {
    if (!search.value) return
    search.value = ''
}

async function reloadTopics() {
    loading.value = true
    const res = await axios.get('/api/topics')
    topics.value = Array.isArray(res.data?.data) ? res.data.data : []
    loading.value = false
}

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
    if (isNaN(d.getTime())) return value

    const day = d.getDate().toString().padStart(2, '0')
    const month = (d.getMonth() + 1).toString().padStart(2, '0')
    const year = d.getFullYear()

    let hours = d.getHours()
    const minutes = d.getMinutes().toString().padStart(2, '0')
    const seconds = d.getSeconds().toString().padStart(2, '0')

    const ampm = hours >= 12 ? 'PM' : 'AM'
    hours = hours % 12
    hours = hours ? hours : 12 // 0 -> 12

    return `${day}/${month}/${year}, ${hours}:${minutes}:${seconds} ${ampm}`
}

</script>
