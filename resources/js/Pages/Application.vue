<template>
    <Head>
        <title>Manajemen aplikasi</title>
    </Head>
    <Layout>
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
                        :src="'/AplikasiLogo.png'"
                        alt="Logo"
                        class="h-20 w-20"
                    />
                </div>
                <div>
                    <h1
                        class="text-4xl font-bold text-white tracking-tight drop-shadow-md"
                    >
                        Daftar Aplikasi Terintegrasi
                    </h1>
                    <div class="flex items-center mt-3">
                        <span
                            class="w-10 h-1 bg-emerald-300 rounded-full mr-3"
                        ></span>
                        <p class="text-lg text-emerald-50 font-medium">
                            Monitor, tinjau, dan kelola aplikasi yang terhubung
                            dengan sistem ini
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="absolute top-0 right-0 h-full w-24 bg-white/5 -skew-x-12"
            ></div>
        </div>
        <!-- Notification Toast -->
        <NotificationToast
            :notification="notification"
            @close="notification.show = false"
        />
        <!-- Header tabel -->
        <section class="flex items-center mb-5">
            <div class="w-full mx-auto">
                <div class="relative w-full sm:rounded-lg">
                    <div
                        class="flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4"
                    >
                        <div class="w-full md:w-1/2">
                            <!-- Komponen search -->
                            <Search @search="handleSearch" />
                        </div>
                        <div
                            class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3"
                        >
                            <!-- Tombol aksi -->
                            <button
                                type="button"
                                @click="showAddModal = true"
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
                                <!-- Tombol hapus -->
                                <button
                                    @click="handleDeleteCheckbox"
                                    id="actionsDropdownButton"
                                    data-dropdown-toggle="actionsDropdown"
                                    :disabled="form.ids.length <= 1"
                                    class="flex items-center justify-center w-full px-4 py-2 bg-red-700 border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 focus:z-10 focus:ring-4 focus:ring-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
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
                                    <!-- Dropdown Urutkan-->
                                    <button
                                        ref="btnOrder"
                                        data-dropdown-toggle="filterDropdown"
                                        @click="orderDropdown = !orderDropdown"
                                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-green-700 focus:z-10 focus:ring-4 focus:ring-gray-200"
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
                                    <!-- Dropdown Urutkan -->
                                    <div
                                        v-show="orderDropdown"
                                        ref="modalOrder"
                                        class="absolute left-0 right-0 z-10 w-full overflow-hidden bg-white divide-y divide-gray-100 rounded shadow top-11"
                                    >
                                        <div class="py-1">
                                            <button
                                                href="#"
                                                @click="
                                                    order('created_at', 'desc')
                                                "
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy ===
                                                            'created_at' &&
                                                        orderDirection ===
                                                            'desc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
                                            >
                                                Tgl (baru-lama)
                                            </button>
                                            <button
                                                href="#"
                                                @click="
                                                    order('created_at', 'asc')
                                                "
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy ===
                                                            'created_at' &&
                                                        orderDirection ===
                                                            'asc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
                                            >
                                                Tgl (lama-baru)
                                            </button>
                                            <button
                                                href="#"
                                                @click="order('name', 'asc')"
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy === 'name' &&
                                                        orderDirection ===
                                                            'asc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            >
                                                Aplikasi (A-Z)
                                            </button>
                                            <button
                                                href="#"
                                                @click="order('name', 'desc')"
                                                :class="{
                                                    'text-green-700 bg-green-200 hover:bg-green-300':
                                                        orderBy === 'name' &&
                                                        orderDirection ===
                                                            'desc',
                                                }"
                                                class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            >
                                                Aplikasi (Z-A)
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
        <!-- Tabel -->
        <Table
            :key="
                applications.current_page +
                '-' +
                applications.total +
                '-' +
                applications.data?.length
            "
            :data="applications"
            :thead="thead"
            :title="'Daftar Aplikasi'"
            :isFetching="isFetching"
            @checkbox="handleCheckbox"
            @refresh="refreshData"
            @showNotification="showNotification"
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

    <!-- Modal hapus checkbox -->
    <div
        tabindex="-1"
        v-show="showDeleteModal"
        class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Tombol tutup modal -->
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
                        <strong>{{ selectedIdsCount }}</strong> aplikasi ini?
                    </h3>
                    <button
                        @click="deleteApp"
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

    <!-- Modal Tambah Aplikasi -->
    <div
        v-if="showAddModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow" ref="addModal">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5 bg-gradient-to-r from-emerald-500 to-teal-500"
                >
                    <!-- Judul modal -->
                    <h3 class="text-xl font-bold text-white">
                        Tambah Aplikasi
                    </h3>
                    <!-- Tombol close modal -->
                    <button
                        @click="showAddModal = false"
                        type="button"
                        class="text-white/80 hover:text-white transition-colors focus:outline-none"
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
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" @submit.prevent="addApplication">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <!-- Label Nama PIC -->
                            <label
                                for="pic_name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Nama PIC
                                <span class="text-red-500">*</span></label
                            >
                            <!-- Input Nama PIC -->
                            <input
                                type="text"
                                name="pic_name"
                                v-model="appPicName"
                                @focus="clearValidationError('pic_name')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Ketik nama PIC"
                            />
                            <div
                                v-if="validationErrors.pic_name"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            <span
                                                v-for="err in validationErrors.pic_name"
                                                :key="err"
                                            >
                                                {{ err }}<br />
                                            </span>
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <!-- Label Nama Aplikasi -->
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Nama Aplikasi
                                <span class="text-red-500">*</span>
                            </label>
                            <!-- Input Application name -->
                            <input
                                type="text"
                                name="name"
                                v-model="appName"
                                @focus="clearValidationError('name')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Ketik nama aplikasi"
                            />
                            <div
                                v-if="validationErrors.name"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            <span
                                                v-for="err in validationErrors.name"
                                                :key="err"
                                            >
                                                {{ err }}<br />
                                            </span>
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <!-- Label Deskripsi Aplikasi -->
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Deskripsi Aplikasi
                                <span class="text-red-500">*</span></label
                            >
                            <!-- Input Deskripsi Aplikasi -->
                            <textarea
                                id="description"
                                rows="4"
                                v-model="appDescription"
                                @focus="clearValidationError('description')"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tulis deskripsi aplikasi di sini"
                            ></textarea>
                            <div
                                v-if="validationErrors.description"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            <span
                                                v-for="err in validationErrors.description"
                                                :key="err"
                                            >
                                                {{ err }}<br />
                                            </span>
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol simpan dan batal -->
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="py-2.5 px-5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors duration-300"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="py-2.5 px-5 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg"
                        >
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
// Impor modul dan komponen yang diperlukan
import * as ApplicationAPI from "../api/ApplicationAPI";
import { ref, watch, Suspense, onMounted } from "vue";
import Layout from "./Layout.vue";
import Table from "../components/TableApp.vue";
import Search from "../components/Search.vue";
import TablePagination from "../components/TablePagination.vue";
import NotificationToast from "../components/NotificationToast.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { useFetch, onClickOutside, useStorage } from "@vueuse/core";

