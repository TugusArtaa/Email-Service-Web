<script setup>
// Import komponen dan library yang diperlukan
import Layout from "./Layout.vue";
import { ref, watch } from "vue";
import Table from "../components/TableIntegrasi.vue";
import Pagination from "../components/TablePagination.vue";
import Search from "../components/Search.vue";
import AddModal from "../components/ModalAddEmail.vue";
import ModalImportExcel from "../components/ModalImportExcel.vue";
import NotificationToast from "../components/NotificationToast.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { useFetch, onClickOutside, useStorage } from "@vueuse/core";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";

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
    form.ids = checked;
};

// Fungsi untuk memuat ulang data
function refreshData(page = 1) {
    currentPage.value = page;
    const { data, isFetching } = useFetch(
        `${baseUrl}/api/email-logs?orderBy=${orderBy.value}&search=${search.value}&page=${page}&date=${date.value}`,
        { refetch: true }
    )
        .get()
        .json();
    watch(data, (newData) => {
        if (newData) {
            logs.value = [];
            logs.value = newData.data.emailLogs;
        }
    });
    watch(isFetching, (newIsFetching) => {
        fetchData.value = newIsFetching;
    });
}

// Fungsi untuk memuat ulang data tanpa loading
function refreshDataWithoutFetchLoad() {
    const { data } = useFetch(
        `${baseUrl}/api/email-logs?orderBy=${orderBy.value}&search=${search.value}&page=${currentPage.value}`,
        { refetch: true }
    )
        .get()
        .json();
    watch(data, (newData) => {
        if (newData) {
            logs.value = [];
            logs.value = newData.data.emailLogs;
        }
    });
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
    if (form.ids.length > 0) {
        selectedIdsCount.value = form.ids.length;
        showDeleteModal.value = true;
    } else {
        showNotification("error", "Tidak ada data yang dipilih!");
    }
}

// Fungsi untuk menangani pesan sukses
function handleSuccessMessage(message) {
    success.value = message;
    clearAlert();
    showNotification("success", message);
}

// Fungsi untuk menampilkan notifikasi
const showNotification = (type, message, description = "") => {
    notification.value = {
        show: true,
        type,
        message,
        description,
    };
    setTimeout(() => {
        notification.value.show = false;
    }, 3000);
};

// Fungsi Cleartalert
function clearAlert() {
    setTimeout(() => {
        success.value = "";
        error.value = "";
    }, 5000);
}

//Fungsi Delete Checkbox
function confirmDelete() {
    form.delete(`${baseUrl}/integrasi/delete`, {
        onSuccess: () => {
            showNotification("success", "Data berhasil dihapus!");
            form.ids = [];
            refreshData();
            showDeleteModal.value = false;
        },
        onError: () => {
            showNotification("error", "Gagal menghapus data!");
        },
    });
}

// Pantau perubahan pada error dan tampilkan notifikasi jika ada
watch(error, (newError) => {
    if (newError) {
        showNotification("error", newError);
    }
});

// Inisialisasi data saat komponen dimuat
refreshData();
</script>

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
            class="px-6 py-8 mb-6 bg-gradient-to-r from-[#019966] to-[#017755] rounded-2xl shadow-xl flex justify-between items-center"
        >
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Aktivitas Pengiriman Email
                </h1>
                <p class="mt-2 text-indigo-100">
                    Cek status, kelola, dan pantau pengiriman email secara
                    real-time
                </p>
            </div>
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
            :thead="thead"
            :fetch="fetchData"
            :logs="logs"
            @checkbox="handleCheckbox"
            @refresh="handleRefresh"
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
            class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center"
            @click.self="showButtonsModal = false"
        >
            <div
                class="w-full max-w-md transform transition-all duration-300 ease-in-out"
                :class="
                    showButtonsModal
                        ? 'scale-100 opacity-100'
                        : 'scale-95 opacity-0'
                "
            >
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                    <div
                        class="flex items-center justify-between p-5 bg-gradient-to-r from-emerald-500 to-teal-500"
                    >
                        <h3 class="text-xl font-bold text-white">
                            Pilih Opsi Pengiriman
                        </h3>
                        <button
                            @click="showButtonsModal = false"
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
                    <br />
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <button
                                type="button"
                                @click="
                                    addModal = true;
                                    showButtonsModal = false;
                                "
                                class="flex-1 flex flex-col items-center justify-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg focus:ring-4 focus:ring-green-300 focus:outline-none transform hover:-translate-y-0.5"
                            >
                                <svg
                                    class="h-6 w-6 mb-2 text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5c4.1.3 7.2 3.4 7.5 7.5z"
                                    />
                                </svg>
                                Email Baru
                            </button>
                            <button
                                type="button"
                                @click="
                                    addExcel = true;
                                    showButtonsModal = false;
                                "
                                class="flex-1 flex flex-col items-center justify-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg focus:ring-4 focus:ring-blue-300 focus:outline-none transform hover:-translate-y-0.5"
                            >
                                <svg
                                    class="h-6 w-6 mb-2 text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                    />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="8" y1="13" x2="16" y2="13" />
                                    <line x1="8" y1="17" x2="16" y2="17" />
                                    <line x1="10" y1="9" x2="14" y2="9" />
                                </svg>
                                File Excel
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <button
                            @click="showButtonsModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-200 focus:outline-none transition-colors duration-200"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal add excel -->
        <ModalImportExcel
            v-show="addExcel"
            @close="handleCloseExcelModal"
            @success="handleSuccessMessage"
        />

        <!-- Modal add email -->
        <AddModal
            v-show="addModal"
            @close="handleCloseAddModal"
            @success="handleSuccessMessage"
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
                            Apakah anda yakin ingin menghapus
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
