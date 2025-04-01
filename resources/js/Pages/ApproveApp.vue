<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import Layout from "./Layout.vue";
import TableApprove from "../components/TableApprove.vue";
import TablePagination from "../components/TablePagination.vue";
import { Head } from "@inertiajs/vue3";
import { useFetch, onClickOutside, useStorage } from "@vueuse/core";
import NotificationToast from "../components/NotificationToast.vue";

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

// Variabel untuk toast notification
const notification = ref({
    show: false,
    type: "",
    message: "",
    description: "",
});

// Mengambil data dari API
const { isFetching, data } = useFetch(url, { refetch: true }).get().json();

// Mengawasi perubahan data dan memperbarui variabel applications
watch(data, (newData) => {
    if (newData) {
        applications.value = newData.data.applications;
    }
});

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
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: response.data.message,
        };
        showApproveModal.value = false;
        refreshData();
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: error.response.data.message || "Terjadi kesalahan",
        };
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
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: response.data.message,
        };
        showRejectModal.value = false;
        refreshData();
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: error.response.data.message || "Terjadi kesalahan",
        };
    }
};

// Fungsi untuk menyegarkan data aplikasi
const refreshData = async () => {
    try {
        const response = await axios.get(url.value);
        applications.value = response.data.data.applications;
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal Memuat Data!",
            description:
                error.response?.data?.message ||
                "Terjadi kesalahan saat menyegarkan data.",
        };
    }
};

// Header tabel
const thead = ref([
    "No",
    "Nama Aplikasi",
    "Nama PIC",
    "Dibuat Pada",
    "Status",
    "Aksi",
]);
</script>

<template>
    <!-- head -->
    <Head>
        <title>Approve Aplikasi</title>
    </Head>
    <Layout>
        <!-- Toast Notification -->
        <NotificationToast
            :notification="notification"
            @close="notification.show = false"
        />

        <!-- Header -->
        <div
            class="px-6 py-8 mb-6 bg-gradient-to-r from-[#019966] to-[#017755] rounded-2xl shadow-xl flex justify-between items-center"
        >
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Daftar Aplikasi yang Menunggu Persetujuan
                </h1>
                <p class="mt-2 text-indigo-100">
                    Tinjau dan setujui aplikasi yang mengajukan pendaftaran ke
                    sistem
                </p>
            </div>
        </div>
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
    </Layout>

    <!-- Modal Approve -->
    <div
        tabindex="-1"
        v-show="showApproveModal"
        class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow-lg"
                ref="approveModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showApproveModal = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center"
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
                    <span class="sr-only">Tutup modal</span>
                </button>
                <!-- Isi modal -->
                <div class="p-6 text-center">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-green-600"
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
                    <h3 class="mb-5 text-lg font-normal text-gray-500">
                        Apakah Anda yakin ingin menyetujui ini?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="approveApplication"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Ya, Saya yakin
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showApproveModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-950 focus:z-10 focus:ring-4 focus:ring-gray-100"
                    >
                        Tidak, Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penolakan -->
    <div
        tabindex="-1"
        v-show="showRejectModal"
        class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow-lg"
                ref="rejectModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showRejectModal = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center"
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
                    <span class="sr-only">Tutup modal</span>
                </button>
                <!-- Isi modal -->
                <div class="p-6 text-center">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-red-600"
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
                    <h3 class="mb-5 text-lg font-normal text-gray-500">
                        Apakah Anda yakin ingin menolak ini?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="rejectApplication"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Ya, Saya yakin
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showRejectModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-950 focus:z-10 focus:ring-4 focus:ring-gray-100"
                    >
                        Tidak, Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
