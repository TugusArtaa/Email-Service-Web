<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside
            class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-white"
        >
            <!-- Logo Section -->
            <div class="flex items-center gap-2 px-6 py-5">
                <img :src="'/bpd.png'" alt="Logo" class="w-8 h-8" />
                <span class="text-base font-bold whitespace-nowrap"
                    >S-MEBB DASHBOARD</span
                >
            </div>

            <!-- Navigation Menu -->
            <div class="px-4 py-2">
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a
                        href="/"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                            currentPath === '/'
                                ? 'text-green-600 bg-green-50'
                                : 'text-gray-600 hover:bg-gray-50',
                        ]"
                    >
                        <div class="w-5 h-5">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-full h-full"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1" />
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
                        Dashboard
                    </a>

                    <!-- Integrasi -->
                    <a
                        href="/integrasi"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                            currentPath === '/integrasi'
                                ? 'text-green-600 bg-green-50'
                                : 'text-gray-600 hover:bg-gray-50',
                        ]"
                    >
                        <div class="w-5 h-5">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-full h-full"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"
                                />
                            </svg>
                        </div>
                        Integrasi
                    </a>

                    <!-- Manajemen Aplikasi -->
                    <a
                        href="/manajemen-aplikasi"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                            currentPath === '/manajemen-aplikasi'
                                ? 'text-green-600 bg-green-50'
                                : 'text-gray-600 hover:bg-gray-50',
                        ]"
                    >
                        <div class="w-5 h-5">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-full h-full"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </div>
                        Manajemen Aplikasi
                    </a>
                </nav>

                <!-- Account Section -->
                <div class="mt-8">
                    <h3
                        class="px-4 text-xs font-semibold text-gray-400 uppercase"
                    >
                        ACCOUNT PAGES
                    </h3>
                    <nav class="mt-2">
                        <a
                            href="/profile"
                            :class="[
                                'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                                currentPath === '/profile'
                                    ? 'text-green-600 bg-green-50'
                                    : 'text-gray-600 hover:bg-gray-50',
                            ]"
                        >
                            <div class="w-5 h-5">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-full h-full"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M20 21a8 8 0 1 0-16 0" />
                                </svg>
                            </div>
                            Profile
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Top Navigation -->
            <header
                class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200"
            >
                <div class="flex items-center flex-1">
                    <h1 class="text-lg font-semibold">{{ pageTitle }}</h1>
                </div>

                <!-- Search and Admin Section -->
                <div class="flex items-center gap-6">
                    <!-- Search -->
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Type here..."
                            class="w-64 pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                        />
                    </div>

                    <!-- Admin Menu -->
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img
                                class="w-10 h-10 rounded-full"
                                :src="'/bpd.png'"
                                alt=""
                            />
                            <span
                                class="top-0 left-7 absolute w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"
                            ></span>
                        </div>
                        <span class="text-sm font-medium">Admin</span>
                        <div class="relative">
                            <button
                                @click="toggleAdminMenu"
                                class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-gray-600"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="showAdminMenu"
                                class="absolute right-0 w-48 py-2 mt-2 bg-white rounded-lg shadow-lg border border-gray-100"
                            >
                                <a
                                    href="/profile"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Profile Settings
                                </a>
                                <form method="POST" action="/logout">
                                    <input
                                        type="hidden"
                                        name="_token"
                                        :value="csrf_token"
                                    />
                                    <button
                                        type="submit"
                                        class="w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
                                    >
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Settings -->
                        <button
                            class="flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path d="M12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const showAdminMenu = ref(false);
const csrf_token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

// Get current path for active navigation
const currentPath = computed(() => {
    return window.location.pathname;
});

// Get page title based on current path
const pageTitle = computed(() => {
    switch (currentPath.value) {
        case "/dashboard":
            return "Dashboard";
        case "/integrasi":
            return "Integrasi";
        case "/manajemen-aplikasi":
            return "Manajemen Aplikasi";
        case "/profile":
            return "Profile";
        default:
            return "Dashboard";
    }
});

const toggleAdminMenu = () => {
    showAdminMenu.value = !showAdminMenu.value;
};
</script>
