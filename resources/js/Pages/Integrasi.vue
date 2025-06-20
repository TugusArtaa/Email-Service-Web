<template>
    <Head>
        <title>Manajemen Email</title>
    </Head>
    <Layout>
        <!-- Notification Toast -->
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
                    <img :src="'/EmailLogo.png'" alt="Logo" class="h-20 w-20" />
                </div>
                <div>
                    <h1
                        class="text-4xl font-bold text-white tracking-tight drop-shadow-md"
                    >
                        Aktivitas Pengiriman Email
                    </h1>
                    <div class="flex items-center mt-3">
                        <span
                            class="w-10 h-1 bg-emerald-300 rounded-full mr-3"
                        ></span>
                        <p class="text-lg text-emerald-50 font-medium">
                            Cek status, kelola, dan pantau pengiriman email
                            secara real-time
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="absolute top-0 right-0 h-full w-24 bg-white/5 -skew-x-12"
            ></div>
        </div>

        <!-- Header Tabel -->
        <section class="flex items-center mb-5">
            <div class="w-full mx-auto">
                <div class="relative w-full sm:rounded-lg">
                    <div
                        class="flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4"
                    >
                        <div class="w-full md:w-1/2">
                            <!-- Component search -->
                            <Search @search="handleSearch" />
                        </div>
                        <div
                            class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                        >
                            <button
                                type="button"
                                @click="showButtonsModal = true"
                                class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 focus:outline-none"
                            >
                                <svg
                                    class="w-5 h-5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 5v14m-7-7h14"
                                    />
                                </svg>
                            </button>
                            <div
                                class="flex items-center w-full space-x-3 md:w-auto"
                            >
                                <button
                                    @click="handleDeleteCheckbox"
                                    id="actionsDropdownButton"
                                    data-dropdown-toggle="actionsDropdown"
                                    :disabled="form.ids.length <= 1"
                                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-700 border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    type="button"
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
                                            stroke="white"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                        />
                                    </svg>
                                </button>
                                <div class="relative">
                                    <button
                                        ref="btnOrder"
                                        id="filterDropdownButton"
                                        data-dropdown-toggle="filterDropdown"
                                        @click="orderDropdown = !orderDropdown"
                                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-gray-700 focus:z-10 focus:ring-4 focus:ring-gray-200"
                                        type="button"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2 text-gray-800"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3"
                                            />
                                        </svg>
                                        Urutkan
                                        <svg
                                            :class="
                                                orderDropdown
                                                    ? 'rotate-180'
                                                    : ''
                                            "
                                            class="-mr-1 ml-1.5 w-5 h-5"
                                            fill="currentColor"
                                            viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                            aria-hidden="true"
                                        >
                                            <path
                                                clip-rule="evenodd"
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            />
                                        </svg>
                                    </button>
                                    <div
                                        id="actionsDropdown"
                                        v-show="orderDropdown"
                                        ref="modalOrder"
                                        class="absolute left-0 right-0 z-10 w-full overflow-hidden bg-white divide-y divide-gray-100 rounded shadow top-11"
                                    >
                                        <div class="py-1">
                                            <button
                                                href="#"
                                                @click="
                                                    orderBy = 'desc';
                                                    refreshData();
                                                "
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy === 'desc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
                                            >
                                                Tgl (baru-lama)
                                            </button>
                                            <button
                                                href="#"
                                                @click="
                                                    orderBy = 'asc';
                                                    refreshData();
                                                "
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy === 'asc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
                                            >
                                                Tgl (lama-baru)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Body/ isi Tabel -->
        <Table
            :key="
                logs.current_page + '-' + logs.total + '-' + logs.data?.length
            "
            :thead="thead"
            :logs="logs"
            :fetch="fetchData"
            :search="search"
            @checkbox="handleCheckbox"
            @refresh="refreshData"
            @notification="handleNotification"
        />

        <!-- Tabel Footer -->
        <Pagination
            @page-change="handlePageChange"
            :current="logs.current_page"
            :last="logs.last_page"
            :from="logs.from"
            :to="logs.to"
            :total="logs.total"
        />

        <!-- Modal untuk opsi pengiriman -->
        <div
            v-show="showButtonsModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="showButtonsModal = false"
        >
            <div
                class="w-full max-w-2xl transform transition-all duration-300 ease-in-out"
                :class="
                    showButtonsModal
                        ? 'scale-100 opacity-100'
                        : 'scale-95 opacity-0'
                "
            >
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Modal header -->
                    <div
                        class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 relative overflow-hidden"
                    >
                        <div class="flex items-center justify-between relative">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Opsi Pengiriman
                                    </h3>
                                    <p class="text-white/80 text-sm">
                                        Pilih metode pengiriman email
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="showButtonsModal = false"
                                class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-xl transition-all duration-200 focus:outline-none"
                            >
                                <svg
                                    class="w-5 h-5"
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
                        <!-- Info section -->
                        <div
                            class="mb-4 p-4 bg-amber-50 rounded-xl border border-amber-200"
                        >
                            <div
                                class="flex items-center justify-center space-x-2 text-sm text-gray-600"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span
                                    >Pilih metode yang sesuai dengan kebutuhan
                                    pengiriman Anda</span
                                >
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <!-- Email Baru Option -->
                            <button
                                type="button"
                                @click="
                                    addModal = true;
                                    showButtonsModal = false;
                                "
                                class="flex-1 group relative overflow-hidden bg-gradient-to-br from-emerald-200 to-teal-200 border-2 border-emerald-200 hover:border-emerald-300 rounded-2xl p-3 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-200"
                            >
                                <div class="relative z-10 mt-4">
                                    <div
                                        class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg"
                                    >
                                        <svg
                                            class="w-10 h-10 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                    <h4
                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-700 transition-colors duration-300"
                                    >
                                        Email Baru
                                    </h4>
                                    <p
                                        class="text-sm text-gray-600 leading-relaxed mb-2"
                                    >
                                        Kirim satu email secara manual
                                    </p>

                                    <!-- Feature badges -->
                                    <div
                                        class="flex justify-center space-x-2 mb-2"
                                    >
                                        <span
                                            class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full"
                                            >Manual</span
                                        >
                                        <span
                                            class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full"
                                            >Form</span
                                        >
                                    </div>
                                </div>
                            </button>

                            <!-- File Excel Option -->
                            <button
                                type="button"
                                @click="
                                    addExcel = true;
                                    showButtonsModal = false;
                                "
                                class="flex-1 group relative overflow-hidden bg-gradient-to-br from-emerald-200 to-teal-200 border-2 border-emerald-200 hover:border-emerald-300 rounded-2xl p-3 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-200"
                            >
                                <div class="relative z-10 mt-4">
                                    <div
                                        class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg"
                                    >
                                        <svg
                                            class="w-10 h-10 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>
                                    <h4
                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-700 transition-colors duration-300"
                                    >
                                        Import Excel
                                    </h4>
                                    <p
                                        class="text-sm text-gray-600 leading-relaxed mb-2"
                                    >
                                        Unggah Excel untuk kirim massal
                                    </p>

                                    <!-- Feature badges -->
                                    <div
                                        class="flex justify-center space-x-2 mb-2"
                                    >
                                        <span
                                            class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full"
                                            >Batch</span
                                        >
                                        <span
                                            class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full"
                                            >Massal</span
                                        >
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-end">
                            <button
                                @click="showButtonsModal = false"
                                class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 hover:border-gray-400 hover:shadow-md rounded-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-200"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal add excel -->
        <ModalImportExcel
            v-show="addExcel"
            @close="handleCloseExcelModal"
            @notification="handleNotification"
        />

        <!-- Modal add email -->
        <AddModal
            v-show="addModal"
            @close="handleCloseAddModal"
            @notification="handleNotification"
        />

        <!-- Modal Hapus Checkbox -->
        <div
            tabindex="-1"
            v-show="showDeleteModal"
            class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
        >
            <div class="relative w-full max-w-md max-h-full p-4">
                <div class="relative bg-white rounded-lg shadow">
                    <button
                        @click="showDeleteModal = false"
                        type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
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
                        <span class="sr-only">Keluar</span>
                    </button>
                    <div class="p-4 text-center md:p-5">
                        <svg
                            class="w-12 h-12 mx-auto mb-4 text-red-700"
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
                        <h3 class="mb-5 text-lg font-normal text-gray-500">
                            Anda yakin ingin menghapus
                            <strong>{{ selectedIdsCount }}</strong> log
                            pengiriman email ini?
                        </h3>
                        <button
                            @click="confirmDelete"
                            type="button"
                            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                        >
                            Ya, Saya yakin
                        </button>
                        <button
                            @click="showDeleteModal = false"
                            type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-950 focus:z-10 focus:ring-4 focus:ring-gray-100"
                        >
                            Tidak, Batalkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
