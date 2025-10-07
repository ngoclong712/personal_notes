<template>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Deadlines</h1>
                    <p class="text-gray-600">Quản lý deadline và subtasks của bạn</p>
                </div>
                <button
                    @click="navigateToAdd"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Thêm Deadline</span>
                </button>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Box -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Tìm kiếm theo tiêu đề..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                        <select
                            v-model="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Tất cả</option>
                            <option value="1">Đang thực hiện</option>
                            <option value="2">Đã hoàn thành</option>
                            <option value="3">Đã huỷ</option>
                            <option value="4">Quá hạn</option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Độ ưu tiên</label>
                        <select
                            v-model="priorityFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Tất cả</option>
                            <option value="1">Thấp</option>
                            <option value="2">Trung bình</option>
                            <option value="3">Cao</option>
                        </select>
                    </div>

                    <!-- Sort by Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo độ ưu tiên</label>
                        <select
                            v-model="prioritySortOrder"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Mặc định</option>
                            <option value="asc">Tăng dần</option>
                            <option value="desc">Giảm dần</option>
                        </select>
                    </div>

                </div>

                    <!-- Clear Filters Button -->
                    <div v-if="showFilters" class="mt-4 flex justify-end">
                        <button
                            @click="clearFilters"
                            class="text-sm text-gray-500 hover:text-gray-700 flex items-center space-x-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Xóa bộ lọc</span>
                        </button>
                    </div>

                <!-- Toggle Filters Button -->
                <div class="flex justify-center">
                    <button
                        @click="toggleFilters"
                        class="flex items-center space-x-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-md transition-colors duration-200"
                    >
                        <svg
                            class="w-4 h-4 transform transition-transform duration-200"
                            :class="{ 'rotate-180': showFilters }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span>{{ showFilters ? 'Thu gọn bộ lọc' : 'Mở rộng bộ lọc' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Deadlines Accordion -->
        <div class="space-y-6">
            <div v-for="deadline in filteredDeadlines" :key="deadline.id"
                 class="bg-white rounded-lg shadow-md border border-gray-200">
                <!-- Deadline Header -->
                <div class="px-4 py-5 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                     @click="toggleDeadline(deadline.id)">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                     :class="{ 'rotate-90': expandedDeadlines.includes(deadline.id) }" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ deadline.title }}</h3>
                                <p class="text-sm text-gray-600">{{ deadline.description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Priority Badge -->
                            <span
                            class="px-2 py-1 text-xs font-medium rounded-full"
                            :class="getPriorityClass(deadline.priority)">
                                {{ getPriorityText(deadline.priority) }}
                            </span>

                            <!-- Status Badge -->
                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                  :class="getStatusClass(deadline.status)">
                                {{ getStatusText(deadline.status) }}
                            </span>
                            <!-- Overflow Menu -->
                            <div class="relative z-50">
                                <button @click.stop="toggleOverflowMenu(deadline.id)"
                                        class="p-1 rounded-full hover:bg-gray-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                        </path>
                                    </svg>
                                </button> <!-- Dropdown Menu -->
                                <div v-if="activeOverflowMenu === deadline.id"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200"
                                     style="position: absolute; top: 100%; right: 0;">
                                    <div class="py-1">
                                        <button
                                            @click="navigateToView(deadline)"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                            Xem chi tiết
                                        </button>
                                        <button @click="navigateToEdit(deadline)"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Chỉnh sửa
                                        </button>
                                        <button @click="deleteDeadline(deadline.id)"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- Subtasks Section -->
                <div v-if="expandedDeadlines.includes(deadline.id)" class="border-t border-gray-200 bg-gray-50">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4"><h4
                            class="text-md font-medium text-gray-700">Subtasks</h4> <span class="text-sm text-gray-500">{{
                                deadline.subtasks.length
                            }} tasks</span></div> <!-- Subtasks List -->
                        <div class="space-y-3">
                            <div v-for="subtask in deadline.subtasks" :key="subtask.id"
                                 class="bg-white rounded-md border border-gray-200 p-3 hover:shadow-sm transition-shadow duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div><h5 class="text-sm font-medium text-gray-800">{{ subtask.title }}</h5>
                                            <p v-if="subtask.content" class="text-xs text-gray-600 mt-1">
                                                {{ subtask.content }}</p></div>
                                    </div>
                                    <div class="flex items-center space-x-2"><span
                                        class="text-xs text-gray-500"> {{ formatDate(subtask.due_date) }} </span> <span
                                        class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getSubtaskStatusClass(subtask.status)"> {{
                                            getSubtaskStatusText(subtask.status)
                                        }} </span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredDeadlines.length === 0 && !searchQuery && !statusFilter && !priorityFilter" class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có deadline nào</h3>
            <p class="text-gray-500">Tạo deadline đầu tiên để bắt đầu quản lý công việc của bạn.</p>
        </div>

        <!-- No Results State -->
        <div v-if="filteredDeadlines.length === 0 && (searchQuery || statusFilter || priorityFilter)" class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy kết quả</h3>
            <p class="text-gray-500">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from "axios";
