<script setup>
import { watch, ref } from "vue";
import { Tippy } from "vue-tippy";
import { onClickOutside, useFetch } from "@vueuse/core";
import { useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    thead: Array,
    fetch: Boolean,
    logs: Object,
});
const baseUrl = import.meta.env.VITE_APP_URL;

// delete modal
const deleteModal = ref(null);
const showDeleteModal = ref(false);
const deleteOne = ref([]);

// detail modal
const detailModal = ref(null);
const showDetailModal = ref(false);
const detailFetch = ref(false);
onClickOutside(detailModal, (event) => {
    showDetailModal.value = false;
});
const logDetail = ref({});

// edit modal
const editModal = ref(null);
const showEditModal = ref(false);
const editFetch = ref(false);
const logEdit = ref({});

//retry
const successMessage = ref("");
const errorMessage = ref("");

const emit = defineEmits(["checkbox", "refresh"]);

const checked = ref([]);

watch(checked, (newChecked) => {
    checked.value = newChecked;
    emit("checkbox", checked.value);
});

// proses delete by id
const pageInertia = usePage();

const form = useForm({
    ids: [],
    _token: pageInertia.props.csrf_token,
});

function deleteLog() {
    form.ids = deleteOne.value;
    form.delete(`${baseUrl}/integrasi/delete`, {
        onSuccess: () => {
            successMessage.value = "Log deleted successfully";
            emit("delete");
            showDeleteModal.value = false;
            form.reset();
        },
        onError: () => {
            errorMessage.value = "Failed to delete log";
        },
    });
}

function getDetail(id) {
    detailFetch.value = false;
    logDetail.value = {};
    axios
        .post(`${baseUrl}/api/email-queue/extract`, { id })
        .then((response) => {
            logDetail.value = response.data.data;
            detailFetch.value = true;
        })
        .catch((error) => {
            console.error(error);
            detailFetch.value = false;
        });
}

const formRetry = useForm({
    id: 0,
    secret: "",
    mail: [
        {
            to: "",
            subject: "",
            content: "",
            attachment: [],
            priority: 0,
        },
    ],
    _token: pageInertia.props.csrf_token,
});

function getEdit(id) {
    editFetch.value = false;
    logEdit.value = {};
    axios
        .post(`${baseUrl}/api/email-queue/extract`, { id })
        .then((response) => {
            const newData = response.data.data;
            console.log("Fetched data:", newData.secret);
            if (newData) {
                formRetry.id = id;
                formRetry.mail[0].to = newData.to;
                formRetry.mail[0].subject = newData.subject;
                formRetry.mail[0].content = newData.content;
                formRetry.mail[0].attachment = newData.attachment || [];
                formRetry.mail[0].priority = newData.priority;
                formRetry.secret = newData.secret;
                editFetch.value = true;
            }
        })
        .catch((error) => {
            console.error(error);
            editFetch.value = false;
        });
}

function handleRetry() {
    axios
        .post(`${baseUrl}/api/email-queue/send`, formRetry)
        .then((response) => {
            if (response.data.kode === 200) {f
                successMessage.value = response.data.message;
            } else {
                errorMessage.value = response.data.message;
            }
        })
        .catch((error) => {
            console.log(error.response);
            errorMessage.value =
                error.response.data.message || "An error occurred";
        })
        .finally(() => {
            showEditModal.value = false;
            formRetry.reset();
            emit("refresh");
        });
}

watch(successMessage, (newMessage) => {
    if (newMessage) {
        setTimeout(() => {
            successMessage.value = "";
        }, 5000);
    }
});

watch(errorMessage, (newMessage) => {
    if (newMessage) {
        setTimeout(() => {
            errorMessage.value = "";
        }, 5000);
    }
});
</script>

