<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Head>
            <title>Halaman Login - S-MEBB</title>
        </Head>

        <!-- Judul Header -->
        <div class="fixed top-0 left-0 w-full p-4 z-50">
            <div class="flex justify-center">
                <div
                    class="flex items-center gap-3 bg-white/80 backdrop-blur-md p-2 px-4 rounded-xl shadow-lg w-auto"
                >
                    <img
                        class="w-9 h-9"
                        :src="'/Logo_SMEBB.png'"
                        alt="Logo SMEBB"
                    />
                    <div class="font-medium text-gray-800 text-sm">
                        <span>S-MEBB DASHBOARD ADMIN</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex min-h-screen">
            <!-- Left: Login Form -->
            <div
                class="w-full md:w-1/2 flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-24 py-20"
            >
                <div class="max-w-md mx-auto w-full">
                    <div class="text-center mb-10">
                        <h1 class="text-3xl font-bold text-green-600">
                            Hi, Selamat Datang
                        </h1>
                        <p class="mt-3 text-sm text-gray-500">
                            Silakan masukkan email dan password untuk
                            melanjutkan
                        </p>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <div
                            v-if="form.errors.invalid"
                            class="bg-red-100 border border-red-200 rounded-lg p-4 flex items-start gap-3"
                        >
                            <svg
                                class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span class="text-sm text-red-700">{{
                                form.errors.invalid
                            }}</span>
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Email
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-5 h-5 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                        ></path>
                                        <polyline
                                            points="22,6 12,13 2,6"
                                        ></polyline>
                                    </svg>
                                </div>
                                <input
                                    type="email"
                                    id="email"
                                    v-model="form.email"
                                    class="pl-10 w-full rounded-lg border border-gray-300 bg-white py-3 px-4 text-sm text-gray-800 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                    placeholder="nama@contoh.com"
                                    required
                                />
                            </div>
                            <div
                                v-if="form.errors.email"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Password
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-5 h-5 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect
                                            x="3"
                                            y="11"
                                            width="18"
                                            height="11"
                                            rx="2"
                                            ry="2"
                                        ></rect>
                                        <path
                                            d="M7 11V7a5 5 0 0 1 10 0v4"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    v-model="form.password"
                                    class="pl-10 w-full rounded-lg border border-gray-300 bg-white py-3 px-4 text-sm text-gray-800 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
                                    placeholder="••••••••"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500"
                                >
                                    <svg
                                        v-if="showPassword"
                                        class="w-5 h-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                        ></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-5 h-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                                        ></path>
                                        <line
                                            x1="1"
                                            y1="1"
                                            x2="23"
                                            y2="23"
                                        ></line>
                                    </svg>
                                </button>
                            </div>
                            <div
                                v-if="form.errors.password"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="sr-only peer"
                                />
                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"
                                ></div>
                                <span
                                    class="text-sm font-medium text-gray-900 ms-3"
                                    >Ingat Saya</span
                                >
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 text-white font-medium rounded-lg text-sm px-5 py-3 transition-all duration-200 shadow-lg shadow-green-500/20 hover:shadow-green-500/30 focus:outline-none"
                        >
                            <span
                                class="flex items-center justify-center gap-2"
                            >
                                <span>Login</span>
                                <svg
                                    class="w-5 h-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"
                                    ></path>
                                    <polyline
                                        points="10 17 15 12 10 7"
                                    ></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Brand Display -->
            <div class="hidden md:flex md:w-1/2 relative overflow-hidden">
                <!-- Wrapper -->
                <div
                    class="absolute inset-0 rounded-tl-xl rounded-bl-xl overflow-hidden"
                >
                    <div
                        class="absolute inset-0 bg-[url('/img/login.png')] bg-cover bg-center"
                    ></div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-green-900/60 to-black/60"
                    ></div>
                </div>

                <!-- Konten -->
                <div
                    class="relative z-10 flex items-center justify-center p-12 w-full h-full"
                >
                    <div class="flex flex-col items-center gap-6 text-center">
                        <div class="flex gap-4 items-center">
                            <img
                                class="object-contain w-24 h-24"
                                :src="'/bpd.png'"
                                alt="BPD Logo"
                            />
                            <div class="text-white">
                                <h1 class="text-5xl font-bold tracking-tight">
                                    S-MEBB
                                </h1>
                                <h2 class="text-lg italic mt-1 text-gray-200">
                                    Service Mail Engine Bank BPD Bali
                                </h2>
                            </div>
                        </div>
                        <div class="max-w-md mt-6">
                            <p class="text-gray-200 text-sm">
                                Selamat datang di sistem manajemen mail engine
                                BPD Bali. Silakan login untuk melanjutkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Mengimpor komponen dan fungsi dari Inertia.js dan Vue
import { Head, useForm } from "@inertiajs/vue3";
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

// Mendapatkan data halaman saat ini menggunakan `usePage`
const page = usePage();

// Variabel reaktif untuk mengontrol visibilitas password
const showPassword = ref(false);

// Membuat form menggunakan `useForm` dengan data awal
const form = useForm({
    email: "",
    password: "",
    remember: false,
    _token: page.props.csrf_token,
});

// Fungsi untuk mengirimkan form ke endpoint `/login`
function submit() {
    form.post("/login", {
        onSuccess: () => {
            if (form.remember) {
                // Simpan email dan password ke localStorage
                localStorage.setItem("remember_email", form.email);
                localStorage.setItem("remember_password", form.password);
            } else {
                // Hapus email dan password dari localStorage
                localStorage.removeItem("remember_email");
                localStorage.removeItem("remember_password");
            }
        },
    });
}

// Saat halaman dimuat, isi form dengan data dari localStorage jika ada
if (localStorage.getItem("remember_email")) {
    form.email = localStorage.getItem("remember_email");
    form.password = localStorage.getItem("remember_password");
    form.remember = true;
}
</script>