// Mendefinisikan URL dasar untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// Menyimpan urutan dan arah urutan di local storage
const orderBy = useStorage("orderBy", "created_at");
const orderDirection = useStorage("orderDirection", "desc");

// Mendefinisikan URL untuk permintaan data aplikasi
const url = ref(
    `${baseUrl}/applications?orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`
);

// Mendefinisikan variabel reaktif untuk menyimpan data aplikasi
const applications = ref({ data: [] });

// Variabel untuk pencarian, halaman, dan checkbox yang dipilih
const search = ref("");
const checked = ref([]);
const page = ref(1);
const selectedIdsCount = ref(0);

// Variabel untuk dropdown urutan dan modal
const orderDropdown = ref(false);
const btnOrder = ref(null);
const modalOrder = ref(null);
const deleteModal = ref(null);
const showDeleteModal = ref(false);

// Variabel untuk modal tambah aplikasi
const addModal = ref(null);
const showAddModal = ref(false);
const appName = ref("");
const appDescription = ref("");
const appPicName = ref("");
const validationErrors = ref({});

// Mengambil data dari API
const { isFetching, error, data } = useFetch(url, { refetch: true })
    .get()
    .json();

// State untuk notifikasi
const notification = ref({
    show: false,
    type: "",
    message: "",
    description: "",
});

// Mengawasi perubahan data dan memperbarui variabel applications
watch(data, (newData) => {
    if (newData) {
        applications.value = newData.applications ||
            newData.data?.applications || { data: [] };
    }
});

// Fungsi untuk menangani perubahan halaman
const handlePageChange = (page) => {
    if (page === applications.value.current_page) {
        return;
    }
    url.value = `${baseUrl}/applications?page=${page}&search=${search.value}&orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`;
    applications.value = { data: [] };
};

// Fungsi untuk menangani pencarian
const handleSearch = (query) => {
    if (query === search.value) {
        return;
    }
    url.value = `${baseUrl}/applications?search=${query}&page=1&orderBy=${orderBy.value}&orderDirection=${orderDirection.value}&filterBy=name,pic_name`;
    search.value = query;
    applications.value = { data: [] };
};

// Fungsi untuk mengatur urutan data
function order(newOrderBy, newOrderDirection) {
    if (
        orderBy.value === newOrderBy &&
        orderDirection.value === newOrderDirection
    ) {
        return;
    }
    orderBy.value = newOrderBy;
    orderDirection.value = newOrderDirection;
    url.value = `${baseUrl}/applications?page=${page}&search=${search.value}&orderBy=${newOrderBy}&orderDirection=${newOrderDirection}`;
    applications.value = { data: [] };
}

