<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useToast } from '@/lib/toast'
import Multiselect from 'vue-multiselect'
import { X } from 'lucide-vue-next'

import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import 'vue-multiselect/dist/vue-multiselect.css'

const router = useRouter()
const topics = ref([])

const form = reactive({
    title: '',
    status: 1,
    topic: null as any,
    content: '',
    attachments: [] as File[]
})

const { success, error } = useToast()

function handleFiles(event: Event) {
    const target = event.target as HTMLInputElement
    if (target.files) {
        const newFiles = Array.from(target.files)

        form.attachments.push(...newFiles)

        target.value = ''
    }
}

function removeFile(index: number) {
    form.attachments.splice(index, 1)
}
async function submit() {
    try {
        const data = new FormData()
        data.append('title', form.title)
        data.append('status', form.status.toString())
        data.append('content', form.content)
        data.append('topic_id', form.topic?.id ?? '')

        form.attachments.forEach((file, index) => {
            data.append(`attachments[${index}]`, file)
        })

        await axios.post('/api/notes', data, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        success('Note created')
        router.push({ name: 'note' })
    } catch (e) {
        console.error(e)
        error('Can not create note')
    }
}

onMounted(async () => {
    try {
        const res = await axios.get('/api/topics')
        topics.value = res.data.data
    } catch (e) {
        console.error('Can not get topics')
    }
})
</script>


<template>
    <div class="p-4">
        <form
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 grid grid-cols-2 gap-4"
            @submit.prevent="submit"
        >
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    v-model="form.title"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="text"
                    placeholder="Input title ..."
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800"
                >
                    <option :value="1">Draft</option>
                    <option :value="2">Done</option>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                <Multiselect
                    v-model="form.topic"
                    :options="topics"
                    label="name"
                    track-by="id"
                    placeholder="-- Please Select Topic --"
                    class="w-full"
                />
            </div>

            <div class="col-span-2">
                <QuillEditor
                    v-model:content="form.content"
                    contentType="html"
                    theme="snow"
                    toolbar="full"
                    class="custom-editor"
                />
            </div>
            <div class="mt-15 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>

                <div class="flex items-center gap-4">
                    <!-- input ẩn -->
                    <input
                        id="attachments"
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                        class="hidden"
                        @change="handleFiles"
                    />

                    <!-- nút custom chọn file -->
                    <label
                        for="attachments"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-blue-600 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Files
                    </label>

                    <!-- nút Add cùng hàng -->
                    <button
                        class="bg-green-500 text-white rounded-lg px-6 py-2 hover:bg-green-600 transition ml-auto"
                        @click.prevent="submit"
                    >
                        Add
                    </button>
                </div>

                <!-- danh sách file đã chọn -->
                <ul v-if="form.attachments.length" class="mt-3 space-y-2 text-sm text-gray-700">
                    <li
                        v-for="(file, index) in form.attachments"
                        :key="index"
                        class="flex items-center justify-between px-3 py-2 border border-gray-200 rounded-md bg-gray-50"
                    >
                        <span>{{ file.name }}</span>
                        <button
                            type="button"
                            class="text-red-500 hover:text-red-700"
                            @click="removeFile(index)"
                        >
                            <X class="w-4 h-4"/>
                        </button>
                    </li>
                </ul>
            </div>
        </form>
    </div>
</template>
