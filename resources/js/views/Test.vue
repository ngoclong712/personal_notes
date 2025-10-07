<script setup>
import { ref } from "vue";

// Mock data chung
const items = ref([
    {
        id: 1,
        title: "Thiết kế giao diện",
        description: "Hoàn thiện layout trang dashboard",
        status: "Đang làm",
        priority: "Cao",
        due: "2025-10-10",
        image: "https://placehold.co/100",
        category: "Frontend",
    },
    {
        id: 2,
        title: "Tạo API Backend",
        description: "Xây dựng CRUD cho bảng users",
        status: "Hoàn thành",
        priority: "Trung bình",
        due: "2025-10-05",
        image: "https://placehold.co/100",
        category: "Backend",
    },
    {
        id: 3,
        title: "Kiểm thử chức năng login",
        description: "Viết test case và test thủ công",
        status: "Chưa bắt đầu",
        priority: "Thấp",
        due: "2025-10-15",
        image: "https://placehold.co/100",
        category: "Testing",
    },
]);
</script>

<template>
    <div class="p-8 space-y-16">
        <!-- 1️⃣ Table -->
        <section>
            <h2 class="text-xl font-semibold mb-4">1️⃣ Table View</h2>
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Title</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Priority</th>
                    <th class="border p-2">Due</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
                    <td class="border p-2 text-center">{{ item.id }}</td>
                    <td class="border p-2">{{ item.title }}</td>
                    <td class="border p-2">{{ item.status }}</td>
                    <td class="border p-2">{{ item.priority }}</td>
                    <td class="border p-2">{{ item.due }}</td>
                </tr>
                </tbody>
            </table>
        </section>

        <!-- 2️⃣ Card Grid -->
        <section>
            <h2 class="text-xl font-semibold mb-4">2️⃣ Card Grid</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="bg-white rounded-xl border shadow-sm p-4 hover:shadow-md"
                >
                    <img
                        :src="item.image"
                        class="w-full h-32 object-cover rounded-md mb-3"
                    />
                    <h3 class="font-semibold">{{ item.title }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ item.description }}</p>
                    <p class="text-xs text-gray-400">Trạng thái: {{ item.status }}</p>
                </div>
            </div>
        </section>

        <!-- 3️⃣ Accordion -->
        <section>
            <h2 class="text-xl font-semibold mb-4">3️⃣ Accordion</h2>
            <div class="space-y-2">
                <details
                    v-for="item in items"
                    :key="item.id"
                    class="border rounded-lg p-3 bg-white"
                >
                    <summary class="cursor-pointer font-medium">
                        {{ item.title }} — <span class="text-gray-500">{{ item.status }}</span>
                    </summary>
                    <p class="mt-2 text-sm text-gray-600">{{ item.description }}</p>
                </details>
            </div>
        </section>

        <!-- 4️⃣ List View -->
        <section>
            <h2 class="text-xl font-semibold mb-4">4️⃣ List View</h2>
            <ul class="divide-y divide-gray-200 bg-white rounded-lg border">
                <li
                    v-for="item in items"
                    :key="item.id"
                    class="flex items-center p-3 space-x-3"
                >
                    <img :src="item.image" class="w-12 h-12 rounded-md object-cover" />
                    <div>
                        <p class="font-medium">{{ item.title }}</p>
                        <p class="text-sm text-gray-500">{{ item.description }}</p>
                    </div>
                </li>
            </ul>
        </section>

        <!-- 5️⃣ Kanban Board -->
        <section>
            <h2 class="text-xl font-semibold mb-4">5️⃣ Kanban Board</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-100 rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Chưa bắt đầu</h3>
                    <div
                        v-for="item in items.filter(i => i.status === 'Chưa bắt đầu')"
                        :key="item.id"
                        class="bg-white p-3 rounded-md shadow mb-2"
                    >
                        {{ item.title }}
                    </div>
                </div>
                <div class="bg-gray-100 rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Đang làm</h3>
                    <div
                        v-for="item in items.filter(i => i.status === 'Đang làm')"
                        :key="item.id"
                        class="bg-white p-3 rounded-md shadow mb-2"
                    >
                        {{ item.title }}
                    </div>
                </div>
                <div class="bg-gray-100 rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Hoàn thành</h3>
                    <div
                        v-for="item in items.filter(i => i.status === 'Hoàn thành')"
                        :key="item.id"
                        class="bg-white p-3 rounded-md shadow mb-2"
                    >
                        {{ item.title }}
                    </div>
                </div>
            </div>
        </section>

        <!-- 6️⃣ Calendar View (fake calendar grid) -->
        <section>
            <h2 class="text-xl font-semibold mb-4">6️⃣ Calendar View</h2>
            <div class="grid grid-cols-7 gap-2 text-center">
                <div
                    v-for="day in 14"
                    :key="day"
                    class="border p-2 rounded-md bg-white relative"
                >
                    <p class="text-xs text-gray-500 mb-2">Ngày {{ day }}</p>
                    <div
                        v-for="item in items.filter(i => parseInt(i.due.split('-')[2]) === day)"
                        :key="item.id"
                        class="text-xs bg-blue-100 text-blue-700 rounded px-1 mb-1"
                    >
                        {{ item.title }}
                    </div>
                </div>
            </div>
        </section>

        <!-- 7️⃣ Chart / Dashboard -->
        <section>
            <h2 class="text-xl font-semibold mb-4">7️⃣ Dashboard View</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-white border rounded-lg p-4">
                    <p class="text-2xl font-bold">3</p>
                    <p class="text-gray-500 text-sm">Tổng số task</p>
                </div>
                <div class="bg-white border rounded-lg p-4">
                    <p class="text-2xl font-bold">1</p>
                    <p class="text-gray-500 text-sm">Đang làm</p>
                </div>
                <div class="bg-white border rounded-lg p-4">
                    <p class="text-2xl font-bold">1</p>
                    <p class="text-gray-500 text-sm">Hoàn thành</p>
                </div>
            </div>
            <div class="mt-6 bg-gradient-to-r from-blue-200 to-blue-400 h-3 rounded-lg w-1/2 mx-auto"></div>
            <p class="text-center mt-2 text-sm text-gray-600">
                (Biểu đồ giả lập, không có thư viện chart)
            </p>
        </section>

        <!-- 8️⃣ Master–Detail -->
        <section>
            <h2 class="text-xl font-semibold mb-4">8️⃣ Master–Detail View</h2>
            <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-sm border">
                <div class="w-full md:w-1/3 border-r">
                    <ul>
                        <li
                            v-for="item in items"
                            :key="item.id"
                            class="p-3 cursor-pointer hover:bg-gray-100"
                        >
                            {{ item.title }}
                        </li>
                    </ul>
                </div>
                <div class="flex-1 p-6">
                    <h3 class="font-semibold">Chi tiết công việc</h3>
                    <p class="text-gray-500 text-sm mt-2">
                        (Phần này sẽ hiển thị thông tin chi tiết khi click vào)
                    </p>
                </div>
            </div>
        </section>

        <!-- 9️⃣ Tree View -->
        <section>
            <h2 class="text-xl font-semibold mb-4">9️⃣ Tree View</h2>
            <ul class="bg-white border rounded-lg p-3">
                <li>
                    <strong>Frontend</strong>
                    <ul class="pl-5 list-disc">
                        <li>Thiết kế giao diện</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Backend</strong>
                    <ul class="pl-5 list-disc">
                        <li>Tạo API Backend</li>
                    </ul>
                </li>
                <li class="mt-2">
                    <strong>Testing</strong>
                    <ul class="pl-5 list-disc">
                        <li>Kiểm thử chức năng login</li>
                    </ul>
                </li>
            </ul>
        </section>

        <!-- 🔟 Timeline -->
        <section>
            <h2 class="text-xl font-semibold mb-4">🔟 Timeline / Activity Feed</h2>
            <div class="relative border-l border-gray-300 pl-6">
                <div
                    v-for="(item, index) in items"
                    :key="item.id"
                    class="mb-6 relative"
                >
                    <div
                        class="absolute -left-2 top-1 w-3 h-3 bg-blue-500 rounded-full"
                    ></div>
                    <p class="font-semibold">{{ item.title }}</p>
                    <p class="text-sm text-gray-500">{{ item.status }} — {{ item.due }}</p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
summary::-webkit-details-marker {
    display: none;
}
</style>
