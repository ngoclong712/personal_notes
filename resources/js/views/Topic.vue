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
                <button @click="clearSearch"
                        class="px-3 py-2 rounded border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                    Xóa
                </button>
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
                >Import CSV
                </button>
            </div>
        </div>

        <!--Card-->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div
                v-if="displayTopics.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
            >
                <div
                    v-for="(topic, index) in displayTopics"
                    :key="topic.id"
                    :class="[
                        'rounded-xl p-5 text-white shadow-md hover:shadow-xl hover:scale-[1.02] transition transform duration-200 flex flex-col justify-between',
                        cardColors[index % cardColors.length]
                      ]"
                >
                    <!-- Header -->
                    <div>
                        <h2 class="text-lg font-bold mb-2">
                            <RouterLink
                                :to="{ name: 'topic.detail', params: { id: topic.id } }"
                                class="underline-offset-2 hover:underline"
                            >
                                {{ topic.name }}
                            </RouterLink>
                        </h2>
                        <p class="text-sm opacity-90 mb-3">
                            {{ truncate(topic.description, 35) }}
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="mt-auto pt-3 border-t border-white/30">
                        <div class="flex">
                            <CalendarDaysIcon class="w-3 h-3 mr-1 mt-0.5"></CalendarDaysIcon>
                            <p class="text-xs opacity-80 mb-2">
                                {{ formatDate(topic.created_at) }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium opacity-90">
                            ID: {{ topic.id }}
                          </span>
                            <div class="flex gap-2">
                                <RouterLink
                                    :to="{ name: 'topic.edit', params: { id: topic.id } }"
                                    class="px-2 py-1 text-xs rounded bg-white/20 hover:bg-white/30"
                                >
                                    Edit
                                </RouterLink>
                                <button
                                    @click="onDelete(topic.id)"
                                    class="px-2 py-1 text-xs rounded bg-white/20 hover:bg-white/30"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="text-center text-sm text-gray-500 dark:text-gray-400 py-8"
            >
                No topics found.
            </div>
        </div>


        <!-- Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="closeImportModal"></div>
            <div
                class="relative w-full max-w-lg mx-4 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Topics</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400" @click="closeImportModal">✕
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Chọn file CSV/XLSX với cột: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">name</code>,
                        <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">description</code>
                    </div>
                    <div class=" justify-between flex items-center gap-2">
                        <button type="button" class="px-3 py-2 rounded-md bg-amber-500 hover:bg-amber-600 text-white"
                                @click="downloadSampleCsv">
                            Xem mẫu CSV
                        </button>
                        <span v-if="selectedFileName"
                              class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ selectedFileName }}</span>
                        <input
                            ref="fileInputRef"
                            type="file"
                            class="hidden"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            @change="onFileChange"
                        />
                        <button type="button"
                                class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700"
                                @click="triggerFilePicker">
                            Chọn file
                        </button>
                    </div>
                    <div v-if="importResult"
                         class="text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 text-gray-800 dark:text-gray-100">
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
                <div
                    class="flex items-center justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-800">
                    <button type="button"
                            class="px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800"
                            @click="closeImportModal">Hủy
                    </button>
                    <button type="button"
                            class="px-3 py-2 rounded-md bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white"
                            :disabled="!selectedFile || importing" @click="doImport">
                        <span v-if="importing">Đang import...</span>
                        <span v-else>Import</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {onMounted, ref, computed} from 'vue'
import axios from 'axios'
import { CalendarDaysIcon } from 'lucide-vue-next';
import {useToast} from '@/lib/toast'

type Topic = {
    id: number
    name: string
    description: string | null
    created_at: string
}

const topics = ref<Topic[]>([])
const loading = ref(false)
const {success} = useToast()

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
    const blob = new Blob([content], {type: 'text/csv;charset=utf-8;'})
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
            headers: {'Content-Type': 'multipart/form-data'}
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

function formatDate(dateStr) {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

const cardColors = [
    'bg-gradient-to-br from-blue-500 to-indigo-600',
    'bg-gradient-to-br from-purple-500 to-pink-500',
    'bg-gradient-to-br from-green-500 to-emerald-600',
    'bg-gradient-to-br from-amber-400 to-orange-500',
    'bg-gradient-to-br from-teal-500 to-cyan-600',
    'bg-gradient-to-br from-rose-500 to-pink-600',
    'bg-gradient-to-br from-sky-500 to-blue-600',
]

</script>