<template>
    <!-- alert success -->
    <div
        v-if="successMessage"
        class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
        role="alert"
    >
        <svg
            class="flex-shrink-0 inline w-4 h-4 me-3"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
            />
        </svg>
        <span class="sr-only">Info</span>
        <div>
            {{ successMessage }}
        </div>
    </div>
    <!-- alert error -->
    <div
        v-if="errorMessage"
        class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
        role="alert"
    >
        <svg
            class="flex-shrink-0 inline w-4 h-4 me-3"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
            />
        </svg>
        <span class="sr-only">Info</span>
        <div>
            {{ errorMessage }}
        </div>
    </div>
    <table
        class="w-full text-sm text-left text-gray-500 border dark:text-gray-400 rounded-2xl"
    >
        <thead
            class="text-xs text-gray-700 uppercase border-b bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
        >
            <tr>
                <th
                    scope="col"
                    class="px-6 py-3"
                    v-for="item in thead"
                    :key="item"
                >
                    {{ item }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="fetch">
                <td colspan="7" class="px-6 py-4 text-lg font-bold text-center">
                    <div role="status">
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </td>
            </tr>
            <tr
                v-if="
                    (!props.logs.data || props.logs.data.length === 0) && !fetch
                "
            >
                <td colspan="7" class="px-6 py-4 text-lg font-bold text-center">
                    Tidak ada data yang ditemukan!
                </td>
            </tr>
            <tr
                v-for="(item, index) in logs.data"
                :key="item.id"
                v-if="!fetch"
                class="dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
                <!-- input checkbox -->
                <td class="px-6 py-4">
                    <input
                        type="checkbox"
                        :value="item.id"
                        v-model="checked"
                        class="w-4 h-4 mt-1.5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                </td>
                <td class="px-6 py-4">
                    {{ item.application.name }}
                </td>
                <td class="px-6 py-4">
                    {{ JSON.parse(item.request).to }}
                </td>
                <td class="px-6 py-4">
                    {{ JSON.parse(item.request).subject }}
                </td>
                <td class="px-6 py-4">
                    <div
                        :class="
                            item.status == 'success'
                                ? 'bg-green-200 text-green-900'
                                : 'bg-red-200 text-red-900'
                        "
                        class="p-1 text-center rounded-full"
                    >
                        {{ item.status }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    {{ new Date(item.updated_at).toLocaleString() }}
                </td>
                <td class="flex gap-1 px-6 py-4">
                    <Tippy content="Detail application">
                        <button
                            @click="
                                showDetailModal = true;
                                getDetail(item.id);
                            "
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-blue-700 rounded-lg focus:outline-none hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-red-900"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-width="2"
                                    d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"
                                />
                                <path
                                    stroke="currentColor"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <Tippy
                        content="Retry send email"
                        v-if="item.status == 'failed'"
                    >
                        <button
                            type="button"
                            @click="
                                showEditModal = true;
                                getEdit(item.id);
                            "
                            class="p-1 text-sm font-medium text-white bg-green-700 rounded-lg focus:outline-none hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-blue-700 dark:focus:ring-red-900"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <Tippy content="Delete Log">
                        <button
                            type="button"
                            @click="
                                showDeleteModal = true;
                                deleteOne = [item.id];
                            "
                            class="p-1 text-sm font-medium text-white bg-red-700 rounded-lg focus:outline-none hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- retry -->
    <div
        v-if="showEditModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 backdrop-blur-[2px] bg-opacity-40 md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <!-- Modal content -->
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="detailModal"
            >
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Detail Email Logs
                    </h3>
                    <button
                        @click="showEditModal = false"
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal"
                    >
                        <svg
                            class="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5">
                    <div v-show="editFetch" class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                To
                            </label>
                            <input
                                type="hidden"
                                name="secret"
                                id="name"
                                v-model="formRetry.secret"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required="" />
                            <input
                                type="text"
                                name="name"
                                id="name"
                                v-model="formRetry.mail[0].to"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Subject
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                v-model="formRetry.mail[0].subject"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Content</label
                            >
                            <textarea
                                id="description"
                                rows="4"
                                v-model="formRetry.mail[0].content"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"
                            ></textarea>
                        </div>
                        <div
                            class="col-span-2"
                            v-for="(item, index) in formRetry.mail[0]
                                .attachment"
                            :key="index"
                        >
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Attachment
                                {{ index + 1 }}
                            </label>
                            <input
                                type="text"
                                name="name"
                                :id="'name-' + index"
                                v-model="formRetry.mail[0].attachment[index]"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="priority"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Select a Priority</label
                            >
                            <select
                                id="priority"
                                v-model="formRetry.mail[0].priority"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <button
                            @click="handleRetry"
                            type="button"
                            class="text-white w-fit bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        >
                            Submit
                        </button>
                    </div>
                    <div
                        v-show="!editFetch"
                        role="status"
                        class="flex justify-center"
                    >
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- detail -->
    <div
        v-if="showDetailModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 backdrop-blur-[2px] bg-opacity-40 md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <!-- Modal content -->
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="detailModal"
            >
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Detail Email Logs
                    </h3>
                    <button
                        @click="showDetailModal = false"
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal"
                    >
                        <svg
                            class="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5">
                    <div
                        v-show="detailFetch"
                        class="grid grid-cols-2 gap-4 mb-4"
                    >
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                To
                            </label>
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="logDetail.to"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Subject
                            </label>
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="logDetail.subject"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Content</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.content"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"
                            ></textarea>
                        </div>
                        <div
                            class="col-span-2"
                            v-for="(item, index) in logDetail.attachment"
                        >
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Attachment {{ index + 1 }}</label
                            >
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="item"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Status</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.status"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"
                            ></textarea>
                        </div>
                        <div class="col-span-2 mt-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Error Message</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.error_message"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"
                            ></textarea>
                        </div>
                    </div>
                    <div
                        v-show="!detailFetch"
                        role="status"
                        class="flex justify-center"
                    >
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- delete -->
    <div
        tabindex="-1"
        v-show="showDeleteModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="deleteModal"
            >
                <button
                    @click="showDeleteModal = false"
                    type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to delete this application?
                    </h3>
                    <button
                        @click="deleteLog"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <button
                        @click="showDeleteModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    >
                        No, cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
