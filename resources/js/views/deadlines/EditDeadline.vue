<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Trash2, X, CalendarCheck } from 'lucide-vue-next'
import { useRoute, useRouter } from 'vue-router'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { useToast } from '@/lib/toast'

const route = useRoute()
const router = useRouter()
const { success, error } = useToast()

// ===== Data =====
const form = ref({
    id: null as number | null,
    title: '',
    description: '',
    priority: 1,
    topic: null as any,
    topic_id: null as number | null,
    subtasks: [] as { id?: number; title: string; content: string; due_date: string; status: number }[],
})

const topics = ref([])

const priorities = [
    { id: 1, text: 'Thấp' },
    { id: 2, text: 'Trung bình' },
    { id: 3, text: 'Cao' },
]

const subtaskStatuses = [
    { id: 1, text: 'Đang thực hiện' },
    { id: 2, text: 'Hoàn thành' },
    { id: 3, text: 'Đã hủy' },
]

const loading = ref(false)
const showModal = ref(false)

const newSubtask = ref({
    title: '',
    content: '',
    due_date: '',
    status: 1,
})

// ===== Methods =====
const openModal = () => (showModal.value = true)
const closeModal = () => {
    showModal.value = false
    newSubtask.value = { title: '', content: '', due_date: '', status: 1 }
}

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
        await axios.put(`/api/deadlines/${form.value.id}`, form.value)
        success('Cập nhật deadline thành công!')
        setTimeout(() => router.push('/deadline'), 1000)
    } catch (err) {
        console.error(err)
        error('Không thể cập nhật deadline')
    } finally {
        loading.value = false
    }
}

// ===== Fetch data =====
onMounted(async () => {
    try {
        const [topicsRes, deadlineRes] = await Promise.all([
            axios.get('/api/topics'),
            axios.get(`/api/deadlines/${route.params.id}`),
        ])

        topics.value = topicsRes.data.data

        const data = deadlineRes.data.data
        form.value = {
            id: data.id,
            title: data.title,
            description: data.description,
            priority: data.priority,
            topic: data.topic,
            topic_id: data.topic?.id || null,
            subtasks: (data.subtasks || []).map((s: any) => ({
                ...s,
                due_date: s.due_date ? new Date(s.due_date).toISOString().split('T')[0] : '', // fix yyyy-MM-dd
            })),
        }
    } catch (err) {
        console.error(err)
        error('Không thể tải dữ liệu deadline')
    }
})
</script>

<template>
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-6 space-y-6 mt-8 relative">
        <!-- Header -->
        <div class="flex items-center w-full">
            <CalendarCheck class="w-7 h-7 mr-2 text-emerald-600" />
            <h2 class="text-2xl font-semibold text-gray-800">Cập nhật Deadline</h2>
        </div>

        <!-- Thông tin Deadline -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Nhập tiêu đề deadline"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-emerald-400"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mức độ ưu tiên</label>
                <select v-model="form.priority" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option v-for="p in priorities" :key="p.id" :value="p.id">{{ p.text }}</option>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                <Multiselect
                    v-model="form.topic"
                    :options="topics"
                    label="name"
                    track-by="id"
                    placeholder="-- Chọn topic --"
                    class="w-full border border-gray-300 rounded-md"
                />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Nhập mô tả cho deadline"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                ></textarea>
            </div>
        </div>

        <!-- Subtask header -->
        <div class="flex justify-between items-center mt-6">
            <h3 class="text-lg font-semibold text-gray-800">Công việc con</h3>
            <button
                type="button"
                class="flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg"
                @click="openModal"
            >
                <Plus class="w-4 h-4" /> Thêm Subtask
            </button>
        </div>

        <!-- Danh sách Subtask -->
        <div v-if="form.subtasks.length" class="space-y-3 mt-3">
            <div
                v-for="(subtask, index) in form.subtasks"
                :key="subtask.id || index"
                class="border border-gray-200 p-4 rounded-lg bg-gray-50 relative hover:shadow-sm transition"
            >
                <button
                    type="button"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                    @click="removeSubtask(index)"
                >
                    <Trash2 class="w-4 h-4" />
                </button>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Left: Title + Content -->
                    <div class="md:col-span-2">
                        <input
                            v-model="subtask.title"
                            placeholder="Tên subtask..."
                            class="font-medium text-gray-800 w-full border-b border-transparent focus:border-gray-300 focus:outline-none mb-1"
                        />
                        <textarea
                            v-model="subtask.content"
                            rows="2"
                            class="text-sm text-gray-600 w-full border border-gray-200 rounded-md px-2 py-1"
                            placeholder="Nội dung công việc..."
                        ></textarea>
                    </div>

                    <!-- Right: Date + Status -->
                    <div class="space-y-2">
                        <div class="text-sm">
                            <label class="block text-gray-700 mb-1">Hạn:</label>
                            <input
                                type="date"
                                v-model="subtask.due_date"
                                class="font-medium text-gray-700 border border-gray-300 rounded-md px-2 py-1 w-full"
                            />
                        </div>
                        <div class="text-sm">
                            <label class="block text-gray-700 mb-1">Trạng thái:</label>
                            <select
                                v-model="subtask.status"
                                class="w-full border border-gray-300 rounded-md px-2 py-1 text-gray-700"
                            >
                                <option v-for="st in subtaskStatuses" :key="st.id" :value="st.id">{{ st.text }}</option>
                            </select>
                        </div>
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
                {{ loading ? 'Đang cập nhật...' : 'Cập nhật Deadline' }}
            </button>
        </div>

        <!-- Modal thêm Subtask -->
        <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
                <button @click="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                    <X class="w-5 h-5" />
                </button>

                <h3 class="text-lg font-semibold mb-4 text-gray-800">Thêm Subtask</h3>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                        <input
                            v-model="newSubtask.title"
                            type="text"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                            placeholder="Nhập tiêu đề..."
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea
                            v-model="newSubtask.content"
                            rows="2"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                            placeholder="Nhập nội dung..."
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hạn hoàn thành</label>
                        <input
                            v-model="newSubtask.due_date"
                            type="date"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select
                            v-model="newSubtask.status"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option v-for="st in subtaskStatuses" :key="st.id" :value="st.id">{{ st.text }}</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button
                        @click="closeModal"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100"
                    >
                        Hủy
                    </button>
                    <button
                        @click="addSubtask"
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