// Menutup dropdown urutan saat klik di luar modal
onClickOutside(modalOrder, (event) => {
    if (event.target !== btnOrder.value) {
        orderDropdown.value = false;
    }
});

// Menutup modal hapus saat klik di luar modal
onClickOutside(deleteModal, (event) => {
    showDeleteModal.value = false;
});

// Header tabel
const thead = ref([
    "",
    "No",
    "Nama Aplikasi",
    "Nama PIC",
    "Dibuat Pada",
    "Status",
    "Aksi",
]);

// Inisialisasi form untuk operasi hapus
const pageInertia = usePage();
const form = useForm({
    ids: checked.value,
    _token: pageInertia.props.csrf_token,
});

// Fungsi untuk menghapus aplikasi banyak
async function deleteApp() {
    try {
        const response = await ApplicationAPI.deleteApplications(form.ids);
        if (response.data.success) {
            checked.value = [];
            form.ids = [];
            selectedIdsCount.value = 0;
            showDeleteModal.value = false;
            await refreshData();
            notification.value = {
                show: true,
                type: "success",
                message: "Berhasil!",
                description: response.data.message,
            };
        } else {
            notification.value = {
                show: true,
                type: "danger",
                message: "Gagal!",
                description: response.data.message || "Terjadi kesalahan.",
            };
        }
    } catch (error) {
        notification.value = {
            show: true,
            type: "danger",
            message: "Gagal!",
            description:
                error.response?.data?.message ||
                error.message ||
                "Terjadi kesalahan.",
        };
    }
}

// Fungsi untuk menambah aplikasi baru
const addApplication = async (event) => {
    event.preventDefault();
    validationErrors.value = {};
    try {
        const response = await ApplicationAPI.createApplication({
            name: appName.value,
            description: appDescription.value,
            pic_name: appPicName.value,
            _token: pageInertia.props.csrf_token,
        });
        appName.value = "";
        appDescription.value = "";
        appPicName.value = "";
        showAddModal.value = false;
        refreshData();
        notification.value = {
            show: true,
            type: "success",
            message: "Berhasil!",
            description: response.data.message,
        };
    } catch (error) {
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors;
        } else {
            notification.value = {
                show: true,
                type: "danger",
                message: "Gagal!",
                description:
                    error.response?.data?.message || "Terjadi kesalahan.",
            };
        }
    }
};

// Fungsi untuk menyegarkan data aplikasi
function refreshData() {
    ApplicationAPI.fetchApplications({
        search: search.value,
        page: applications.value.current_page || 1,
        orderBy: orderBy.value,
        orderDirection: orderDirection.value,
    })
        .then((response) => {
            applications.value = response.data.applications ||
                response.data.data?.applications || { data: [] };
        })
        .catch(() => {
            notification.value = {
                show: true,
                type: "danger",
                message: "Gagal Memuat Data!",
                description: "Terjadi kesalahan saat menyegarkan data.",
            };
        });
}

//Fungsi untuk checkbox
function handleCheckbox(selectedIds) {
    form.ids = [...selectedIds];
    selectedIdsCount.value = form.ids.length;
}

// Fungsi untuk menangani perubahan checkbox
const handleDeleteCheckbox = () => {
    if (form.ids.length > 0) {
        selectedIdsCount.value = form.ids.length;
        showDeleteModal.value = true;
    } else {
        showNotification("error", "Tidak ada data yang dipilih!");
    }
};

// Inisialisasi form untuk operasi tambah
const Addform = useForm({
    name: appName.value,
    description: appDescription.value,
    pic_name: appPicName.value,
    _token: pageInertia.props.csrf_token,
});

// Mengawasi perubahan pada variabel appName dan memperbarui form
watch(appName, (newAppName) => {
    Addform.name = newAppName;
});

// Mengawasi perubahan pada variabel appDescription dan memperbarui form
watch(appDescription, (newAppDescription) => {
    Addform.description = newAppDescription;
});

// Mengawasi perubahan pada variabel appPicName dan memperbarui form
watch(appPicName, (newAppPicName) => {
    Addform.pic_name = newAppPicName;
});

// Bersihkan data-page pada elemen
onMounted(() => {
    const appDiv = document.getElementById("app");
    if (appDiv) {
        appDiv.removeAttribute("data-page");
    }
});

// Fungsi untuk menampilkan notifikasi
function showNotification(notificationData) {
    notification.value = {
        show: true,
        type: notificationData.type,
        message: notificationData.message,
        description: notificationData.description,
    };
}

// Fungsi untuk membersihkan pesan kesalahan validasi
function clearValidationError(field) {
    if (validationErrors.value[field]) {
        validationErrors.value[field] = "";
    }
}
</script>
