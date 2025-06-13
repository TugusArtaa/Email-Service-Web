<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside
            :class="[
                'fixed lg:relative lg:translate-x-0 top-0 left-0 z-40 h-full transition-all duration-300 ease-in-out',
                isSidebarOpen
                    ? 'w-64 translate-x-0'
                    : 'w-0 -translate-x-full lg:w-20 lg:translate-x-0',
            ]"
        >
            <div
                class="h-full flex flex-col bg-gradient-to-br from-[#019966] to-[#017755] text-white shadow-xl"
                style="
                    background-image: radial-gradient(
                            circle at 10% 20%,
                            rgba(255, 255, 255, 0.05) 0%,
                            transparent 40%
                        ),
                        radial-gradient(
                            circle at 90% 80%,
                            rgba(255, 255, 255, 0.07) 0%,
                            transparent 40%
                        ),
                        repeating-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.03) 0px,
                            rgba(255, 255, 255, 0.03) 1px,
                            transparent 1px,
                            transparent 10px
                        ),
                        linear-gradient(to bottom right, #019966, #017755);
                    background-blend-mode: overlay;
                "
            >
                <!-- Bagian Logo -->
                <div
                    class="flex items-center justify-center h-16 px-4 border-b border-green-700/50"
                >
                    <div class="flex items-center gap-3">
                        <img
                            :src="'/Logo_SMEBB.png'"
                            alt="Logo"
                            class="w-9 h-9 object-contain"
                        />
                        <span
                            :class="[
                                'text-base font-bold whitespace-nowrap tracking-wide',
                                isSidebarOpen ? 'block' : 'hidden',
                            ]"
                            >S-MEBB DASHBOARD</span
                        >
                    </div>
                </div>

                <!-- Menu Sidebar -->
                <div class="flex-1 py-6 px-3 overflow-y-auto">
                    <nav class="space-y-1.5">
                        <!-- Dashboard -->
                        <a
                            href="/"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                                currentPath === '/'
                                    ? 'bg-white/20 text-white font-medium'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            <div class="flex-shrink-0 w-5 h-5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-full h-full"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        x="3"
                                        y="3"
                                        width="7"
                                        height="7"
                                        rx="1"
                                    />
                                    <rect
                                        x="14"
                                        y="3"
                                        width="7"
                                        height="7"
                                        rx="1"
                                    />
                                    <rect
                                        x="14"
                                        y="14"
                                        width="7"
                                        height="7"
                                        rx="1"
                                    />
                                    <rect
                                        x="3"
                                        y="14"
                                        width="7"
                                        height="7"
                                        rx="1"
                                    />
                                </svg>
                            </div>
                            <span
                                :class="[isSidebarOpen ? 'block' : 'lg:hidden']"
                                >Dashboard</span
                            >
                        </a>

                        <!-- Integrasi -->
                        <a
                            href="/integrasi"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                                currentPath === '/integrasi'
                                    ? 'bg-white/20 text-white font-medium'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            <div class="flex-shrink-0 w-5 h-5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                                    />
                                </svg>
                            </div>
                            <span
                                :class="[isSidebarOpen ? 'block' : 'lg:hidden']"
                                >Manajemen Email</span
                            >
                        </a>

                        <!-- Manajemen Aplikasi -->
                        <a
                            href="/application"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                                currentPath === '/application'
                                    ? 'bg-white/20 text-white font-medium'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            <div class="flex-shrink-0 w-5 h-5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-full h-full"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"
                                    />
                                </svg>
                            </div>
                            <span
                                :class="[isSidebarOpen ? 'block' : 'lg:hidden']"
                                >Manajemen Aplikasi</span
                            >
                        </a>

                        <!-- Approve Applications -->
                        <a
                            v-show="user.level === 'supervisor'"
                            href="/approve"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                                currentPath === '/approve'
                                    ? 'bg-white/20 text-white font-medium'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white',
                            ]"
                        >
                            <div class="flex-shrink-0 w-5 h-5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="size-5"
                                    viewBox="0 0 48 48"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M41.5,10.5v24c0,3.9-3.1,7-7,7h-21c-1.7,0-3-1.3-3-3v-2"
                                    ></path>
                                    <path
                                        d="M36.5,17.6v15.9c0,1.7-1.3,3-3,3h-25c-1.7,0-3-1.3-3-3v-5.1"
                                    ></path>
                                    <path
                                        d="M5.5,21.7V8.5c0-1.7,1.3-3,3-3h25c1.7,0,3,1.3,3,3v2.2"
                                    ></path>
                                    <polyline
                                        points="15.5,21.5 19.5,25.5 28.5,16.5"
                                    ></polyline>
                                </svg>
                            </div>
                            <span
                                :class="[isSidebarOpen ? 'block' : 'lg:hidden']"
                                >Approve Aplikasi</span
                            >
                        </a>
                    </nav>

                    <!-- Profil -->
                    <div :class="['mt-8', !isSidebarOpen && 'lg:mt-10']">
                        <h3
                            :class="[
                                'px-4 text-xs font-semibold text-white/60 uppercase tracking-wider',
                                !isSidebarOpen && 'lg:hidden',
                            ]"
                        >
                            HALAMAN AKUN
                        </h3>
                        <nav class="mt-2">
                            <a
                                href="/profile"
                                :class="[
                                    'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
                                    currentPath === '/profile'
                                        ? 'bg-white/20 text-white font-medium'
                                        : 'text-white/80 hover:bg-white/10 hover:text-white',
                                ]"
                            >
                                <div class="flex-shrink-0 w-5 h-5">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-full h-full"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="8" r="4" />
                                        <path d="M20 21a8 8 0 1 0-16 0" />
                                    </svg>
                                </div>
                                <span
                                    :class="[
                                        isSidebarOpen ? 'block' : 'lg:hidden',
                                    ]"
                                    >Profil</span
                                >
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Footer Menu Sidebar -->
                <div
                    :class="[
                        'p-4 border-t border-green-700/50',
                        !isSidebarOpen && 'lg:p-3',
                    ]"
                >
                    <form @submit.prevent="showLogoutModal = true">
                        <input
                            type="hidden"
                            name="_token"
                            :value="page.props.csrf_token"
                        />
                        <button
                            type="submit"
                            :class="[
                                'w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-500/70 hover:bg-red-600/80 rounded-lg transition-all text-white',
                                !isSidebarOpen && 'lg:px-2',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            <span
                                :class="[isSidebarOpen ? 'block' : 'lg:hidden']"
                                >Logout</span
                            >
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Bagian atas Navigation -->
            <header class="bg-white border-b border-gray-200 shadow-sm z-30">
                <div
                    class="flex items-center justify-between h-16 px-4 lg:px-6"
                >
                    <div class="flex items-center flex-1 gap-4">
                        <!-- Tombol Toggle Sidebar -->
                        <button
                            @click="toggleSidebar"
                            class="p-2 rounded-lg text-gray-600 hover:bg-green-100 hover:text-green-600 transition-colors"
                            aria-label="Toggle sidebar"
                        >
                            <svg
                                v-if="isSidebarOpen"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"
                                />
                            </svg>
                        </button>

                        <!-- Judul Halaman -->
                        <div>
                            <h1 class="text-lg font-semibold text-gray-800">
                                {{ pageTitle }}
                            </h1>
                            <div
                                class="text-xs text-gray-500 flex items-center gap-1"
                            >
                                <span>S-MEBB</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>{{ pageTitle }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Dropdown Profil User -->
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img
                                        class="w-9 h-9 rounded-full object-cover border-2 border-green-500"
                                        :src="'/bpd.png'"
                                        alt="User avatar"
                                    />
                                    <span
                                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"
                                    ></span>
                                </div>
                                <div class="hidden md:block">
                                    <div
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        {{ user.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ user.level }}
                                    </div>
                                </div>
                                <button
                                    @click="toggleAdminMenu"
                                    class="p-1.5 text-gray-600 hover:text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                >
                                    <svg
                                        v-if="showAdminMenu"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M18 15l-6-6-6 6" />
                                    </svg>
                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Menu Dropdown Admin -->
                            <div
                                v-if="showAdminMenu"
                                class="absolute right-0 min-w-max mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                @click.outside="showAdminMenu = false"
                            >
                                <div class="px-4 py-3">
                                    <p class="text-sm text-gray-900">
                                        {{ user.name }}
                                    </p>
                                    <p
                                        class="text-sm font-medium text-gray-500 truncate"
                                    >
                                        {{ user.email }}
                                    </p>
                                </div>
                                <div class="py-1">
                                    <a
                                        href="/profile"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                            />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        Pengaturan Profil
                                    </a>
                                </div>
                                <div class="py-1">
                                    <button
                                        @click="showLogoutModal = true"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                            />
                                            <polyline
                                                points="16 17 21 12 16 7"
                                            />
                                            <line
                                                x1="21"
                                                y1="12"
                                                x2="9"
                                                y2="12"
                                            />
                                        </svg>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto bg-gray-50">
                <!-- Isi Content -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
                >
                    <slot></slot>
                </div>
            </main>
        </div>

        <!-- Modal Konfirmasi Logout -->
        <div
            tabindex="-1"
            v-show="showLogoutModal"
            class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
        >
            <div class="relative w-full max-w-sm max-h-full p-2">
                <div
                    class="relative bg-white rounded-xl shadow-lg"
                    ref="logoutModal"
                >
                    <!-- Tombol tutup modal -->
                    <button
                        @click="showLogoutModal = false"
                        type="button"
                        class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-full text-base w-8 h-8 flex justify-center items-center transition"
                    >
                        <svg
                            class="w-4 h-4"
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
                                d="M6 6l8 8M6 14L14 6"
                            />
                        </svg>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                    <div class="p-5 text-center md:p-6">
                        <!-- Ikon -->
                        <div class="flex justify-center mb-4">
                            <div
                                class="bg-red-100 rounded-full p-2 inline-flex"
                            >
                                <svg
                                    class="w-10 h-10 text-red-600"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 48 48"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="3"
                                        d="M32 32l8-8m0 0l-8-8m8 8H12m12 8v2a4 4 0 01-4 4h-4a4 4 0 01-4-4V12a4 4 0 014-4h4a4 4 0 014 4v2"
                                    />
                                </svg>
                            </div>
                        </div>
                        <!-- Judul -->
                        <h2 class="mb-1 text-lg font-bold text-gray-800">
                            Konfirmasi Logout
                        </h2>
                        <!-- Deskripsi -->
                        <p class="mb-4 text-sm text-gray-600">
                            Anda akan keluar dari dashboard S-MEBB.<br />
                            Apakah Anda yakin ingin logout?
                        </p>
                        <!-- Tombol konfirmasi -->
                        <button
                            @click="submitLogout"
                            type="button"
                            class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-semibold rounded-lg text-sm inline-flex items-center px-5 py-2 text-center transition"
                            :disabled="logoutLoading"
                        >
                            <svg
                                v-if="logoutLoading"
                                aria-hidden="true"
                                class="w-4 h-4 mr-2 text-white animate-spin"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                ></path>
                            </svg>
                            <span v-if="!logoutLoading">Ya, Logout</span>
                            <span v-else>Memproses...</span>
                        </button>
                        <!-- Tombol batal -->
                        <button
                            @click="showLogoutModal = false"
                            type="button"
                            class="py-2 px-5 ml-2 text-sm font-semibold text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-950 focus:z-10 focus:ring-4 focus:ring-gray-100 transition"
                            :disabled="logoutLoading"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";

// State untuk menampilkan atau menyembunyikan menu dropdown admin
const showAdminMenu = ref(false);

// State untuk menampilkan atau menyembunyikan sidebar
const isSidebarOpen = ref(true);

// State untuk modal konfirmasi logout
const showLogoutModal = ref(false);
// State loading untuk tombol logout
const logoutLoading = ref(false);

// Mengambil data halaman dan pengguna dari Inertia.js
const page = usePage();
const user = page.props.auth.user;

// Form untuk logout menggunakan Inertia.js
const form = useForm({
    _token: page.props.csrf_token,
});

// Fungsi untuk mengirimkan form logout
function submit() {
    form.post("/logout");
}

// Fungsi untuk logout asli (dipanggil dari modal)
function submitLogout() {
    if (logoutLoading.value) return;
    logoutLoading.value = true;
    form.post("/logout", {
        onFinish: () => {
            logoutLoading.value = false;
            showLogoutModal.value = false;
        },
    });
}

// Menghitung path URL saat ini
const currentPath = computed(() => {
    return window.location.pathname;
});

// Menghitung judul halaman berdasarkan path URL
const pageTitle = computed(() => {
    switch (currentPath.value) {
        case "/dashboard":
            return "Dashboard";
        case "/integrasi":
            return "Manajemen Email";
        case "/application":
            return "Manajemen Aplikasi";
        case "/approve":
            return "Approve Aplikasi";
        case "/profile":
            return "Profil";
        default:
            return "Dashboard";
    }
});

// Fungsi untuk toggle (menampilkan/menyembunyikan) menu dropdown admin
const toggleAdminMenu = () => {
    showAdminMenu.value = !showAdminMenu.value;
};

// Fungsi untuk toggle (menampilkan/menyembunyikan) sidebar
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style>
html,
body {
    height: 100%;
    overflow: hidden;
}

main {
    overflow-y: auto;
}
</style>
