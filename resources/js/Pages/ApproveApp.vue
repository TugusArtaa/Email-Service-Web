<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import Layout from "./Layout.vue";
import TableApprove from "../components/TableApprove.vue";
import TablePagination from "../components/TablePagination.vue";
import { Head } from "@inertiajs/vue3";
import { useFetch, onClickOutside, useStorage } from "@vueuse/core";

// Mendefinisikan URL dasar untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// Menyimpan urutan dan arah urutan di local storage
const orderBy = useStorage("orderBy", "created_at");
const orderDirection = useStorage("orderDirection", "desc");

// Mendefinisikan URL untuk permintaan data aplikasi approve
const url = ref(
    `${baseUrl}/api/applications/approve?orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`
);

// Mendefinisikan variabel reaktif untuk menyimpan data aplikasi
const applications = ref({ data: [] });

// Variabel untuk pencarian, halaman, dan checkbox yang dipilih
const search = ref("");
const page = ref(1);

// Variabel untuk modal approve aplikasi
const approveModal = ref(null);
const rejectModal = ref(null);
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const selectedApplication = ref({});
const alertMessage = ref("");
const alertType = ref("");

// Mengambil data dari API
const { isFetching, data } = useFetch(url, { refetch: true }).get().json();

// Mengawasi perubahan data dan memperbarui variabel applications
watch(data, (newData) => {
    if (newData) {
        applications.value = newData.data.applications;
    }
});

// Fungsi untuk membersihkan pesan alert
function clearAlert() {
    alertMessage.value = "";
    alertType.value = "";
}

// Fungsi untuk menangani perubahan halaman
const handlePageChange = (page) => {
    if (page === applications.value.current_page) {
        return;
    }
    url.value = `${baseUrl}/api/applications/approve?page=${page}&search=${search.value}&orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`;
    applications.value = { data: [] };
};

// Menutup modal approve saat klik di luar modal
onClickOutside(approveModal, () => {
    showApproveModal.value = false;
});

// Menutup modal reject saat klik di luar modal
onClickOutside(rejectModal, () => {
    showRejectModal.value = false;
});

// Fungsi untuk membuka modal approve
const openApproveModal = (application) => {
    selectedApplication.value = application;
    showApproveModal.value = true;
};

// Fungsi untuk mengirim permintaan approve aplikasi
const approveApplication = async () => {
    try {
        const response = await axios.post(
            `${baseUrl}/api/applications/approve-application`,
            {
                id: selectedApplication.value.id,
            }
        );
        alertMessage.value = response.data.message;
        alertType.value = "success";
        showApproveModal.value = false;
        refreshData();
        setTimeout(clearAlert, 4000);
    } catch (error) {
        alertMessage.value = error.response.data.message || "An error occurred";
        alertType.value = "danger";
        setTimeout(clearAlert, 4000);
    }
};

// Fungsi untuk membuka modal reject
const openRejectModal = (application) => {
    selectedApplication.value = application;
    showRejectModal.value = true;
};

// Fungsi untuk mengirim permintaan reject aplikasi
const rejectApplication = async () => {
    try {
        const response = await axios.post(
            `${baseUrl}/api/applications/application-status-change`,
            {
                id: selectedApplication.value.id,
                status: "rejected",
            }
        );
        alertMessage.value = response.data.message;
        alertType.value = "success";
        showRejectModal.value = false;
        refreshData();
        setTimeout(clearAlert, 4000);
    } catch (error) {
        alertMessage.value = error.response.data.message || "An error occurred";
        alertType.value = "danger";
        setTimeout(clearAlert, 4000);
    }
};

// Fungsi untuk menyegarkan data aplikasi
const refreshData = async () => {
    try {
        const response = await axios.get(url.value);
        applications.value = response.data.data.applications;
    } catch (error) {
        console.error("Error refreshing data:", error);
    }
};

// Header tabel
const thead = ref([
    "No",
    "Application name",
    "PIC Name",
    "Created At",
    "Status",
    "Actions",
]);
</script>

<template>
    <!-- head -->
    <Head>
        <title>Approve Aplikasi</title>
    </Head>
    <Layout>
        <div
            class="relative w-full px-3 py-5 overflow-x-auto bg-white shadow-md sm:rounded-lg"
        >
            <!-- Judul -->
            <h3 class="mb-3 text-2xl font-bold dark:text-white">
                Daftar Approve
            </h3>
            <!-- Alert -->
            <div
                v-if="alertMessage"
                :class="`flex items-center p-4 mb-4 text-sm border rounded-lg ${
                    alertType === 'success'
                        ? 'text-green-800 border-green-300 bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800'
                        : 'text-red-800 border-red-300 bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800'
                }`"
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
                    <span class="font-medium">{{
                        alertType === "success"
                            ? "Success alert!"
                            : "Danger alert!"
                    }}</span>
                    {{ alertMessage }}
                </div>
            </div>
            <br />
            <!-- Tabel -->
            <TableApprove
                :data="applications"
                :thead="thead"
                :title="'Daftar Approve'"
                :isFetching="isFetching"
                :page="page"
                @approve="openApproveModal"
                @reject="openRejectModal"
                @refresh="refreshData"
            />
            <!-- Tabel footer -->
            <TablePagination
                :current="applications.current_page"
                :from="applications.from"
                :to="applications.to"
                :total="applications.total"
                :last="applications.last_page"
                @page-change="handlePageChange"
            />
        </div>
    </Layout>

    <!-- Modal Approve -->
    <div
        tabindex="-1"
        v-show="showApproveModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800"
                ref="approveModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showApproveModal = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-4 h-4"
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
                <!-- Isi modal -->
                <div class="p-6 text-center">
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
                    <!-- Judul modal -->
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to approve this?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="approveApplication"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showApproveModal = false"
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
    <!-- Modal Reject -->
    <div
        tabindex="-1"
        v-show="showRejectModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800"
                ref="rejectModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showRejectModal = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-4 h-4"
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
                <!-- Isi modal -->
                <div class="p-6 text-center">
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
                    <!-- Judul modal -->
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to reject this?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="rejectApplication"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showRejectModal = false"
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
