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
    <div
        v-if="notification.show"
        class="fixed top-3 right-3 z-50 max-w-sm w-full pointer-events-auto transition-all duration-500 transform translate-y-0 opacity-100"
        :class="[
            notification.type === 'success' ? 'bg-green-50' : 'bg-red-50',
            'backdrop-blur-md bg-opacity-95 border-l-3',
            notification.type === 'success'
                ? 'border-l-green-500'
                : 'border-l-red-500',
        ]"
        style="
            box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1),
                0 8px 8px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.75rem;
        "
    >
        <div class="p-4 flex items-start">
            <div class="flex-shrink-0">
                <div
                    v-if="notification.type === 'success'"
                    class="h-10 w-10 rounded-full flex items-center justify-center shadow-md"
                    style="
                        background: linear-gradient(145deg, #10b981, #34d399);
                    "
                >
                    <svg
                        class="h-5 w-5 text-white"
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
                    class="h-10 w-10 rounded-full flex items-center justify-center shadow-md"
                    style="
                        background: linear-gradient(145deg, #ef4444, #f87171);
                    "
                >
                    <svg
                        class="h-5 w-5 text-white"
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
                            ? 'text-green-700'
                            : 'text-red-700'
                    "
                >
                    {{ notification.message }}
                </p>
                <p
                    class="mt-0.5 text-sm"
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
                class="ml-3 p-1.5 rounded-full transition-colors duration-200 hover:bg-opacity-20"
                :class="
                    notification.type === 'success'
                        ? 'text-green-600 hover:bg-green-200'
                        : 'text-red-600 hover:bg-red-200'
                "
            >
                <span class="sr-only">Close</span>
                <svg
                    class="h-4.5 w-4.5"
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
