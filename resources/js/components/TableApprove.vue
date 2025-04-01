<script setup>
import { ref, computed } from "vue";
import { Tippy } from "vue-tippy";
import "tippy.js/dist/tippy.css";

// Mendefinisikan properti komponen
const props = defineProps({
    data: Object,
    thead: Array,
    isFetching: Boolean,
    page: Number,
});

// Mendefinisikan event yang akan diemit
const emit = defineEmits(["approve", "reject"]);

// Fungsi untuk membuka modal approve
const openApproveModal = (application) => {
    emit("approve", application);
};

// Fungsi untuk membuka modal reject
const openRejectModal = (application) => {
    emit("reject", application);
};

// Menghitung nomor yang diincrement berdasarkan data
const incrementedNumbers = computed(() => {
    if (!props.data || !props.data.data) return [];
    return props.data.data.map((_, index) => props.data.from + index);
});
</script>

<template>
    <!-- Menampilkan pesan alert jika ada -->
    <div
        v-if="alertMessage"
        :class="`flex items-center p-4 mb-4 text-sm border rounded-lg ${
            alertType === 'success'
                ? 'text-green-800 border-green-300 bg-green-50'
                : 'text-red-800 border-red-300 bg-red-50'
        }`"
        role="alert"
    >
        <svg
            class="flex-shrink-0 inline w-4 h-4 me-3"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
            />
        </svg>
        <span class="sr-only">Info</span>
        <div>
            <span class="font-medium">{{
                alertType === "success" ? "Success alert!" : "Danger alert!"
            }}</span>
            {{ alertMessage }}
        </div>
    </div>
    <!-- Tabel data aplikasi -->
    <table class="w-full text-sm text-left text-gray-500 border rounded-2xl">
        <thead class="text-xs text-gray-700 uppercase border-b bg-gray-50">
            <tr>
                <!-- Header tabel -->
                <th
                    scope="col"
                    class="px-6 py-3"
                    v-for="item in thead"
                    :key="item"
                >
                    {{ item }}
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- Menampilkan pesan jika tidak ada data -->
            <tr v-if="data.data.length === 0 && isFetching == false">
                <td colspan="6" class="px-6 py-4 text-lg font-bold text-center">
                    <div class="flex flex-col items-center justify-center">
                        <img
                            :src="'/NotFound.png'"
                            alt="Tidak ada data"
                            class="w-80 h-44 mb-3"
                        />
                        <p class="text-lg font-bold text-gray-500">
                            Tidak ada data yang ditemukan!
                        </p>
                        <p class="text-sm text-gray-400">
                            Belum ada data yang ditambahkan.
                        </p>
                    </div>
                </td>
            </tr>
            <!-- Menampilkan loading spinner saat fetching data -->
            <tr v-if="isFetching">
                <td colspan="6" class="px-6 py-4 text-lg font-bold text-center">
                    <div role="status">
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </td>
            </tr>
            <!-- Menampilkan data aplikasi -->
            <tr
                v-for="(item, index) in data.data"
                :key="item.id"
                class="hover:bg-gray-50"
            >
                <!-- Nomor urut -->
                <td class="px-5 py-5 bg-white text-sm">
                    {{ incrementedNumbers[index] }}
                </td>
                <!-- Nama aplikasi -->
                <td class="px-5 py-5 bg-white text-sm">
                    {{ item.name }}
                </td>
                <!-- Nama PIC -->
                <td class="px-5 py-5 bg-white text-sm">
                    {{ item.pic_name }}
                </td>
                <!-- Tanggal pembuatan -->
                <td class="px-5 py-5 bg-white text-sm">
                    {{ new Date(item.created_at).toLocaleString() }}
                </td>
                <!-- Status aplikasi -->
                <td class="items-center px-5 py-5 bg-white text-sm">
                    <span
                        :class="[
                            'px-2 py-1 rounded-full text-white',
                            item.status === 'pending'
                                ? 'bg-yellow-500'
                                : 'bg-yellow-500',
                        ]"
                    >
                        {{ item.status }}
                    </span>
                </td>
                <!-- Tombol aksi -->
                <td
                    class="flex items-center space-x-2 px-5 py-5 bg-white text-sm"
                >
                    <!-- Tombol approve aplikasi -->
                    <Tippy content="Approve application">
                        <button
                            @click="() => openApproveModal(item)"
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-green-500 rounded-lg focus:outline-none hover:bg-green-700 focus:ring-4 focus:ring-blue-300"
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
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <!-- Tombol reject aplikasi -->
                    <Tippy content="Reject application">
                        <button
                            @click="() => openRejectModal(item)"
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-red-500 rounded-lg focus:outline-none hover:bg-red-700 focus:ring-4 focus:ring-blue-300"
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
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </Tippy>
                </td>
            </tr>
        </tbody>
    </table>
</template>
