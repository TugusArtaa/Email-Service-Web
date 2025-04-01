<script setup>
// Import library Vue dan Axios
import { ref, watch } from "vue";
import axios from "axios";

// Props untuk menerima data dari parent component
const props = defineProps({
    show: Boolean,
});

// Emit untuk mengirim event ke parent component
const emit = defineEmits(["close", "success"]);

// State untuk menyimpan file, error, dan elemen input
const selectedFile = ref(null);
const fileInput = ref(null);
const fileError = ref(null);
const formatError = ref(null);
const error = ref("");
const modalExcel = ref(null);

// URL API dari environment variable
const baseUrl = import.meta.env.VITE_APP_URL;

// Fungsi untuk menangani upload file
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const fileExtension = file.name.split(".").pop().toLowerCase();
        const isExcel = ["xlsx", "xls"].includes(fileExtension);
        if (!isExcel) {
            fileError.value = ["Only Excel files (.xlsx, .xls) are allowed."];
            if (fileInput.value) {
                fileInput.value.value = null;
            }
            return;
        }
        fileError.value = null;
        selectedFile.value = file;
    }
}

// Fungsi untuk membatalkan upload file
function cancelFileUpload() {
    selectedFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = null;
    }
    fileError.value = null;
}

// Fungsi untuk mengunggah file ke server
async function uploadFile() {
    if (!selectedFile.value) {
        error.value = "Please select a file first.";
        return;
    }
    const formData = new FormData();
    formData.append("excel_file", selectedFile.value);
    try {
        const response = await axios.post(
            `${baseUrl}/api/email-queue/sendExcel`,
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        emit("success", "File uploaded successfully.");
        selectedFile.value = null;
        emit("close");
        if (fileInput.value) {
            fileInput.value.value = null;
        }
    } catch (error) {
        fileError.value = null;
        formatError.value = null;
        this.error = "Error uploading file.";
        const errMessage = JSON.parse(error.request.response);
        if (
            errMessage.original &&
            errMessage.original.messages &&
            errMessage.original.messages.excel_file
        ) {
            fileError.value = errMessage.original.messages.excel_file;
        } else {
            formatError.value = errMessage.messages;
        }
    }
}

// Fungsi untuk menutup modal
function closeModal() {
    emit("close");
}

// Watcher untuk reset state saat modal ditutup
watch(
    () => props.show,
    (newValue) => {
        if (!newValue) {
            fileError.value = null;
            formatError.value = null;
            error.value = "";
            selectedFile.value = null;
            if (fileInput.value) {
                fileInput.value.value = null;
            }
        }
    }
);
</script>

<template>
    <!-- Modal utama -->
    <div
        v-show="show"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center"
        @click.self="closeModal"
    >
        <div class="w-full max-w-md transform transition-all duration-300">
            <!-- Konten Modal -->
            <div
                class="bg-white rounded-xl shadow-2xl overflow-hidden"
                ref="modalExcel"
            >
                <!-- Modal header -->
                <div
                    class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">
                            Import Excel
                        </h3>
                        <button
                            @click="closeModal"
                            class="text-white/80 hover:text-white transition-colors focus:outline-none"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal body -->
                <div class="p-6">
                    <!-- Bagian progress file -->
                    <div v-if="selectedFile" class="mb-4">
                        <div
                            class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100"
                        >
                            <div
                                class="flex items-center space-x-3 overflow-hidden"
                            >
                                <div
                                    class="flex-shrink-0 p-2 bg-blue-100 rounded-lg"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"
                                        />
                                    </svg>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <span
                                        class="text-sm font-medium text-green-600 truncate block"
                                        >{{ selectedFile.name }}</span
                                    >
                                    <span class="text-xs text-gray-500"
                                        >{{
                                            (
                                                selectedFile.size /
                                                1024 /
                                                1024
                                            ).toFixed(2)
                                        }}
                                        MB</span
                                    >
                                </div>
                            </div>
                            <button
                                @click="cancelFileUpload"
                                class="flex-shrink-0 ml-2 p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors"
                            >
                                <svg
                                    class="w-5 h-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Upload -->
                    <div
                        v-if="!selectedFile"
                        class="p-8 mt-4 mb-4 text-center border-2 border-dashed border-emerald-200 rounded-lg bg-emerald-50 hover:bg-emerald-100 hover:border-emerald-300 transition-all duration-300"
                    >
                        <div class="flex flex-col items-center">
                            <svg
                                class="w-16 h-16 mb-4 text-emerald-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                />
                            </svg>
                            <span class="text-sm font-medium text-gray-600 mb-2"
                                >Upload file excel anda disini (.xlsx,
                                .xls)</span
                            >
                            <label
                                for="file_input"
                                class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors duration-300"
                            >
                                Pilih File
                            </label>
                            <input
                                @change="handleFileUpload"
                                class="hidden"
                                id="file_input"
                                type="file"
                                ref="fileInput"
                                accept=".xlsx, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            />
                        </div>
                    </div>

                    <!-- Error Messages -->
                    <div
                        v-if="fileError || formatError"
                        class="mb-4 p-4 bg-red-50 border border-red-100 rounded-lg"
                    >
                        <div class="flex items-center space-x-2 mb-2">
                            <svg
                                class="w-5 h-5 text-red-500"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span class="font-medium text-red-800"
                                >Please fix the following errors:</span
                            >
                        </div>
                        <ul v-if="fileError" class="ml-5 space-y-1">
                            <li
                                v-for="err in fileError"
                                class="text-sm text-red-600 list-disc"
                            >
                                {{ err }}
                            </li>
                        </ul>
                        <div v-if="formatError" class="space-y-3">
                            <div v-for="err in formatError" class="ml-1">
                                <p class="text-sm font-medium text-red-700">
                                    Error excel on row number {{ err.row }}
                                </p>
                                <ul class="ml-5 space-y-1">
                                    <li
                                        v-for="message in err.errors"
                                        class="text-sm text-red-600 list-disc"
                                    >
                                        {{ message }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6">
                        <button
                            @click="uploadFile"
                            :disabled="!selectedFile"
                            class="px-4 py-2 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-300"
                        >
                            Upload
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
