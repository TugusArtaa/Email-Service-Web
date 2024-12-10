<script setup>
// Impor modul dan komponen yang diperlukan
import { ref, watch } from "vue";
import { defineProps, computed } from "vue";
import { onClickOutside, useFetch } from "@vueuse/core";
import { useForm, usePage } from "@inertiajs/vue3";
import { Tippy } from "vue-tippy";
import "tippy.js/dist/tippy.css";

// Mendefinisikan properti komponen
const props = defineProps({
    data: Object,
    thead: Array,
    title: String,
    isFetching: Boolean,
});

// Mendefinisikan event yang akan diemit
const emit = defineEmits(["checkbox", "refresh"]);

// Mendefinisikan variabel reaktif
const checked = ref([]);
const application = ref({});

// State untuk modal hapus
const deleteModal = ref(null);
const showDeleteModal = ref(false);
const deleteOne = ref([]);

// State untuk modal regenerasi kunci
const keyModal = ref(null);
const showKeyModal = ref(false);
const keyId = ref(null);

// State untuk modal detail
const detailModal = ref(null);
const showDetailModal = ref(false);
const detailFetch = ref(false);

// State untuk modal status
const statusModal = ref(null);
const showStatusModal = ref(false);
const statusId = ref(null);
const currentStatus = ref(null);

// State untuk pesan alert
const alertMessage = ref("");
const alertType = ref("");

// Mengawasi perubahan pada variabel checked dan emit event
watch(checked, (newValue) => {
    emit("checkbox", newValue);
});

// Menghitung nomor yang diincrement berdasarkan data
const incrementedNumbers = computed(() => {
    if (!props.data || !props.data.data) return [];
    return props.data.data.map((_, index) => props.data.from + index);
});

// Menutup modal hapus saat klik di luar modal
onClickOutside(deleteModal, (event) => {
    showDeleteModal.value = false;
});

// Menutup modal detail saat klik di luar modal
onClickOutside(detailModal, (event) => {
    showDetailModal.value = false;
});

// Inisialisasi form untuk operasi hapus
const pageInertia = usePage();
const form = useForm({
    ids: deleteOne.value,
    _token: pageInertia.props.csrf_token,
});

// Mengawasi perubahan pada deleteOne dan memperbarui form
watch(deleteOne, (newValue) => {
    form.ids = [newValue];
});

// Fungsi untuk menghapus aplikasi
function deleteApp() {
    fetch("/api/applications/delete", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": pageInertia.props.csrf_token,
        },
        body: JSON.stringify({ ids: form.ids }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                emit("refresh");
                showDeleteModal.value = false;
                alertMessage.value = data.message;
                alertType.value = "success";
                setTimeout(clearAlert, 4000);
            } else {
                alertMessage.value = data.message;
                alertType.value = "error";
                setTimeout(clearAlert, 4000);
            }
        })
        .catch((error) => {
            console.error(error);
            alertMessage.value =
                "An error occurred while deleting the application.";
            alertType.value = "error";
            setTimeout(clearAlert, 4000);
        });
}

// URL dasar untuk permintaan API
const baseUrl = import.meta.env.VITE_APP_URL;

// Fungsi untuk mendapatkan detail aplikasi
function getDetail(id) {
    detailFetch.value = false;
    application.value = {};
    const fetchData = useFetch(`${baseUrl}/api/applications/${id}`)
        .get()
        .json();
    watch(fetchData.data, (newData) => {
        if (newData) {
            application.value = newData.data.application;
            detailFetch.value = true;
        }
    });
}

// Inisialisasi form untuk regenerasi kunci
const formKey = useForm({
    id: keyId.value,
    status: "pending",
    _token: pageInertia.props.csrf_token,
});

// Mengawasi perubahan pada keyId dan memperbarui form
watch(keyId, (newValue) => {
    formKey.id = newValue;
});

// Fungsi untuk meregenerasi kunci rahasia
async function generateKey() {
    try {
        const response = await fetch(
            `/api/applications/application-status-change`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": pageInertia.props.csrf_token,
                },
                body: JSON.stringify({
                    id: keyId.value,
                    status: "pending",
                }),
            }
        );

        const data = await response.json();

        if (data.success) {
            emit("refresh");
            showKeyModal.value = false;
            alertMessage.value = "Secret key regenerated request successfully!";
            alertType.value = "success";
            setTimeout(clearAlert, 4000);
        } else {
            emit("refresh");
            showKeyModal.value = false;
            alertMessage.value = data.message;
            alertType.value = "error";
            setTimeout(clearAlert, 4000);
        }
    } catch (error) {
        console.error(error);
        showKeyModal.value = false;
        alertMessage.value =
            "An error occurred while regenerating the secret key.";
        alertType.value = "error";
        setTimeout(clearAlert, 4000);
    }
}

// Fungsi untuk membuka modal konfirmasi status
function openStatusModal(id, status) {
    statusId.value = id;
    currentStatus.value = status;
    showStatusModal.value = true;
}

