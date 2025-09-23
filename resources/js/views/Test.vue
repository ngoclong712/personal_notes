<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const file = ref<File|null>(null)
const message = ref("")
const topics = ref<any[]>([])   // 👈 thêm biến lưu dữ liệu import

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    file.value = target.files ? target.files[0] : null
}

const uploadFile = async () => {
    if (!file.value) {
        message.value = "Vui lòng chọn file!"
        return
    }

    const formData = new FormData()
    formData.append("file", file.value)

    try {
        const res = await axios.post("/api/topics/import", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        })
        message.value = res.data.message || "Import thành công!"
        topics.value = res.data.data || []   // 👈 lưu dữ liệu để hiển thị
    } catch (err: any) {
        console.error(err)
        message.value = err.response?.data?.message || "Có lỗi xảy ra"
    }
}
</script>

<template>
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">Import Topics Excel</h2>

        <input type="file" @change="onFileChange" class="mb-4" accept=".xlsx"/>
        <button
            @click="uploadFile"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
            Upload
        </button>

        <p v-if="message" class="mt-4 font-semibold">{{ message }}</p>

        <!-- 👇 render danh sách vừa import -->
        <ul v-if="topics.length" class="mt-4 list-disc pl-5">
            <li v-for="(t, i) in topics" :key="i">
                {{ t.name }} - {{ t.description }}
            </li>
        </ul>
    </div>
</template>
