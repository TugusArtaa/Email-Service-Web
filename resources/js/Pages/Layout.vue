<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside
            :class="[
                'fixed top-0 left-0 z-40 h-screen transition-all duration-300 ease-in-out bg-white',
                isSidebarOpen ? 'w-64' : 'w-0 -translate-x-full',
            ]"
        >
            <!-- Logo Section -->
            <div class="flex items-center gap-2 px-6 py-5">
                <img
                    :src="'/bpd.png'"
                    alt="Logo"
                    class="w-8 h-8"
                    :class="{ hidden: !isSidebarOpen }"
                />
                <span
                    :class="[
                        'text-base font-bold whitespace-nowrap transition-opacity duration-300',
                        isSidebarOpen ? 'opacity-100' : 'hidden',
                    ]"
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
                            !isSidebarOpen && 'hidden',
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
                        <span>Dashboard</span>
                    </a>

                    <!-- Integrasi -->
                    <a
                        href="/integrasi"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                            currentPath === '/integrasi'
                                ? 'text-green-600 bg-green-50'
                                : 'text-gray-600 hover:bg-gray-50',
                            !isSidebarOpen && 'hidden',
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
                        <span>Integrasi</span>
                    </a>

                    <!-- Manajemen Aplikasi -->
                    <a
                        href="/manajemen-aplikasi"
                        :class="[
                            'flex items-center gap-4 px-4 py-3 text-sm font-medium rounded-lg',
                            currentPath === '/manajemen-aplikasi'
                                ? 'text-green-600 bg-green-50'
                                : 'text-gray-600 hover:bg-gray-50',
                            !isSidebarOpen && 'hidden',
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
                        <span>Manajemen Aplikasi</span>
                    </a>
                </nav>

                <!-- Account Section -->
                <div :class="['mt-8', !isSidebarOpen && 'hidden']">
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
                            <span>Profile</span>
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div
            :class="[
                'flex-1 transition-all duration-300',
                isSidebarOpen ? 'ml-64' : 'ml-0',
            ]"
        >
            <!-- Top Navigation -->
            <header
                class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200"
            >
                <div class="flex items-center flex-1 gap-4">
                    <!-- Toggle Sidebar Button -->
                    <button
                        @click="toggleSidebar"
                        class="p-2 rounded-lg hover:bg-green-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            :class="{ 'rotate-180': !isSidebarOpen }"
                        >
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold">{{ pageTitle }}</h1>
                </div>

                <div class="flex items-center gap-6">
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
                        <span class="text-sm font-medium">{{ user.name }}</span>
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
                                class="absolute right-0 w-48 py-2 mt-2 bg-white border border-gray-100 rounded-lg shadow-lg"
                            >
                                <a
                                    href="/profile"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Profile Settings
                                </a>
                                <form @submit.prevent="submit">
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
import { usePage, useForm } from "@inertiajs/vue3";

const showAdminMenu = ref(false);
const isSidebarOpen = ref(true);
const page = usePage();
const user = page.props.auth.user;

const form = useForm({
    _token: page.props.csrf_token,
});

function submit() {
    form.post("/logout");
}

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

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>