// Import komponen dan library yang diperlukan
import Layout from "./Layout.vue";
import { ref, watch, onMounted } from "vue";
import Table from "../components/TableIntegrasi.vue";
import Pagination from "../components/TablePagination.vue";
import Search from "../components/Search.vue";
import AddModal from "../components/ModalAddEmail.vue";
import ModalImportExcel from "../components/ModalImportExcel.vue";
import NotificationToast from "../components/NotificationToast.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { onClickOutside, useStorage } from "@vueuse/core";
import * as IntegrasiAPI from "../api/IntegrasiAPI";

// State dan variabel reaktif
const baseUrl = import.meta.env.VITE_APP_URL;
const success = ref("");
const error = ref("");
const thead = ref([
    "",
    "Nama Aplikasi",
    "Email Penerima",
    "Subjek",
    "Status",
    "Tanggal",
    "Aksi",
]);
const logs = ref([]);
const fetchData = ref(false);
const currentPage = ref(1);
const search = ref("");
const orderBy = useStorage("integrasi-orderBy", "desc");
const date = ref("");
const addModal = ref(false);
const orderDropdown = ref(false);
const modalOrder = ref(null);
const showButtonsModal = ref(false);
const btnOrder = ref(null);
const addExcel = ref(false);
const showDeleteModal = ref(false);
const selectedIdsCount = ref(0);
const pageInertia = usePage();
const form = useForm({
    ids: [],
    _token: pageInertia.props.csrf_token,
});
const formDate = useForm({
    start_date: "",
    end_date: "",
    _token: pageInertia.props.csrf_token,
});
const notification = ref({
    show: false,
    type: "",
    message: "",
    description: "",
});

