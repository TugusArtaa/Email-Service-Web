<script setup>
// Mendefinisikan props yang diterima oleh komponen ini
const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    displayRange: {
        type: Object,
        required: true,
        validator: (value) => {
            return "start" in value && "end" in value && "total" in value;
        },
    },
});

// Mendefinisikan `emit` untuk memancarkan event "page-change" ketika halaman berubah.
const emit = defineEmits(["page-change"]);

// Fungsi `handlePageChange` untuk mengubah halaman.
const handlePageChange = (page) => {
    emit("page-change", page);
};
</script>

<template>
    <div class="flex items-center justify-between mt-4 px-4">
        <!-- Menampilkan rentang data saat ini dalam format teks -->
        <div class="text-sm text-gray-500">
            Showing {{ displayRange.start }} to {{ displayRange.end }} of
            {{ displayRange.total }} entries
        </div>
        <div class="flex gap-2">
            <!-- Navigasi pagination -->
            <!-- Tombol "Previous" untuk berpindah ke halaman sebelumnya -->
            <button
                @click="handlePageChange(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50"
            >
                Previous
            </button>
            <!-- Tombol untuk setiap halaman dalam pagination -->
            <button
                v-for="page in totalPages"
                :key="page"
                @click="handlePageChange(page)"
                :class="[
                    'px-3 py-1 text-sm border rounded hover:bg-gray-50',
                    currentPage === page ? 'bg-blue-50 text-green-600' : '',
                ]"
            >
                {{ page }}
            </button>
            <!-- Tombol "Next" untuk berpindah ke halaman berikutnya -->
            <button
                @click="handlePageChange(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50 disabled:opacity-50"
            >
                Next
            </button>
        </div>
    </div>
</template>