import { useToast } from '@/lib/toast'

const router = useRouter()
const { success } = useToast()

// Reactive data
const expandedDeadlines = ref<number[]>([])
const searchQuery = ref<string>('')
const statusFilter = ref<string>('')
const priorityFilter = ref<string>('')
const prioritySortOrder = ref('');
const showFilters = ref<boolean>(true)
const activeOverflowMenu = ref<number | null>(null)

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
const deadlines = ref<Deadline[]>([])

// Computed properties
const filteredDeadlines = computed(() => {
    let filtered = deadlines.value;

    // Search filter
    if (searchQuery.value) {
        filtered = filtered.filter(deadline =>
            deadline.title.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // Status filter
    if (statusFilter.value) {
        filtered = filtered.filter(deadline =>
            deadline.status === parseInt(statusFilter.value)
        );
    }

    // Priority filter
    if (priorityFilter.value) {
        filtered = filtered.filter(deadline =>
            deadline.priority === parseInt(priorityFilter.value)
        );
    }

    if (prioritySortOrder.value === 'asc') {
        filtered.sort((a, b) => a.priority - b.priority);
    } else if (prioritySortOrder.value === 'desc') {
        filtered.sort((a, b) => b.priority - a.priority);
    }

    return filtered;
})

// Methods
const toggleDeadline = (deadlineId: number) => {
    const index = expandedDeadlines.value.indexOf(deadlineId);
    if (index > -1) {
        expandedDeadlines.value.splice(index, 1);
    } else {
        expandedDeadlines.value.push(deadlineId);
    }
}

const getPriorityClass = (priority: number) => {
    const classes: Record<number, string> = {
        1: 'bg-green-100 text-green-800',
        2: 'bg-yellow-100 text-yellow-800',
        3: 'bg-red-100 text-red-800'
    };
    return classes[priority] || classes[1];
}

const getPriorityText = (priority: number) => {
    const texts: Record<number, string> = {
        1: 'Thấp',
        2: 'Trung bình',
        3: 'Cao'
    };
    return texts[priority] || texts[1];
}

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        1: 'bg-blue-100 text-blue-800',
        2: 'bg-green-100 text-green-800',
        3: 'bg-gray-100 text-gray-800',
        4: 'bg-red-100 text-red-800'
    };
    return classes[status] || classes[1];
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: 'Đang thực hiện',
        2: 'Đã Hoàn thành',
        3: 'Đã Huỷ',
        4: 'Quá Hạn',
    };
    return texts[status] || texts[1];
}

const getSubtaskStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        1: 'bg-blue-100 text-blue-800',
        2: 'bg-green-100 text-green-800',
        3: 'bg-gray-100 text-gray-800',
        4: 'bg-red-100 text-red-800'
    };
    return classes[status] || classes[1];
}

const getSubtaskStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: 'Đang làm',
        2: 'Hoàn thành',
        3: 'Huỷ',
        4: 'Quá hạn',
    };
    return texts[status] || texts[1];
}

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    priorityFilter.value = '';
    prioritySortOrder.value = '';
    fetchDeadlines();
}

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
}

const toggleOverflowMenu = (deadlineId: number) => {
    activeOverflowMenu.value = activeOverflowMenu.value === deadlineId ? null : deadlineId;
}

const navigateToAdd = () => {
    router.push('/deadline/add');
}

const navigateToView = (deadline) => {
    router.push(`/deadline/${deadline.id}`);
    activeOverflowMenu.value = null;
};
const navigateToEdit = (deadline: any) => {
    router.push(`/deadline/${deadline.id}/edit`);
    activeOverflowMenu.value = null;
}

const deleteDeadline = (deadlineId: number) => {
    if (confirm('Bạn có chắc chắn muốn xóa deadline này?')) {
        const index = deadlines.value.findIndex(d => d.id === deadlineId);
        if (index > -1) {
            deadlines.value.splice(index, 1);
        }
    }
    activeOverflowMenu.value = null;
}

async function fetchDeadlines() {
    try {
        const res = await axios.get('/api/deadlines');
        deadlines.value = res.data.data;
    }
    catch (error) {

    }
}

// Lifecycle
onMounted(() => {
    fetchDeadlines();
    // Close overflow menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            activeOverflowMenu.value = null;
        }
    });
})
</script>

<style scoped>
/* Custom scrollbar for better UX */
.space-y-4::-webkit-scrollbar {
    width: 6px;
}

.space-y-4::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.space-y-4::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.space-y-4::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>
