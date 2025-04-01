<script setup>
//Impor fungsi reaktif dari vue
import { reactive, toRefs, watch } from "vue";

// Mendefinisikan properti `notification` yang wajib diisi dan bertipe objek
const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
});

// Mendefinisikan event `close` yang dapat dipancarkan (emit)
const emit = defineEmits(["close"]);

// Fungsi untuk menutup notifikasi dengan memancarkan event `close`
const closeNotification = () => {
    emit("close");
};

// Mengawasi perubahan pada `notification.show`
watch(
    () => props.notification.show,
    (newVal) => {
        if (newVal) {
            setTimeout(() => {
                emit("close");
            }, 3000);
        }
    }
);
</script>

<template>
    <!-- Komponen notifikasi hanya ditampilkan jika `notification.show` bernilai true -->
    <div
        v-if="notification.show"
        class="fixed top-4 right-4 z-50 max-w-sm w-full pointer-events-auto transition-all duration-300 transform translate-y-0 opacity-100"
        :class="[
            notification.type === 'success' ? 'bg-green-100' : 'bg-red-100',
            'backdrop-blur-sm bg-opacity-90 border',
            notification.type === 'success'
                ? 'border-green-300'
                : 'border-red-300',
        ]"
        style="
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.75rem;
        "
    >
        <div class="p-4 flex items-start">
            <div class="flex-shrink-0">
                <div
                    v-if="notification.type === 'success'"
                    class="h-10 w-10 rounded-full flex items-center justify-center"
                    style="
                        background: linear-gradient(145deg, #dcfce7, #bbf7d0);
                    "
                >
                    <svg
                        class="h-6 w-6 text-green-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                <div
                    v-else
                    class="h-10 w-10 rounded-full flex items-center justify-center"
                    style="
                        background: linear-gradient(145deg, #fee2e2, #fecaca);
                    "
                >
                    <svg
                        class="h-6 w-6 text-red-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </div>
            </div>
            <!-- Konten notifikasi: pesan dan deskripsi -->
            <div class="ml-4 flex-1">
                <p
                    class="text-base font-medium"
                    :class="
                        notification.type === 'success'
                            ? 'text-green-800'
                            : 'text-red-800'
                    "
                >
                    {{ notification.message }}
                </p>
                <p
                    class="mt-1 text-sm"
                    :class="
                        notification.type === 'success'
                            ? 'text-green-600'
                            : 'text-red-600'
                    "
                >
                    {{ notification.description || "" }}
                </p>
            </div>
            <!-- Tombol untuk menutup notifikasi -->
            <button
                @click="closeNotification"
                class="ml-4 p-1 rounded-full hover:bg-gray-200 transition-colors duration-200"
                :class="
                    notification.type === 'success'
                        ? 'text-green-500 hover:text-green-700'
                        : 'text-red-500 hover:text-red-700'
                "
            >
                <span class="sr-only">Close</span>
                <svg
                    class="h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>