// Fungsi untuk mengubah status aplikasi
async function changeStatus() {
    const newStatus =
        currentStatus.value === "enabled" ? "disabled" : "enabled";
    try {
        const response = await fetch(
            `/api/applications/application-status-change`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": pageInertia.props.csrf_token,
                },
                body: JSON.stringify({ id: statusId.value, status: newStatus }),
            }
        );

        const data = await response.json();

        if (data.success) {
            emit("refresh");
            showStatusModal.value = false;
            alertMessage.value = `Application status changed to ${newStatus}.`;
            alertType.value = "success";
        } else {
            alertMessage.value = data.message;
            alertType.value = "error";
        }
        setTimeout(clearAlert, 4000);
    } catch (error) {
        console.error(error);
        alertMessage.value =
            "An error occurred while changing the application status.";
        alertType.value = "error";
        setTimeout(clearAlert, 4000);
    }
}

// Fungsi untuk membersihkan pesan alert
function clearAlert() {
    alertMessage.value = "";
    alertType.value = "";
}
</script>

<template>
    <!-- Menampilkan pesan alert jika ada -->
    <div
        v-if="alertMessage"
        :class="`flex items-center p-4 mb-4 text-sm border rounded-lg ${
            alertType === 'success'
                ? 'text-green-800 border-green-300 bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800'
                : 'text-red-800 border-red-300 bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800'
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
    <table
        class="w-full text-sm text-left text-gray-500 border dark:text-gray-400 rounded-2xl"
    >
        <thead
            class="text-xs text-gray-700 uppercase border-b bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
        >
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
                    Tidak ada data yang ditemukan!
                </td>
            </tr>
            <!-- Menampilkan loading spinner saat fetching data -->
            <tr v-if="isFetching">
                <td colspan="6" class="px-6 py-4 text-lg font-bold text-center">
                    <div role="status">
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500"
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
                :class="{
                    'bg-red-50 hover:bg-red-100': checked.includes(item.id),
                }"
                class="dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
                <!-- input checkbox -->
                <td class="px-5 py-5 bg-white text-sm">
                    <input
                        type="checkbox"
                        v-model="checked"
                        :value="item.id"
                        @change="handleCheckboxChange"
                    />
                </td>
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
                            item.status === 'enabled'
                                ? 'bg-green-600'
                                : 'bg-red-600',
                        ]"
                    >
                        {{ item.status }}
                    </span>
                </td>
                <!-- Tombol aksi -->
                <td
                    class="flex items-center space-x-2 px-5 py-5 bg-white text-sm"
                >
                    <!-- Tombol detail aplikasi -->
                    <Tippy content="Detail application">
                        <button
                            @click="
                                showDetailModal = true;
                                getDetail(item.id);
                            "
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-emerald-500 rounded-lg focus:outline-none hover:bg-emerald-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-red-900"
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
                                    d="m21 21-3.5-3.5M10 7v6m-3-3h6m4 0a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <!-- Tombol regenerasi kunci rahasia -->
                    <Tippy content="Regenerate secret key">
                        <button
                            @click="
                                showKeyModal = true;
                                keyId = item.id;
                            "
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-yellow-500 rounded-lg focus:outline-none hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-red-900"
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
                                    fill="currentColor"
                                    d="M6.94318 11h-.85227l.96023-2.90909h1.07954L9.09091 11h-.85227l-.63637-2.10795h-.02272L6.94318 11Zm-.15909-1.14773h1.60227v.59093H6.78409v-.59093ZM9.37109 11V8.09091h1.25571c.2159 0 .4048.04261.5667.12784.162.08523.2879.20502.3779.35937.0899.15436.1349.33476.1349.5412 0 .20833-.0464.38873-.1392.54119-.0918.15246-.2211.26989-.3878.35229-.1657.0824-.3593.1236-.5809.1236h-.75003v-.61367h.59093c.0928 0 .1719-.0161.2372-.0483.0663-.03314.1169-.08002.152-.14062.036-.06061.054-.13211.054-.21449 0-.08334-.018-.15436-.054-.21307-.0351-.05966-.0857-.10511-.152-.13636-.0653-.0322-.1444-.0483-.2372-.0483h-.2784V11h-.78981Zm3.41481-2.90909V11h-.7898V8.09091h.7898Z"
                                />
                                <path
                                    stroke="currentColor"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.31818 2c-.55228 0-1 .44772-1 1v.72878c-.06079.0236-.12113.04809-.18098.07346l-.55228-.53789c-.38828-.37817-1.00715-.37817-1.39543 0L3.30923 5.09564c-.19327.18824-.30229.44659-.30229.71638 0 .26979.10902.52813.30229.71637l.52844.51468c-.01982.04526-.03911.0908-.05785.13662H3c-.55228 0-1 .44771-1 1v2.58981c0 .5523.44772 1 1 1h.77982c.01873.0458.03802.0914.05783.1366l-.52847.5147c-.19327.1883-.30228.4466-.30228.7164 0 .2698.10901.5281.30228.7164l1.88026 1.8313c.38828.3781 1.00715.3781 1.39544 0l.55228-.5379c.05987.0253.12021.0498.18102.0734v.7288c0 .5523.44772 1 1 1h2.65912c.5523 0 1-.4477 1-1v-.7288c.1316-.0511.2612-.1064.3883-.1657l.5435.2614v.4339c0 .5523.4477 1 1 1H14v.0625c0 .5523.4477 1 1 1h.0909v.0625c0 .5523.4477 1 1 1h.6844l.4952.4823c1.1648 1.1345 3.0214 1.1345 4.1863 0l.2409-.2347c.1961-.191.3053-.454.3022-.7277-.0031-.2737-.1183-.5342-.3187-.7207l-6.2162-5.7847c.0173-.0398.0342-.0798.0506-.12h.7799c.5522 0 1-.4477 1-1V8.17969c0-.55229-.4478-1-1-1h-.7799c-.0187-.04583-.038-.09139-.0578-.13666l.5284-.51464c.1933-.18824.3023-.44659.3023-.71638 0-.26979-.109-.52813-.3023-.71637l-1.8803-1.8313c-.3883-.37816-1.0071-.37816-1.3954 0l-.5523.53788c-.0598-.02536-.1201-.04985-.1809-.07344V3c0-.55228-.4477-1-1-1H8.31818Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                                        <!-- Tombol ubah status aplikasi -->
                                        <Tippy content="Change status">
                        <button
                            @click="openStatusModal(item.id, item.status)"
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-blue-500 rounded-lg focus:outline-none hover:bg-blue-700 focus:ring-4 focus:ring-purple-300 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-red-900"
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
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <!-- Tombol hapus aplikasi -->
                    <Tippy content="Delete application">
                        <button
                            @click="
                                showDeleteModal = true;
                                deleteOne = item.id;
                            "
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-red-500 rounded-lg focus:outline-none hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
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
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Modal konfirmasi hapus -->
    <div
        tabindex="-1"
        v-show="showDeleteModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="deleteModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showDeleteModal = false"
                    type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <!-- Ikon -->
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                    <!-- Judul -->
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to delete this application?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="deleteApp"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showDeleteModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-500 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    >
                        No, cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal detail aplikasi -->
    <div
        v-if="showDetailModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 backdrop-blur-[2px] bg-opacity-40 md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="detailModal"
            >
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Detail Application
                    </h3>
                    <!-- Tombol tutup modal -->
                    <button
                        @click="showDetailModal = false"
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal"
                    >
                        <svg
                            class="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5">
                    <div
                        v-show="detailFetch"
                        class="grid grid-cols-2 gap-4 mb-4"
                    >
                        <div class="col-span-2">
                            <!-- Nama aplikasi -->
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Application name</label
                            >
                            <!-- Input nama aplikasi -->
                            <input
                                readonly
                                disabled
                                type="text"
                                v-model="application.name"
                                name="name"
                                id="name"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <!-- Kunci rahasia -->
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Secret key</label
                            >
                            <!-- Input kunci rahasia -->
                            <input
                                readonly
                                disabled
                                type="text"
                                v-model="application.secret_key"
                                name="name"
                                id="name"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <!-- Nama PIC -->
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                >Application Description</label
                            >
                            <!-- Input deskripsi aplikasi -->
                            <textarea
                                readonly
                                disabled
                                v-model="application.description"
                                id="description"
                                rows="4"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"
                            ></textarea>
                        </div>
                    </div>
                    <!-- Loading spinner saat fetching data -->
                    <div
                        v-show="!detailFetch"
                        role="status"
                        class="flex justify-center"
                    >
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500"
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
                </form>
            </div>
        </div>
    </div>
    <!-- Modal konfirmasi regenerasi kunci -->
    <div
        tabindex="-1"
        v-show="showKeyModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800"
                ref="keyModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showKeyModal = false"
                    type="button"
                    class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-4 h-4"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <!-- Isi modal -->
                <div class="p-6 text-center">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                    <!-- Judul modal -->
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to regenerate this secret key?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="generateKey"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-yellow-500 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showKeyModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-yellow-500 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    >
                        No, cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal konfirmasi ubah status -->
    <div
        tabindex="-1"
        v-show="showStatusModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div
                class="relative bg-white rounded-lg shadow dark:bg-gray-700"
                ref="statusModal"
            >
                <!-- Tombol tutup modal -->
                <button
                    @click="showStatusModal = false"
                    type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                    <!-- Pesan konfirmasi -->
                    <h3
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                    >
                        Are you sure you want to change the status of this
                        application?
                    </h3>
                    <!-- Tombol konfirmasi -->
                    <button
                        @click="changeStatus()"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-blue-500 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Yes, I'm sure
                    </button>
                    <!-- Tombol batal -->
                    <button
                        @click="showStatusModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-500 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    >
                        No, cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
