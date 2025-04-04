<script setup>
// Import modul dan library yang diperlukan
import { ref, onMounted, watch } from "vue";
import { vAutoAnimate } from "@formkit/auto-animate";
import axios from "axios";

// Base URL untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// State untuk mengontrol modal form
const formModal = ref(false);

// Emit event ke parent component
const emit = defineEmits(["close", "notification"]);

// State untuk menyimpan data email yang akan dikirim
const emails = ref({
    secret: "",
    mail: [],
});

// State untuk menyimpan data email yang sedang diedit atau ditambahkan
const email = ref({
    subject: "",
    to: "",
    priority: "low",
    content: "",
    attachment: [],
});

// State untuk pesan sukses, error, dan validasi
const successMessage = ref("");
const errorMessage = ref({});
const invalidKey = ref("");
const detailErrors = ref({});
const emailIndex = ref(null);

// Lifecycle hook untuk mengambil data email dari localStorage
onMounted(() => {
    const storedEmails = localStorage.getItem("emails");
    if (storedEmails) {
        emails.value = JSON.parse(storedEmails);
    }
});

// Watcher untuk menyimpan perubahan data emails ke localStorage
watch(
    emails,
    (newEmails) => {
        localStorage.setItem("emails", JSON.stringify(newEmails));
    },
    { deep: true }
);

// Fungsi untuk mengirim email
async function handleSubmit() {
    invalidKey.value = "";
    try {
        const response = await axios.post(
            `${baseUrl}/api/email-queue/send`,
            emails.value
        );
        // Emit notification success
        emit("notification", "success", "Berhasil!", response.data.message);
        emit("close");
    } catch (error) {
        if (
            error.response &&
            error.response.data &&
            error.response.data.errors
        ) {
            errorMessage.value = error.response.data.errors;
        } else if (error.response && error.response.data) {
            invalidKey.value = error.response.data.error;
        } else {
            errorMessage.value = {
                general: ["Terjadi kesalahan yang tidak diketahui"],
            };
        }
        // Emit notification error
        emit(
            "notification",
            "danger",
            "Gagal!",
            error.response?.data?.message ||
                "Terjadi kesalahan yang tidak diketahui"
        );
    }
}

// Fungsi untuk validasi data email
function validateEmail() {
    let isValid = true;
    const tempErrors = {};
    // Validasi alamat email penerima
    if (!email.value.to) {
        if (!tempErrors[`mail.${emailIndex.value}.to`]) {
            tempErrors[`mail.${emailIndex.value}.to`] = [];
        }
        tempErrors[`mail.${emailIndex.value}.to`].push(
            "Alamat email penerima wajib diisi"
        );
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.to)) {
        if (!tempErrors[`mail.${emailIndex.value}.to`]) {
            tempErrors[`mail.${emailIndex.value}.to`] = [];
        }
        tempErrors[`mail.${emailIndex.value}.to`].push(
            "Format email tidak valid"
        );
        isValid = false;
    }
    // Validasi prioritas
    if (!email.value.priority) {
        if (!tempErrors[`mail.${emailIndex.value}.priority`]) {
            tempErrors[`mail.${emailIndex.value}.priority`] = [];
        }
        tempErrors[`mail.${emailIndex.value}.priority`].push(
            "Prioritas wajib dipilih"
        );
        isValid = false;
    }
    // Validasi secret key sebelum menambahkan email
    if (!emails.value.secret) {
        invalidKey.value = "Secret key wajib diisi";
        isValid = false;
    } else {
        invalidKey.value = "";
    }

    detailErrors.value = tempErrors;
    return isValid;
}

// Fungsi untuk menambahkan email baru
function addEmail(newEmail) {
    // Tetapkan indeks email baru
    emailIndex.value = emails.value.mail.length;
    // Gunakan fungsi validateEmail untuk validasi
    if (validateEmail()) {
        // Jika validasi berhasil, tambahkan email ke daftar
        emails.value.mail.push(newEmail);
        // Tutup modal
        formModal.value = false;
        // Reset pesan error
        errorMessage.value = {};
        invalidKey.value = "";
        detailErrors.value = {};
    }
}

// Fungsi untuk melihat detail email
function detailEmail(index) {
    detailErrors.value = Object.keys(errorMessage.value || {})
        .filter((key) => key.startsWith(`mail.${index}.`))
        .reduce((acc, key) => {
            acc[key] = errorMessage.value[key];
            return acc;
        }, {});
    email.value = JSON.parse(JSON.stringify(emails.value.mail[index]));
    emailIndex.value = index;
}

