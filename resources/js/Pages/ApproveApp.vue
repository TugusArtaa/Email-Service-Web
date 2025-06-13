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
            class="px-8 py-6 mb-8 bg-gradient-to-br from-[#019966] via-[#018860] to-[#017755] rounded-3xl shadow-lg flex items-center justify-between relative overflow-hidden"
        >
            <div
                class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMwLTkuOTQtOC4wNi0xOC0xOC0xOFYwYzkuOTQgMCAxOCA4LjA2IDE4IDE4aDEyYzAgOS45NCA4LjA2IDE4IDE4IDE4djEyYy05Ljk0IDAtMTgtOC4wNi0xOC0xOEgzNnptLTE4IDM2YzkuOTQgMCAxOCA4LjA2IDE4IDE4aDE4YzAgOS45NC04LjA2IDE4LTE4IDE4djEyYzkuOTQgMCAxOC04LjA2IDE4LTE4aDEyYzAgOS45NCA4LjA2IDE4IDE4IDE4VjcyYy05Ljk0IDAtMTgtOC4wNi0xOC0xOEgxOGMwLTkuOTQtOC4wNi0xOC0xOC0xOHYtMTJjOS45NCAwIDE4IDguMDYgMTggMTh6IiBmaWxsPSIjZmZmIi8+PC9nPjwvc3ZnPg==')]"
            ></div>
            <div class="flex items-center z-10">
                <div class="bg-white/10 p-2 rounded-2xl backdrop-blur-sm mr-8">
                    <img
                        :src="'/ApproveLogo.png'"
                        alt="Logo"
                        class="h-20 w-20"
                    />
                </div>
                <div>
                    <h1
                        class="text-4xl font-bold text-white tracking-tight drop-shadow-md"
                    >
                        Daftar Aplikasi yang Menunggu Persetujuan
                    </h1>
                    <div class="flex items-center mt-3">
                        <span
                            class="w-10 h-1 bg-emerald-300 rounded-full mr-3"
                        ></span>
                        <p class="text-lg text-emerald-50 font-medium">
                            Tinjau dan setujui aplikasi yang mengajukan
                            pendaftaran ke sistem
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="absolute top-0 right-0 h-full w-24 bg-white/5 -skew-x-12"
            ></div>
        </div>
        <!-- Tabel -->
        <TableApprove
            :data="applications"
            :thead="thead"
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
                        Anda yakin ingin menyetujui permohonan ini?
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
                        Anda yakin ingin menolak permohonan ini?
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

<script setup>
import { ref, watch, onMounted } from "vue";
import * as ApproveAPI from "../api/ApproveAPI";
import Layout from "./Layout.vue";
import TableApprove from "../components/TableApprove.vue";
import TablePagination from "../components/TablePagination.vue";
import { Head } from "@inertiajs/vue3";
import { onClickOutside, useStorage } from "@vueuse/core";
import NotificationToast from "../components/NotificationToast.vue";

// URL dasar untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// Menyimpan urutan dan arah urutan di local storage
const orderBy = useStorage("orderBy", "created_at");
const orderDirection = useStorage("orderDirection", "desc");

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

const isFetching = ref(false);

// Refactor: fetch data via API service
async function fetchApproveApplications() {
    isFetching.value = true;
    try {
        const response = await ApproveAPI.fetchApproveApplications({
            orderBy: orderBy.value,
            orderDirection: orderDirection.value,
            search: search.value,
            page: page.value,
        });
        applications.value = response.data.applications ||
            response.data.data?.applications || { data: [] };
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal Memuat Data!",
            description:
                error.response?.data?.message ||
                "Terjadi kesalahan saat menyegarkan data.",
        };
    } finally {
        isFetching.value = false;
    }
}

// Inisialisasi data saat komponen dimuat
fetchApproveApplications();

// Bersihkan data-page pada elemen #app setelah SPA ter-hydrate
onMounted(() => {
    const appDiv = document.getElementById("app");
    if (appDiv) {
        appDiv.removeAttribute("data-page");
    }
});

// Fungsi untuk menangani perubahan halaman
const handlePageChange = (newPage) => {
    if (newPage === applications.value.current_page) return;
    page.value = newPage;
    fetchApproveApplications();
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
        const response = await ApproveAPI.approveApplication(
            selectedApplication.value.id
        );
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: response.data.message,
        };
        showApproveModal.value = false;
        fetchApproveApplications();
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: error.response?.data?.message || "Terjadi kesalahan",
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
        const response = await ApproveAPI.changeApplicationStatus(
            selectedApplication.value.id,
            "rejected"
        );
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: response.data.message,
        };
        showRejectModal.value = false;
        fetchApproveApplications();
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: error.response?.data?.message || "Terjadi kesalahan",
        };
    }
};

// Fungsi untuk menyegarkan data aplikasi
const refreshData = () => {
    fetchApproveApplications();
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
