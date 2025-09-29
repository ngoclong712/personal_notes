<script setup lang="ts">
import Multiselect from "vue-multiselect";
import { QuillEditor } from "@vueup/vue-quill";
import { onMounted, ref, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "@/lib/toast";
import { X } from "lucide-vue-next";

const { success } = useToast();
const route = useRoute();
const router = useRouter();

const topics = ref<any[]>([]);

const form = reactive({
    title: "",
    status: 1,
    topic: null as any,
    content: "",
    attachments: [] as File[], // file mới upload
    existingAttachments: [] as any[], // file đã có từ DB
    deletedIds: [] as number[], // id file cần xoá
});

function removeExistingFile(id: number) {
    form.existingAttachments = form.existingAttachments.filter(f => f.id !== id);
    form.deletedIds.push(id); // lưu id lại để gửi BE
}

function handleFiles(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.attachments.push(...Array.from(target.files));
    }
    target.value = ""; // reset input để chọn lại được cùng file
}

function removeFile(index: number) {
    form.attachments.splice(index, 1);
}

onMounted(async () => {
    try {
        const topicRes = await axios.get("/api/topics");
        topics.value = topicRes.data.data;

        const res = await axios.get(`/api/notes/${route.params.id}`);
        const data = res.data.data ?? res.data;

        form.title = data.title;
        form.status = data.status;
        form.content = data.content;
        form.existingAttachments = data.attachments || [];

        if (data.topic && data.topic.id) {
            form.topic = topics.value.find(t => t.id === data.topic.id) || null;
        }
    } catch (e) {
        console.error("Can not load data", e);
    }
});

async function submit() {
    const payload = new FormData();
    payload.append("title", form.title);
    payload.append("status", form.status.toString());
    payload.append("content", form.content);
    payload.append("topic_id", form.topic?.id ?? "");

    // file mới
    form.attachments.forEach((file) => {
        payload.append("attachments[]", file);
    });

    // file xoá
    form.deletedIds.forEach((id) => {
        payload.append("deleted_ids[]", id.toString());
    });

    await axios.post(`/api/notes/${route.params.id}?_method=PUT`, payload, {
        headers: { "Content-Type": "multipart/form-data" },
    });

    success("Note updated");
    router.push({ name: "note" });
}

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

            <!-- Attachments -->
            <div class="col-span-2 mt-15">
                <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>

                <div class="flex items-center gap-4">
                    <!-- input file ẩn -->
                    <input
                        id="attachments"
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                        class="hidden"
                        @change="handleFiles"
                    />

                    <!-- nút chọn file -->
                    <label
                        for="attachments"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-blue-600 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Files
                    </label>

                    <!-- nút Add -->
                    <button
                        class="bg-green-500 text-white rounded-lg px-6 py-2 hover:bg-green-600 transition ml-auto"
                        @click.prevent="submit"
                    >
                        Update
                    </button>
                </div>

                <!-- existing files -->
                <ul v-if="form.existingAttachments.length" class="mt-3 space-y-2 text-sm text-gray-700">
                    <li
                        v-for="file in form.existingAttachments"
                        :key="file.id"
                        class="flex items-center justify-between px-3 py-2 border border-gray-200 rounded-md bg-gray-50"
                    >
                        <a :href="file.file_path" target="_blank" class="text-blue-600 underline">
                            {{ file.file_name }}
                        </a>
                        <button
                            type="button"
                            class="text-red-500 hover:text-red-700"
                            @click="removeExistingFile(file.id)"
                        >
                            <X class="w-4 h-4"/>
                        </button>
                    </li>
                </ul>

                <!-- new files -->
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

