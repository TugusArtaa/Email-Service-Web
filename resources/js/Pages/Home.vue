<template>
    <head>
        <title>Email Dashboard</title>
    </head>

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
                        :src="'/DashboardLogo.png'"
                        alt="Logo"
                        class="h-20 w-20"
                    />
                </div>
                <div>
                    <h1
                        class="text-4xl font-bold text-white tracking-tight drop-shadow-md"
                    >
                        Dashboard Analisis Email
                    </h1>
                    <div class="flex items-center mt-3">
                        <span
                            class="w-10 h-1 bg-emerald-300 rounded-full mr-3"
                        ></span>
                        <p class="text-lg text-emerald-50 font-medium">
                            Ringkasan performa pengiriman email
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="absolute top-0 right-0 h-full w-24 bg-white/5 -skew-x-12"
            ></div>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Email terkirim -->
            <div
                class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
            >
                <div
                    class="px-6 py-5 bg-gradient-to-r from-emerald-500 to-teal-500"
                >
                    <h3 class="text-lg font-semibold text-white">
                        Email Terkirim
                    </h3>
                </div>
                <div class="p-6 flex items-center">
                    <div
                        class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mr-4"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-emerald-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ emailStats.sent }}
                        </h2>
                        <p class="text-gray-500">
                            {{ successRate }}% dari total
                        </p>
                    </div>
                </div>
            </div>
            <!-- Email gagal -->
            <div
                class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
            >
                <div
                    class="px-6 py-5 bg-gradient-to-r from-red-500 to-rose-500"
                >
                    <h3 class="text-lg font-semibold text-white">
                        Email Gagal
                    </h3>
                </div>
                <div class="p-6 flex items-center">
                    <div
                        class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mr-4"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-rose-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ emailStats.failed }}
                        </h2>
                        <p class="text-gray-500">
                            {{ failureRate }}% dari total
                        </p>
                    </div>
                </div>
            </div>
            <!-- Total Email -->
            <div
                class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
            >
                <div
                    class="px-6 py-5 bg-gradient-to-r from-blue-500 to-indigo-500"
                >
                    <h3 class="text-lg font-semibold text-white">
                        Total Email
                    </h3>
                </div>
                <div class="p-6 flex items-center">
                    <div
                        class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mr-4"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-blue-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ emailStats.total }}
                        </h2>
                        <p class="text-gray-500">Total pengiriman</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris kedua: Chart dan Persentase Statistik -->
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-12">
            <!-- Chart Section -->
            <div
                class="lg:col-span-8 bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 h-full"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Statistik Pengiriman Email
                    </h3>
                    <div class="flex space-x-2">
                        <div class="flex items-center">
                            <div
                                class="w-3 h-3 bg-emerald-500 rounded-full mr-1"
                            ></div>
                            <span class="text-xs text-gray-600">Terkirim</span>
                        </div>
                        <div class="flex items-center">
                            <div
                                class="w-3 h-3 bg-red-500 rounded-full mr-1"
                            ></div>
                            <span class="text-xs text-gray-600">Gagal</span>
                        </div>
                    </div>
                </div>
                <Chart :data="props.data" />
            </div>

            <!-- Persentase Card -->
            <div
                class="lg:col-span-4 bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 h-full flex flex-col"
            >
                <!-- Header -->
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Ringkasan Persentase
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Statistik keberhasilan dan kegagalan
                    </p>
                </div>

                <!-- Konten -->
                <div class="flex-1 p-6 flex flex-col justify-between">
                    <!-- Success Rate -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-medium text-gray-700">
                                Tingkat Keberhasilan
                            </h4>
                            <span
                                class="px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-full"
                                >Success</span
                            >
                        </div>
                        <div class="flex items-center">
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16" viewBox="0 0 36 36">
                                    <path
                                        class="stroke-emerald-100 fill-none"
                                        stroke-width="3.8"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                    <path
                                        class="stroke-emerald-500 fill-none"
                                        stroke-width="3.8"
                                        stroke-linecap="round"
                                        :stroke-dasharray="`${successRate}, 100`"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                </svg>
                                <div
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <span
                                        class="text-xs font-semibold text-gray-800"
                                        >{{ successRate }}%</span
                                    >
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-2xl font-bold text-gray-800">
                                    {{ emailStats.sent }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Email terkirim
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 my-4"></div>

                    <!-- Failure Rate -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-medium text-gray-700">
                                Tingkat Kegagalan
                            </h4>
                            <span
                                class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full"
                                >Failed</span
                            >
                        </div>
                        <div class="flex items-center">
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16" viewBox="0 0 36 36">
                                    <path
                                        class="stroke-red-100 fill-none"
                                        stroke-width="3.8"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                    <path
                                        class="stroke-red-500 fill-none"
                                        stroke-width="3.8"
                                        stroke-linecap="round"
                                        :stroke-dasharray="`${failureRate}, 100`"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                </svg>
                                <div
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <span
                                        class="text-xs font-semibold text-gray-800"
                                        >{{ failureRate }}%</span
                                    >
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-2xl font-bold text-gray-800">
                                    {{ emailStats.failed }}
                                </h4>
                                <p class="text-sm text-gray-500">Email gagal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, computed } from "vue";
import Layout from "./Layout.vue";
import Chart from "../components/Chart.vue";

// Mendefinisikan props yang diterima dari parent, berupa data
const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// Menghitung statistik email berdasarkan data
const emailStats = computed(() => ({
    sent: props.data.filter((log) => log.status === "success").length,
    failed: props.data.filter((log) => log.status === "failed").length,
    total: props.data.length,
}));

// Menghitung persentase keberhasilan dan kegagalan
const successRate = computed(() =>
    ((emailStats.value.sent / emailStats.value.total) * 100).toFixed(2)
);
const failureRate = computed(() =>
    ((emailStats.value.failed / emailStats.value.total) * 100).toFixed(2)
);

// Fungsi `getColorClass` menentukan kelas warna berdasarkan jenis card (`type`).
const getColorClass = (type) => {
    const colors = {
        success: {
            bg: "bg-green-100",
            text: "text-green-500",
            stroke: "stroke-[#007E39]",
        },
        error: {
            bg: "bg-red-100",
            text: "text-red-500",
            stroke: "stroke-[#A30000]",
        },
        default: {
            bg: "bg-gray-100",
            text: "text-gray-500",
            stroke: "stroke-current",
        },
    };
    return colors[type];
};
</script>
