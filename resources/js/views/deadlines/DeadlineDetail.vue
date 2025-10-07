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
        1: 'Thấp',
        2: 'Trung bình',
        3: 'Cao',
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
        1: 'Đang thực hiện',
        2: 'Đã hoàn thành',
        3: 'Đã huỷ',
        4: 'Quá hạn',
    }
    return texts[status] || texts[1]
}

// ===== Tính progress =====
const progress = computed(() => {
    if (!deadline.value || !deadline.value.subtasks) return 0
    const validSubtasks = deadline.value.subtasks.filter((s: any) => s.status !== 3) // loại subtask bị huỷ
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
        console.error('Lỗi khi tải dữ liệu deadline:', err)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="max-w-4xl mx-auto py-8">
        <div v-if="loading" class="text-center text-gray-500">Đang tải dữ liệu...</div>

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

            <!-- Thông tin chung -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div class="flex items-center space-x-2">
                    <Tag class="w-4 h-4 text-gray-500" />
                    <span>Chủ đề: <b>{{ deadline.topic?.name || '—' }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <CalendarDays class="w-4 h-4 text-gray-500" />
                    <span>Hạn gần nhất: <b>{{ deadline.subtasks_min_due_date || '—' }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <ListChecks class="w-4 h-4 text-gray-500" />
                    <span>Tiến độ: <b>{{ progress }}%</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <Clock class="w-4 h-4 text-gray-500" />
                    <span>Ngày tạo: <b>{{ deadline.created_at?.slice(0, 10) }}</b></span>
                </div>
                <div class="flex items-center space-x-2">
                    <Clock class="w-4 h-4 text-gray-500" />
                    <span>Cập nhật: <b>{{ deadline.updated_at?.slice(0, 10) }}</b></span>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex items-center space-x-2 mb-2 text-gray-700 font-medium">
                    <FileText class="w-4 h-4 text-gray-500" /> <span>Mô tả</span>
                </div>
                <p class="text-gray-700 whitespace-pre-line">{{ deadline.description || 'Không có mô tả' }}</p>
            </div>

            <!-- Progress bar -->
            <div v-if="deadline.status !== 3" class="space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Tiến độ hoàn thành</span>
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
                Công việc đã bị huỷ — không có tiến độ.
            </div>

            <!-- Subtasks -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-800">Các công việc con</h3>
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
                        <p class="text-xs text-gray-500 mt-1">Hạn: {{ subtask.due_date || '—' }}</p>
                    </li>
                </ul>
            </div>
        </div>

        <div v-else class="text-center text-gray-500">Không tìm thấy deadline.</div>
    </div>
</template>
