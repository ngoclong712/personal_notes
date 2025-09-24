<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useToast } from '@/lib/toast'
import Multiselect from 'vue-multiselect'

import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

import 'vue-multiselect/dist/vue-multiselect.css'

const router = useRouter()

const topics = ref([])

const form = reactive({
    title: '',
    status: 1,
    topic_id: null,
    content: '',
})

const { success } = useToast()

async function submit() {
    await axios.post('/api/notes', form)
    success('Topic created')
    router.push({ name: 'note' })
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
                    v-model="form.topic_id"
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
            <div class="mt-15">
                <Button class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:cursor-pointer">Add</Button>
            </div>
        </form>
    </div>
</template>