// Fungsi untuk menutup modal
function handleCloseAddModal() {
    addModal.value = false;
}
function handleCloseExcelModal() {
    addExcel.value = false;
}

// Fungsi untuk menutup dropdown jika klik di luar
onClickOutside(modalOrder, (event) => {
    if (event.target !== btnOrder.value) {
        orderDropdown.value = false;
    }
});

// Fungsi untuk menangani perubahan halaman
const handlePageChange = (page) => {
    if (page === logs.value.current_page) {
        return;
    }
    refreshData(page);
};

// Fungsi untuk menangani pencarian
const handleSearch = (query) => {
    search.value = query;
    refreshData();
};

// Fungsi untuk menangani checkbox
const handleCheckbox = (checked) => {
    // Sinkronkan form.ids dengan checked
    form.ids = [...checked];
    // Reset selectedIdsCount agar selalu sesuai dengan jumlah terpilih saat ini
    selectedIdsCount.value = form.ids.length;
};

// Fungsi untuk memuat ulang data
async function refreshData(page = 1) {
    currentPage.value = page;
    fetchData.value = true;
    try {
        const response = await IntegrasiAPI.fetchEmailLogs({
            orderBy: orderBy.value,
            search: search.value,
            page,
            date: date.value,
        });
        logs.value = response.data.emailLogs;
    } catch (err) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: err.message || "Gagal memuat data!",
        };
    } finally {
        fetchData.value = false;
    }
}

// Fungsi untuk memuat ulang data tanpa loading
async function refreshDataWithoutFetchLoad() {
    try {
        const response = await IntegrasiAPI.fetchEmailLogs({
            orderBy: orderBy.value,
            search: search.value,
            page: currentPage.value,
            date: date.value,
        });
        logs.value = response.data.emailLogs;
    } catch (err) {}
}

// Interval untuk memuat ulang data setiap 2 detik
setInterval(() => {
    refreshDataWithoutFetchLoad();
}, 2000);

// Fungsi untuk memuat ulang data secara manual
function handleRefresh() {
    refreshData();
}

// Fungsi untuk menghapus data yang dipilih
function handleDeleteCheckbox() {
    // Hitung ulang selectedIdsCount berdasarkan form.ids saat tombol delete diklik
    selectedIdsCount.value = form.ids.length;
    if (form.ids.length > 0) {
        showDeleteModal.value = true;
    } else {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: "Tidak ada data yang dipilih!",
        };
    }
}

//Fungsi Delete Checkbox
async function confirmDelete() {
    try {
        await IntegrasiAPI.deleteEmailLogs(form.ids, baseUrl);
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: "Data berhasil dihapus!",
        };
        // Reset checkbox state & counter setelah hapus
        form.ids = [];
        selectedIdsCount.value = 0;
        await refreshData();
        showDeleteModal.value = false;
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description:
                error?.response?.data?.message ||
                error.message ||
                "Gagal menghapus data!",
        };
    }
}

//Untuk Notification
function handleNotification(type, message, description = "") {
    notification.value = {
        show: true,
        type,
        message,
        description,
    };
}

// Pantau perubahan pada error dan tampilkan notifikasi jika ada
watch(error, (newError) => {
    if (newError) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description: newError,
        };
    }
});

// Inisialisasi data saat komponen dimuat
refreshData();

// Bersihkan data-page pada elemen
onMounted(() => {
    const appDiv = document.getElementById("app");
    if (appDiv) {
        appDiv.removeAttribute("data-page");
    }
});
</script>
