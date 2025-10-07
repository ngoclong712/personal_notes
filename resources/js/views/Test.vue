<script setup lang="ts">
import { ref, computed } from 'vue'
import { Clock, Flag, ListChecks, CalendarDays } from 'lucide-vue-next'

const deadline = ref({
    id: 1,
    title: 'Hoàn thiện module báo cáo',
    description: 'Tổng hợp số liệu và viết báo cáo cuối kỳ',
    priority: 2,
    status: 1,
    subtasks_min_due_date: '2025-10-15',
    subtasks: [
        { id: 1, title: 'Thu thập dữ liệu', status: 2 },
        { id: 2, title: 'Phân tích kết quả', status: 1 },
        { id: 3, title: 'Viết báo cáo', status: 1 },
    ],
})

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
    const total = deadline.value.subtasks.length
    const done = deadline.value.subtasks.filter((s) => s.status === 2).length
    return Math.round((done / total) * 100)
})
</script>

<template>
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ deadline.title }}
            </h2>
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

        <!-- Description -->
        <p class="text-gray-600">{{ deadline.description }}</p>

        <!-- Info -->
        <div class="flex items-center space-x-6 text-sm text-gray-700">
            <div class="flex items-center space-x-2">
                <CalendarDays class="w-5 h-5 text-gray-500" />
                <span>Hạn gần nhất: <b>{{ deadline.subtasks_min_due_date }}</b></span>
            </div>
            <div class="flex items-center space-x-2">
                <ListChecks class="w-5 h-5 text-gray-500" />
                <span>Tiến độ: <b>{{ progress }}%</b></span>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div
                class="h-3 bg-blue-500 transition-all"
                :style="{ width: progress + '%' }"
            ></div>
        </div>

        <!-- Subtasks -->
        <div class="space-y-3">
            <h3 class="text-lg font-semibold text-gray-800">Các công việc con</h3>
            <ul class="space-y-2">
                <li
                    v-for="subtask in deadline.subtasks"
                    :key="subtask.id"
                    class="flex justify-between items-center bg-gray-50 p-3 rounded-md border border-gray-200"
                >
                    <span class="text-gray-700">{{ subtask.title }}</span>
                    <span
                        class="px-3 py-1 text-xs rounded-full"
                        :class="getStatusClass(subtask.status)"
                    >
            {{ getStatusText(subtask.status) }}
          </span>
                </li>
            </ul>
        </div>
    </div>
</template>
