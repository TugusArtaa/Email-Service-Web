<script setup>
import { computed } from "vue";

// Mendefinisikan `props` yang akan diterima komponen dari parent:
const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    sortBy: {
        type: String,
        required: true,
    },
    sortOrder: {
        type: String,
        required: true,
    },
});

// Mendefinisikan `emit` untuk mengirim event ke komponen parent.
const emit = defineEmits(["sort"]);

// `handleSort` adalah fungsi yang memicu event `sort` dan mengirimkan nama kolom yang akan disortir.
const handleSort = (field) => {
    emit("sort", field);
};

// Format Timestamp
const formatTimestamp = (isoString) => {
    try {
        return new Date(isoString)
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
    <div class="overflow-x-auto">
        <table
            class="w-full table-fixed border-separate border-spacing-0 border shadow-lg rounded-lg"
        >
            <!-- Header tabel dengan kolom untuk setiap data yang akan ditampilkan -->
            <thead>
                <tr class="text-sm font-medium text-gray-500 border-b">
                    <th class="w-[15%] px-6 py-3 text-left">
                        <div
                            class="flex items-center cursor-pointer"
                            @click="handleSort('application')"
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
                            @click="handleSort('application')"
                        >
                            <span class="whitespace-nowrap">TIMESTAMP</span>
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
                    <th class="w-[30%] px-6 py-3 text-left">
                        <span class="whitespace-nowrap">ERROR MESSAGE</span>
                    </th>
                </tr>
                <tr>
                    <td colspan="6" class="border-b border-gray-300"></td>
                </tr>
            </thead>
            <!-- Body tabel untuk menampilkan data email log -->
            <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="item in data"
                    :key="item.id"
                    class="text-sm text-gray-800 hover:bg-gray-50"
                >
                    <td class="px-6 py-4 font-medium">
                        {{ item.application.name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ JSON.parse(item.request).to }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium">
                            {{ JSON.parse(item.request).subject }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span
                            :class="[
                                'px-3 py-1 text-xs font-medium rounded-full whitespace-nowrap',
                                item.status === 'success'
                                    ? 'text-green-700 bg-green-100'
                                    : 'text-red-700 bg-red-100',
                            ]"
                        >
                            {{
                                item.status === "success" ? "Terkirim" : "Gagal"
                            }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                        {{ formatTimestamp(item.created_at) }}
                    </td>
                    <td class="px-6 py-4 text-red-500">
                        {{ item.error_message || "-" }}
                    </td>
                </tr>
                <!-- Pesan jika tidak ada data yang ditemukan -->
                <tr v-if="data.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data yang ditemukan!
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
