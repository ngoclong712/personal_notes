<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from '@/lib/toast'

// Data from API
const notes = ref([])
const loading = ref(false)
const { success } = useToast()

// Filters
const search = ref('')
const topicFilter = ref('')
const statusFilter = ref('')

// Derived topics list from fetched data
const topics = computed(() => Array.from(new Set(notes.value.map(n => n.topic))))

// Filtering
const filteredNotes = computed(() => {
    return notes.value.filter(n => {
        const matchesSearch = (n.title || '').toLowerCase().includes(search.value.toLowerCase())
        const matchesTopic = topicFilter.value ? n.topic === topicFilter.value : true
        const matchesStatus = statusFilter.value ? n.status === statusFilter.value : true
        return matchesSearch && matchesTopic && matchesStatus
    })
})

// Sorting
const sortKey = ref('updatedAt')
const sortDir = ref('desc')
const sortedNotes = computed(() => {
    const list = [...filteredNotes.value]
    list.sort((a, b) => {
        const av = a[sortKey.value]
        const bv = b[sortKey.value]
        if (av === bv) return 0
        if (sortDir.value === 'asc') return av > bv ? 1 : -1
        return av < bv ? 1 : -1
    })
    return list
})

function setSort(key) {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortDir.value = 'asc'
    }
}

// Fetch data
onMounted(async () => {
    await reloadNotes()
})

async function reloadNotes() {
    loading.value = true
    const res = await axios.get('/api/notes')
    const rows = Array.isArray(res.data?.data) ? res.data.data : []
    // Normalize for UI: topic as string, updatedAt key (date only)
    notes.value = rows.map(r => ({
        id: r.id,
        title: r.title,
        topic: r.topic?.name ?? 'Null',
        status: r.status, // numeric enum
        updatedAt: r.updated_at ? new Date(r.updated_at).toISOString().slice(0, 10) : ''
    }))
    loading.value = false
}
// Import modal logic for notes (optional backend support)


const showImportModal = ref(false)
const importing = ref(false)
const selectedFile = ref(null)
const selectedFileName = ref('')
const fileInputRef = ref(null)
const importResult = ref(null)

function closeImportModal() {
    showImportModal.value = false
    selectedFile.value = null
    selectedFileName.value = ''
    if (fileInputRef.value) fileInputRef.value.value = ''
}

function triggerFilePicker() {
    fileInputRef.value?.click()
}

function onFileChange(e) {
    const input = e.target
    const file = input.files && input.files[0] ? input.files[0] : null
    if (!file) {
        selectedFile.value = null
        selectedFileName.value = ''
        return
    }
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
    const content = 'title,status,topic\nNote A,draft,Math\nNote B,published,History\n'
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'notes_sample.csv'
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
        const res = await axios.post('/api/notes/import', form, { headers: { 'Content-Type': 'multipart/form-data' } })
        importResult.value = res.data
        await reloadNotes()
    } catch (err) {
        console.error(err)
        alert(err?.response?.data?.message || 'Import thất bại')
    } finally {
        importing.value = false
    }
}

async function deleteNote(id) {
    try {
        await axios.delete(`/api/notes/${id}`)
        await reloadNotes()
        success('Note Deleted')
    } catch (e) {
        console.error(e)
    }
}
</script>

<template>
    <div class="space-y-4 dark:text-white">
        <!-- Toolbar -->
        <div class="justify-between flex flex-wrap items-center gap-2 bg-white  dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-3">
            <h1 class="text-xl font-semibold">Note</h1>
            <div class="">
                <input v-model="search" type="text" placeholder="Search notes..." class="mr-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700" />

                <select v-model="topicFilter" class="mr-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                    <option value="">All topics</option>
                    <option v-for="t in topics" :key="t" :value="t">{{ t }}</option>
                </select>

                <select v-model="statusFilter" class="px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                    <option value="">All status</option>
                 <option :value="1">Draft</option>
                 <option :value="2">Done</option>
                </select>
            </div>
            <div class=" flex gap-2 order-2 md:order-none md:ml-0">
                <RouterLink :to="{ name: 'note.add' }" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:opacity-90">Add</RouterLink>
                <button @click="showImportModal = true" class="px-3 py-2 rounded-lg bg-green-600 text-white hover:opacity-90">Import CSV</button>
            </div>
        </div>

        <!-- Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="closeImportModal"></div>
            <div class="relative w-full max-w-lg mx-4 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Notes</h3>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400" @click="closeImportModal">✕</button>
                </div>
                <div class="p-4 space-y-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        File CSV/XLSX với cột: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">title</code>, <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">status</code>, <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">topic</code>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <button type="button" class="px-3 py-2 rounded-md bg-amber-500 hover:bg-amber-600 text-white" @click="downloadSampleCsv">
                            Xem mẫu CSV
                        </button>
                        <span v-if="selectedFileName" class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ selectedFileName }}</span>
                        <input ref="fileInputRef" type="file" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" @change="onFileChange" />
                        <button type="button" class="px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700" @click="triggerFilePicker">
                            Chọn file
                        </button>
                    </div>
                    <div v-if="importResult" class="text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 text-gray-800 dark:text-gray-100">
                        <div class="mb-1">Kết quả:</div>
                        <pre class="whitespace-pre-wrap text-xs">{{ JSON.stringify(importResult, null, 2) }}</pre>
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

        <!-- Table -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-2 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="setSort('title')">Title</th>
                        <th class="px-4 py-2 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="setSort('topic')">Topic</th>
                        <th class="px-4 py-2 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="setSort('status')">Status</th>
                        <th class="px-4 py-2 cursor-pointer text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="setSort('updatedAt')">Updated</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <tr v-for="n in sortedNotes" :key="n.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ n.id }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">
                            <RouterLink :to="{ name: 'note.detail', params: {id: n.id }}" class="hover:underline">{{ n.title }}</RouterLink>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ n.topic }}</td>
                        <td class="px-4 py-2 text-sm">
                             <span :class="[
                                 'px-2 py-1 rounded-full text-xs',
                                 n.status === 2 ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                             ]">{{ n.status === 2 ? 'Done' : 'Draft' }}</span>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ n.updatedAt }}</td>
                        <td class="px-4 py-2 text-sm text-right">
                            <RouterLink :to="`/note/${n.id}/edit`" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 mr-2">Edit</RouterLink>
                            <button @click="deleteNote(n.id)" class="px-3 py-1.5 rounded-lg bg-red-600 text-white hover:opacity-80 hover:cursor-pointer">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