// Fungsi untuk membuat email baru
function newEmail() {
    detailErrors.value = {};
    email.value = {
        subject: "",
        to: "",
        priority: "low",
        content: "",
        attachment: [],
    };
    invalidKey.value = "";
    emailIndex.value = null;
}

// Fungsi untuk menyimpan perubahan pada email yang diedit
function saveEdit() {
    if (validateEmail()) {
        emails.value.mail[emailIndex.value] = email.value;
        formModal.value = false;
        errorMessage.value = Object.keys(errorMessage.value || {})
            .filter((key) => !key.startsWith(`mail.${emailIndex.value}.`))
            .reduce((acc, key) => {
                acc[key] = errorMessage.value[key];
                return acc;
            }, {});
        invalidKey.value = "";
        detailErrors.value = {};
    }
}

// Fungsi untuk menghapus email dari daftar
function removeEmail(index) {
    emails.value.mail.splice(index, 1);
}

// Fungsi untuk menambahkan lampiran
function addAttachment() {
    email.value.attachment.push("");
}

// Fungsi untuk menghapus lampiran
function removeAttachment(index) {
    email.value.attachment.splice(index, 1);
}

// Fungsi untuk validasi sebelum mengirim email
function validateBeforeSubmit() {
    if (!emails.value.secret) {
        invalidKey.value = "Secret key wajib diisi";
        return false;
    }
    if (emails.value.mail.length === 0) {
        invalidKey.value = "Tambahkan minimal satu email";
        return false;
    }
    return true;
}
</script>

