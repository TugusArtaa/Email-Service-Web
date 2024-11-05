<script setup>
import { ref, computed } from "vue";
import Layout from "./Layout.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// State untuk sorting, pagination, dan search
const currentPage = ref(1);
const itemsPerPage = 5;
const sortBy = ref("created_at");
const sortOrder = ref("desc");
const searchQuery = ref("");

// Computed property untuk email stats
const emailStats = computed(() => ({
    sent: props.data.filter((log) => log.status === "success").length,
    failed: props.data.filter((log) => log.status === "failed").length,
    total: props.data.length,
}));

// Computed property untuk filtered data
const filteredData = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return props.data.filter((item) => {
        const applicationName = item.application.name.toLowerCase();
        const email = JSON.parse(item.request).to.toLowerCase();
        const subject = JSON.parse(item.request).subject.toLowerCase();
        const status = item.status === "success" ? "terkirim" : "gagal";

        return (
            applicationName.includes(query) ||
            email.includes(query) ||
            subject.includes(query) ||
            status.includes(query)
        );
    });
});

// Computed property untuk sorted data
const sortedData = computed(() => {
    let sorted = [...filteredData.value].sort((a, b) => {
        let aValue, bValue;

        switch (sortBy.value) {
            case "application":
                aValue = a.application.name.toLowerCase();
                bValue = b.application.name.toLowerCase();
                break;
            case "created_at":
                aValue = new Date(a.created_at);
                bValue = new Date(b.created_at);
                break;
            default:
                return 0;
        }

        if (sortOrder.value === "asc") {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });

    return sorted;
});

// Computed property untuk paginated data
const paginatedData = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    return sortedData.value.slice(startIndex, endIndex);
});

// Compute total pages
const totalPages = computed(() =>
    Math.ceil(sortedData.value.length / itemsPerPage)
);

// Method untuk mengubah sorting
const toggleSort = (field) => {
    if (sortBy.value === field) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortBy.value = field;
        sortOrder.value = "asc";
    }
};

// Method untuk pagination
const changePage = (page) => {
    currentPage.value = page;
};

// Method untuk handle search
const handleSearch = () => {
    currentPage.value = 1;
};

// Computed property untuk menampilkan rentang data yang ditampilkan
const displayRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage + 1;
    const end = Math.min(
        currentPage.value * itemsPerPage,
        sortedData.value.length
    );
    return {
        start,
        end,
        total: sortedData.value.length,
    };
});

const formatTimestamp = (isoString) => {
    try {
        const date = new Date(isoString);

        return date
            .toLocaleString("id-ID", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: false,
            })
            .replace(/\//g, "-");
    } catch (error) {
        return isoString;
    }
};
</script>

<template>
    <Layout>
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Card Email Terkirim -->
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Jumlah Email Terkirim
                        </p>
                        <h3 class="text-2xl font-semibold">
                            {{ emailStats.sent }}
                        </h3>
                        <p class="text-sm text-green-500">Terkirim</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="#007E39"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card Email Gagal -->
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Jumlah Email Gagal</p>
                        <h3 class="text-2xl font-semibold">
                            {{ emailStats.failed }}
                        </h3>
                        <p class="text-sm text-red-500">Gagal</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="#A30000"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card Total Email -->
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Seluruh Email</p>
                        <h3 class="text-2xl font-semibold">
                            {{ emailStats.total }}
                        </h3>
                        <p class="text-sm text-gray-500">Email</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar dan Tabel -->
        <div class="p-6 bg-white rounded-xl shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Log E-Mail</h2>
                <!-- Search Input -->
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

            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-separate border-spacing-0">
                    <thead>
                        <tr class="text-sm font-medium text-gray-500 border-b">
                            <th class="w-[15%] px-6 py-3 text-left">
                                <div
                                    class="flex items-center cursor-pointer"
                                    @click="toggleSort('application')"
                                >
                                    <span class="whitespace-nowrap">NAMA APLIKASI</span>
                                    <span class="ml-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            :class="{
                                                'text-green-500':
                                                    sortBy === 'application',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                v-if="
                                                    sortBy === 'application' &&
                                                    sortOrder === 'asc'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 15l7-7 7 7"
                                            />
                                            <path
                                                v-else
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </span>
                                </div>
                            </th>
                            <th class="w-[20%] px-6 py-3 text-left">
                                <span class="whitespace-nowrap">EMAIL PENERIMA</span>
                            </th>
                            <th class="w-[10%] px-6 py-3 text-left">
                                <span class="whitespace-nowrap">SUBJECT</span>
                            </th>
                            <th class="w-[10%] px-6 py-3 text-left">
                                <span class="whitespace-nowrap">STATUS</span>
                            </th>
                            <th class="w-[15%] px-6 py-3 text-left">
                                <div
                                    class="flex items-center cursor-pointer"
                                    @click="toggleSort('created_at')"
                                >
                                    <span class="whitespace-nowrap">TIMESTAMP</span>
                                    <span class="ml-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            :class="{
                                                'text-green-500':
                                                    sortBy === 'created_at',
                                            }"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                v-if="
                                                    sortBy === 'created_at' &&
                                                    sortOrder === 'asc'
                                                "
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 15l7-7 7 7"
                                            />
                                            <path
                                                v-else
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </span>
                                </div>
                            </th>
                            <th class="w-[30%] px-6 py-3 text-left">
                                <span class="whitespace-nowrap">ERROR MESSAGE</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="data in paginatedData"
                            :key="data.id"
                            class="text-sm text-gray-800 hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 font-medium">
                                {{ data.application.name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ JSON.parse(data.request).to }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium">
                                    {{ JSON.parse(data.request).subject }}
                                </div>
                                <div
                                    v-if="data.subjectDetail"
                                    class="text-gray-500"
                                >
                                    {{ JSON.parse(data.request).to }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 text-xs font-medium rounded-full whitespace-nowrap',
                                        data.status === 'success'
                                            ? 'text-green-700 bg-green-100'
                                            : 'text-red-700 bg-red-100',
                                    ]"
                                >
                                    {{
                                        data.status === "success"
                                            ? "Terkirim"
                                            : "Gagal"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ formatTimestamp(data.created_at) }}
                            </td>
                            <td class="px-6 py-4 text-red-500">
                                {{ data.error_message || "-" }}
                            </td>
                        </tr>
                        <tr v-if="paginatedData.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                Tidak ada data yang ditemukan!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls Ges -->
            <div class="flex items-center justify-between mt-4 px-4">
                <div class="text-sm text-gray-500">
                    Showing {{ displayRange.start }} to
                    {{ displayRange.end }} of {{ displayRange.total }} entries
                </div>
                <div class="flex gap-2">
                    <button
                        @click="changePage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50"
                    >
                        Previous
                    </button>
                    <button
                        v-for="page in totalPages"
                        :key="page"
                        @click="changePage(page)"
                        :class="[
                            'px-3 py-1 text-sm border rounded hover:bg-gray-50',
                            currentPage === page
                                ? 'bg-blue-50 text-green-600'
                                : '',
                        ]"
                    >
                        {{ page }}
                    </button>
                    <button
                        @click="changePage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>