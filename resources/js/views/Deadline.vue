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
                    <div class="md:col-span-2">
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
                            <option value="1">Chờ xử lý</option>
                            <option value="2">Đang thực hiện</option>
                            <option value="3">Hoàn thành</option>
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
                                        <input type="checkbox" :checked="subtask.status === 2"
                                               @change="toggleSubtask(deadline.id, subtask.id)"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                        />
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

const router = useRouter()

// Reactive data
const expandedDeadlines = ref<number[]>([])
const searchQuery = ref<string>('')
const statusFilter = ref<string>('')
const priorityFilter = ref<string>('')
const showFilters = ref<boolean>(true)
const activeOverflowMenu = ref<number | null>(null)

// Sample data
const deadlines = ref([
                {
                    id: 1,
                    title: 'Hoàn thành dự án website',
                    description: 'Phát triển website bán hàng với Laravel và Vue.js',
                    status: 1, // 1: pending, 2: in_progress, 3: completed
                    priority: 2, // 1: low, 2: medium, 3: high
                    topic_id: 1,
                    created_at: '2024-01-15T08:00:00Z',
                    updated_at: '2024-01-15T08:00:00Z',
                    subtasks: [
                        {
                            id: 1,
                            deadline_id: 1,
                            title: 'Thiết kế giao diện',
                            content: 'Tạo mockup và wireframe cho website',
                            due_date: '2024-01-20T18:00:00Z',
                            status: 2, // 1: pending, 2: in_progress, 3: completed
                            created_at: '2024-01-15T08:00:00Z',
                            updated_at: '2024-01-15T08:00:00Z'
                        },
                        {
                            id: 2,
                            deadline_id: 1,
                            title: 'Setup database',
                            content: 'Tạo migration và seeder cho database',
                            due_date: '2024-01-18T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-15T08:00:00Z',
                            updated_at: '2024-01-15T08:00:00Z'
                        },
                        {
                            id: 3,
                            deadline_id: 1,
                            title: 'Implement authentication',
                            content: 'Tạo hệ thống đăng nhập và đăng ký',
                            due_date: '2024-01-25T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-15T08:00:00Z',
                            updated_at: '2024-01-15T08:00:00Z'
                        }
                    ]
                },
                {
                    id: 2,
                    title: 'Học Vue.js 3',
                    description: 'Nắm vững kiến thức về Vue.js 3 và Composition API',
                    status: 2,
                    priority: 3,
                    topic_id: 2,
                    created_at: '2024-01-10T08:00:00Z',
                    updated_at: '2024-01-10T08:00:00Z',
                    subtasks: [
                        {
                            id: 4,
                            deadline_id: 2,
                            title: 'Học Composition API',
                            content: 'Tìm hiểu về setup(), ref(), reactive()',
                            due_date: '2024-01-22T18:00:00Z',
                            status: 2,
                            created_at: '2024-01-10T08:00:00Z',
                            updated_at: '2024-01-10T08:00:00Z'
                        },
                        {
                            id: 5,
                            deadline_id: 2,
                            title: 'Thực hành với project nhỏ',
                            content: 'Tạo todo app với Vue 3',
                            due_date: '2024-01-28T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-10T08:00:00Z',
                            updated_at: '2024-01-10T08:00:00Z'
                        }
                    ]
                },
                {
                    id: 3,
                    title: 'Chuẩn bị presentation',
                    description: 'Chuẩn bị bài thuyết trình cho cuộc họp tuần tới',
                    status: 1,
                    priority: 1,
                    topic_id: 3,
                    created_at: '2024-01-12T08:00:00Z',
                    updated_at: '2024-01-12T08:00:00Z',
                    subtasks: [
                        {
                            id: 6,
                            deadline_id: 3,
                            title: 'Thu thập dữ liệu',
                            content: 'Gather data và statistics cho presentation',
                            due_date: '2024-01-19T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-12T08:00:00Z',
                            updated_at: '2024-01-12T08:00:00Z'
                        },
                        {
                            id: 7,
                            deadline_id: 3,
                            title: 'Tạo slide deck',
                            content: 'Design và tạo PowerPoint presentation',
                            due_date: '2024-01-24T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-12T08:00:00Z',
                            updated_at: '2024-01-12T08:00:00Z'
                        },
                        {
                            id: 8,
                            deadline_id: 3,
                            title: 'Rehearse presentation',
                            content: 'Luyện tập thuyết trình trước khi present',
                            due_date: '2024-01-26T18:00:00Z',
                            status: 1,
                            created_at: '2024-01-12T08:00:00Z',
                            updated_at: '2024-01-12T08:00:00Z'
                        }
                    ]
                },
        ])

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

const toggleSubtask = (deadlineId: number, subtaskId: number) => {
    const deadline = deadlines.value.find(d => d.id === deadlineId);
    if (deadline) {
        const subtask = deadline.subtasks.find(s => s.id === subtaskId);
        if (subtask) {
            subtask.status = subtask.status === 2 ? 1 : 2;
        }
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
        1: 'bg-gray-100 text-gray-800',
        2: 'bg-blue-100 text-blue-800',
        3: 'bg-green-100 text-green-800'
    };
    return classes[status] || classes[1];
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: 'Chờ xử lý',
        2: 'Đang thực hiện',
        3: 'Hoàn thành'
    };
    return texts[status] || texts[1];
}

const getSubtaskStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        1: 'bg-gray-100 text-gray-800',
        2: 'bg-blue-100 text-blue-800',
        3: 'bg-green-100 text-green-800'
    };
    return classes[status] || classes[1];
}

const getSubtaskStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: 'Chờ',
        2: 'Đang làm',
        3: 'Xong'
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
}

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
}

const toggleOverflowMenu = (deadlineId: number) => {
    activeOverflowMenu.value = activeOverflowMenu.value === deadlineId ? null : deadlineId;
}

const navigateToAdd = () => {
    router.push('/deadlines/create');
}

const navigateToEdit = (deadline: any) => {
    router.push(`/deadlines/${deadline.id}/edit`);
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

// Lifecycle
onMounted(() => {
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