<template>
    <div>
        <!-- Background overlay untuk modal -->
        <div
            @click="emit('close')"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full max-h-full p-4 overflow-x-hidden overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm md:inset-0"
        ></div>

        <!-- Kontainer modal -->
        <div
            class="fixed z-50 w-full max-h-full -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2 max-w-7xl"
        >
            <!-- Konten Modal -->
            <form class="relative bg-white rounded-lg shadow">
                <!-- Header Modal -->
                <div
                    class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-emerald-500 to-teal-500 rounded-t md:p-5"
                >
                    <h3 class="text-xl font-bold text-white">Tambah Email</h3>
                    <!-- Tombol tutup modal -->
                    <button
                        type="button"
                        @click="emit('close')"
                        class="text-white/80 hover:text-white transition-colors focus:outline-none"
                        data-modal-hide="extralarge-modal"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Pesan Error Umum -->
                <div
                    v-if="
                        Object.keys(errorMessage).length > 0 &&
                        formModal === false
                    "
                    class="p-4 mb-4 text-sm text-red-700 bg-red-100 flex items-center"
                    role="alert"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 mr-2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                        />
                    </svg>
                    <span>
                        Terjadi kesalahan. Harap edit baris dengan latar
                        belakang merah untuk mengetahui detail kesalahan.
                    </span>
                </div>

                <!-- Pesan error untuk secret key -->
                <div
                    v-if="invalidKey && formModal === false"
                    class="p-4 mb-4 text-sm text-red-700 bg-red-100 flex items-center"
                    role="alert"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5 mr-2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                        />
                    </svg>
                    <span>{{ invalidKey }}</span>
                </div>

                <!-- Tabel Daftar Email -->
                <div
                    v-show="!formModal"
                    class="relative p-2 overflow-x-auto sm:rounded-lg"
                >
                    <table
                        class="w-full text-sm text-left text-gray-500 rtl:text-right"
                    >
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Subjek</th>
                                <th scope="col" class="px-6 py-3">Kepada</th>
                                <th scope="col" class="px-6 py-3">Prioritas</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris Email -->
                            <tr
                                v-for="(item, index) in emails.mail"
                                :class="{
                                    'bg-red-100 hover:bg-red-200':
                                        Object.keys(errorMessage).some((key) =>
                                            key.startsWith(`mail.${index}.`)
                                        ) ||
                                        (invalidKey && emails.mail.length > 0),
                                }"
                                class="border-gray-200 hover:bg-gray-50"
                            >
                                <td class="px-6 py-4">
                                    {{ index + 1 }}
                                </td>
                                <th
                                    scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                                >
                                    {{ item.subject }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ item.to }}
                                </td>
                                <td class="px-6 py-4">
                                    {{
                                        item.priority.charAt(0).toUpperCase() +
                                        item.priority.slice(1)
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    <a
                                        @click="
                                            (formModal = true),
                                                detailEmail(index),
                                                (emailIndex = index)
                                        "
                                        href="#"
                                        class="font-medium text-blue-600 hover:underline"
                                        >Edit</a
                                    >
                                    <a
                                        @click="removeEmail(index)"
                                        href="#"
                                        class="font-medium text-red-700 ms-3"
                                        >Hapus</a
                                    >
                                </td>
                            </tr>
                            <!-- Baris tambah email -->
                            <tr>
                                <td
                                    @click="newEmail(), (formModal = true)"
                                    class="px-6 py-4 text-center text-green-700 border border-green-200 cursor-pointer"
                                    colspan="5"
                                >
                                    Tambah Email Baru
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Form modal untuk tambah/edit email -->
                <div
                    v-show="formModal"
                    class="flex flex-col p-4 md:p-5 gap-x-5 gap-y-4 max-h-[70vh] overflow-auto"
                >
                    <div>
                        <label
                            for="subject"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Subjek</label
                        >
                        <input
                            type="text"
                            id="subject"
                            v-model="email.subject"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Contoh"
                            required
                        />
                        <div
                            v-if="
                                detailErrors['mail.' + emailIndex + '.subject']
                            "
                        >
                            <p
                                v-for="subjectErr in detailErrors[
                                    'mail.' + emailIndex + '.subject'
                                ]"
                                class="text-sm text-red-700"
                            >
                                {{
                                    subjectErr.replace(
                                        `mail.${emailIndex}.`,
                                        ""
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label
                            for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Kepada</label
                        >
                        <input
                            type="email"
                            id="first_name"
                            v-model="email.to"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="nama@contoh.com"
                            required
                        />
                        <div v-if="detailErrors['mail.' + emailIndex + '.to']">
                            <p
                                v-for="toErr in detailErrors[
                                    'mail.' + emailIndex + '.to'
                                ]"
                                class="text-sm text-red-700"
                            >
                                {{ toErr.replace(`mail.${emailIndex}.`, "") }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label
                            for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Pilih Prioritas</label
                        >
                        <select
                            id="countries"
                            v-model="email.priority"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <div
                            v-if="
                                detailErrors['mail.' + emailIndex + '.priority']
                            "
                        >
                            <p
                                v-for="priorityErr in detailErrors[
                                    'mail.' + emailIndex + '.priority'
                                ]"
                                class="text-sm text-red-700"
                            >
                                {{
                                    priorityErr.replace(
                                        `mail.${emailIndex}.`,
                                        ""
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label
                            for="secret"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Secret key</label
                        >
                        <input
                            type="text"
                            id="secret"
                            v-model="emails.secret"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Masukkan Secret key"
                            required
                        />
                        <div v-if="invalidKey && formModal">
                            <p class="text-sm text-red-700">
                                {{ invalidKey }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label
                            for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Lampiran</label
                        >
                        <div class="flex flex-col gap-y-2" v-auto-animate>
                            <div
                                v-for="(item, index) in email.attachment"
                                :key="index"
                                class="flex gap-x-2"
                            >
                                <div class="w-full">
                                    <input
                                        type="text"
                                        v-model="email.attachment[index]"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                        placeholder="Lampiran URL..."
                                        required
                                    />
                                    <div
                                        v-if="
                                            detailErrors[
                                                'mail.' +
                                                    emailIndex +
                                                    '.attachment.' +
                                                    index
                                            ]
                                        "
                                    >
                                        <p
                                            v-for="attachmentErr in detailErrors[
                                                'mail.' +
                                                    emailIndex +
                                                    '.attachment.' +
                                                    index
                                            ]"
                                            class="text-sm text-red-700"
                                        >
                                            {{
                                                attachmentErr.replace(
                                                    `mail.${emailIndex}.`,
                                                    ""
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    @click="removeAttachment(index)"
                                    type="button"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2.5 focus:outline-none"
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
                            </div>
                            <button
                                type="button"
                                @click="addAttachment"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Tambah Lampiran
                            </button>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label
                            for="message"
                            class="block mb-2 text-sm font-medium text-gray-900"
                        >
                            Isi</label
                        >
                        <textarea
                            id="message"
                            rows="4"
                            v-model="email.content"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tulis isi yang akan di kirim..."
                        ></textarea>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div
                    class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b md:p-5 rtl:space-x-reverse"
                >
                    <!-- Tombol Batal -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="formModal = false"
                        v-if="formModal"
                        class="py-2.5 px-5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors duration-300"
                    >
                        Batal
                    </button>
                    <!-- Tombol Tambah Baru -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        v-if="formModal && emailIndex === null"
                        @click="addEmail(email)"
                        class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        Tambah
                    </button>
                    <!-- Tombol Simpan Perubahan Edit -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="saveEdit"
                        v-if="formModal && emailIndex !== null"
                        class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        Simpan
                    </button>
                    <!-- Tombol Kirim Email -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="validateBeforeSubmit() && handleSubmit()"
                        v-if="!formModal"
                        class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    >
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
