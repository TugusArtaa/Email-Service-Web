<script setup>
import { ref } from "vue";
import Layout from "./Layout.vue";
import StatsCard from "../components/StatsCard.vue";
import EmailLogTable from "../components/EmailLogTable.vue";
import Pagination from "../components/Pagination.vue";
import { useEmailData } from "../composables/useEmailData";

// Mendefinisikan props yang diterima dari parent, berupa data
const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// Mendestrukturisasi properti yang didapat dari composable useEmailData
const {
    sortBy,
    sortOrder,
    searchQuery,
    currentPage,
    emailStats,
    paginatedData,
    totalPages,
    displayRange,
    toggleSort,
    handleSearch,
} = useEmailData(props.data);
</script>

<template>
    <!-- Judul -->
    <head>
        <title>Dashboard</title>
    </head>
    <!-- Layout utama halaman -->
    <Layout>
        <!-- Menampilkan statistik -->
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
            <StatsCard
                title="Jumlah Email Terkirim"
                :value="emailStats.sent"
                subtitle="Terkirim"
                type="success"
            />
            <StatsCard
                title="Jumlah Email Gagal"
                :value="emailStats.failed"
                subtitle="Gagal"
                type="error"
            />
            <StatsCard
                title="Total Seluruh Email"
                :value="emailStats.total"
                subtitle="Email"
                type="default"
            />
        </div>

        <!-- Bagian utama tabel log email dan pencarian -->
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Log E-Mail</h2>
                <div class="relative">
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="handleSearch"
                        placeholder="Search..."
                        class="px-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                    <svg
                        class="absolute right-3 top-2.5 h-5 w-5 text-gray-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
            </div>

            <!-- Tabel log email dengan paginasi dan sorting -->
            <EmailLogTable
                :data="paginatedData"
                :sort-by="sortBy"
                :sort-order="sortOrder"
                @sort="toggleSort"
            />

            <!-- Komponen Pagination untuk navigasi halaman -->
            <Pagination
                :current-page="currentPage"
                :total-pages="totalPages"
                :display-range="displayRange"
                @page-change="(page) => (currentPage = page)"
            />
        </div>
    </Layout>
</template>
