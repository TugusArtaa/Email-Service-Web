<script setup>
// Mendefinisikan props yang dapat diterima oleh komponen dari parent.
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: Number,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "default",
        validator: (value) => ["success", "error", "default"].includes(value),
    },
});

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

<template>
    <!-- Struktur utama card -->
    <div class="p-4 bg-white rounded-lg shadow hover:-translate-y-1 hover:border-green-400 border-2 transition duration-200">
        <div class="flex items-center justify-between">
            <!-- Bagian kiri card yang menampilkan teks -->
            <div>
                <p class="text-sm text-gray-500">{{ title }}</p>
                <h3 class="text-2xl font-semibold">{{ value }}</h3>
                <p class="text-sm" :class="getColorClass(type).text">
                    {{ subtitle }}
                </p>
            </div>
            <!-- Bagian kanan card menampilkan ikon SVG dengan warna background berdasarkan `type` -->
            <div class="p-3 rounded-lg" :class="getColorClass(type).bg">
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
</template>
