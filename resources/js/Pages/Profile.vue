<template>
    <Head><title>Profile</title></Head>
    <Layout>
        <!-- Notification toast -->
        <NotificationToast
            :notification="notification"
            @close="notification.show = false"
        />

        <!-- Profil Header -->
        <div class="flex items-center space-x-4">
            <div class="relative">
                <img
                    :src="'/PNB1.png'"
                    alt="Profile"
                    class="w-16 h-16 rounded-full border-2 border-green-100"
                />
                <span
                    class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 border-2 border-white rounded-full"
                ></span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ user.name }}
                </h2>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
        </div>
        <br />

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-6">
                <button
                    @click="activeTab.current = 'profile'"
                    class="py-3 px-1 border-b-2 font-medium text-sm"
                    :class="
                        activeTab.current === 'profile'
                            ? 'border-green-500 text-green-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'
                    "
                >
                    <span class="flex items-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                        Informasi Pribadi
                    </span>
                </button>
                <button
                    @click="activeTab.current = 'security'"
                    class="py-3 px-1 border-b-2 font-medium text-sm"
                    :class="
                        activeTab.current === 'security'
                            ? 'border-green-500 text-green-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'
                    "
                >
                    <span class="flex items-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                        Keamanan
                    </span>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- Tab Informasi Pribadi -->
            <div v-if="activeTab.current === 'profile'">
                <form @submit.prevent="updateProfile" class="space-y-4">
                    <h3
                        class="text-lg font-medium text-gray-900 mb-4 flex items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-green-500 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Edit Informasi Pribadi
                    </h3>
                    <div class="mb-4">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Nama Lengkap</label
                        >
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                @focus="clearProfileError('name')"
                                class="pl-10 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2"
                                placeholder="Masukkan nama lengkap Anda"
                            />
                        </div>
                        <div v-if="form.errors.name" class="relative mt-2">
                            <div
                                class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                        ></path>
                                        <line
                                            x1="12"
                                            y1="9"
                                            x2="12"
                                            y2="13"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="17"
                                            x2="12.01"
                                            y2="17"
                                        ></line>
                                    </svg>
                                    <span>
                                        <span
                                            v-for="err in [].concat(
                                                form.errors.name
                                            )"
                                            :key="err"
                                        >
                                            {{ err }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Alamat Email</label
                        >
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="text"
                                @focus="clearProfileError('email')"
                                class="pl-10 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2"
                                placeholder="Masukkan email Anda"
                            />
                        </div>
                        <div v-if="form.errors.email" class="relative mt-2">
                            <div
                                class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                        ></path>
                                        <line
                                            x1="12"
                                            y1="9"
                                            x2="12"
                                            y2="13"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="17"
                                            x2="12.01"
                                            y2="17"
                                        ></line>
                                    </svg>
                                    <span>
                                        <span
                                            v-for="err in [].concat(
                                                form.errors.email
                                            )"
                                            :key="err"
                                        >
                                            {{ err }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <svg
                                v-if="form.processing"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                class="-ml-1 mr-2 h-4 w-4 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                />
                            </svg>
                            Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Keamanan (Ganti Password) -->
            <div v-if="activeTab.current === 'security'">
                <form @submit.prevent="updatePassword" class="space-y-4">
                    <h3
                        class="text-lg font-medium text-gray-900 mb-4 flex items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-green-500 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                            />
                        </svg>
                        Ganti Password
                    </h3>
                    <!-- Password sekarang -->
                    <div class="mb-4">
                        <label
                            for="current_password"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Password Saat Ini</label
                        >
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </div>
                            <input
                                :type="
                                    showCurrentPassword ? 'text' : 'password'
                                "
                                id="current_password"
                                v-model="passwordForm.current_password"
                                @focus="clearPasswordError('current_password')"
                                class="pl-10 pr-10 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2"
                                placeholder="Masukkan password saat ini"
                            />
                            <button
                                type="button"
                                @click="
                                    showCurrentPassword = !showCurrentPassword
                                "
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500"
                                tabindex="-1"
                            >
                                <svg
                                    v-if="showCurrentPassword"
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
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div
                            v-if="passwordForm.errors.current_password"
                            class="relative mt-2"
                        >
                            <div
                                class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                        ></path>
                                        <line
                                            x1="12"
                                            y1="9"
                                            x2="12"
                                            y2="13"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="17"
                                            x2="12.01"
                                            y2="17"
                                        ></line>
                                    </svg>
                                    <span>
                                        <span
                                            v-for="err in [].concat(
                                                passwordForm.errors
                                                    .current_password
                                            )"
                                            :key="err"
                                        >
                                            {{ err }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <!-- Password baru -->
                    <div class="mb-4">
                        <label
                            for="new_password"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Password</label
                        >
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                                    />
                                </svg>
                            </div>
                            <input
                                :type="showNewPassword ? 'text' : 'password'"
                                id="new_password"
                                v-model="passwordForm.new_password"
                                @focus="clearPasswordError('new_password')"
                                class="pl-10 pr-10 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2"
                                placeholder="Masukkan password baru"
                            />
                            <button
                                type="button"
                                @click="showNewPassword = !showNewPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500"
                                tabindex="-1"
                            >
                                <svg
                                    v-if="showNewPassword"
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
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div
                            v-if="passwordForm.errors.new_password"
                            class="relative mt-2"
                        >
                            <div
                                class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                        ></path>
                                        <line
                                            x1="12"
                                            y1="9"
                                            x2="12"
                                            y2="13"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="17"
                                            x2="12.01"
                                            y2="17"
                                        ></line>
                                    </svg>
                                    <span>
                                        <span
                                            v-for="err in [].concat(
                                                passwordForm.errors.new_password
                                            )"
                                            :key="err"
                                        >
                                            {{ err }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <!-- Konfirmasi Password yang baru -->
                    <div class="mb-4">
                        <label
                            for="new_password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Konfirmasi Password Baru</label
                        >
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>
                            <input
                                :type="
                                    showConfirmPassword ? 'text' : 'password'
                                "
                                id="new_password_confirmation"
                                v-model="passwordForm.new_password_confirmation"
                                @focus="
                                    clearPasswordError(
                                        'new_password_confirmation'
                                    )
                                "
                                class="pl-10 pr-10 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg py-2"
                                placeholder="Konfirmasi password baru"
                            />
                            <button
                                type="button"
                                @click="
                                    showConfirmPassword = !showConfirmPassword
                                "
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-500"
                                tabindex="-1"
                            >
                                <svg
                                    v-if="showConfirmPassword"
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
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div
                            v-if="passwordForm.errors.new_password_confirmation"
                            class="relative mt-2"
                        >
                            <div
                                class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                            >
                                <div class="flex items-center gap-2">
                                    <svg
                                        class="w-4 h-4 flex-shrink-0"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path
                                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                        ></path>
                                        <line
                                            x1="12"
                                            y1="9"
                                            x2="12"
                                            y2="13"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="17"
                                            x2="12.01"
                                            y2="17"
                                        ></line>
                                    </svg>
                                    <span>
                                        <span
                                            v-for="err in [].concat(
                                                passwordForm.errors
                                                    .new_password_confirmation
                                            )"
                                            :key="err"
                                        >
                                            {{ err }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                            :disabled="passwordForm.processing"
                        >
                            <svg
                                v-if="passwordForm.processing"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                class="-ml-1 mr-2 h-4 w-4 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                />
                            </svg>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
// Mengimpor fungsi dan komponen yang diperlukan
import { Head } from "@inertiajs/vue3";
import { reactive, onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Layout from "./Layout.vue";
import NotificationToast from "../components/NotificationToast.vue";
import * as ProfileAPI from "../api/ProfileAPI";

// Mendapatkan data pengguna dari properti halaman
const page = usePage();
const user = page.props.auth.user;

// State untuk notifikasi
const notification = reactive({
    show: false,
    type: "",
    message: "",
    timeout: null,
});

// Forms for profile and password updates
const form = useForm({
    name: user.name,
    email: user.email,
});

const passwordForm = useForm({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

// UpdateProfile
const updateProfile = async () => {
    form.clearErrors();
    try {
        const response = await ProfileAPI.updateProfile({
            name: form.name,
            email: form.email,
        });
        // Update user data pada halaman setelah berhasil update profil
        if (response?.data?.user) {
            user.name = response.data.user.name;
            user.email = response.data.user.email;
        }
        notification.show = true;
        notification.type = "success";
        notification.message = "Berhasil!";
        notification.description = "Profil berhasil diperbarui.";
    } catch (error) {
        // Tangani error validasi dari backend
        if (error.response?.status === 422 && error.response?.data?.errors) {
            form.setError(error.response.data.errors);
        } else {
            notification.show = true;
            notification.type = "danger";
            notification.message = "Gagal!";
            notification.description =
                error.response?.data?.message || "Terjadi kesalahan.";
        }
    }
};

// UpdatePassword
const updatePassword = async () => {
    passwordForm.clearErrors();
    try {
        await ProfileAPI.updatePassword({
            current_password: passwordForm.current_password,
            new_password: passwordForm.new_password,
            new_password_confirmation: passwordForm.new_password_confirmation,
        });
        passwordForm.reset();
        notification.show = true;
        notification.type = "success";
        notification.message = "Berhasil!";
        notification.description = "Password berhasil diperbarui.";
    } catch (error) {
        // Tangani error validasi dari backend
        if (error.response?.status === 422 && error.response?.data?.errors) {
            passwordForm.setError(error.response.data.errors);
        } else {
            notification.show = true;
            notification.type = "danger";
            notification.message = "Gagal!";
            notification.description =
                error.response?.data?.message || "Terjadi kesalahan.";
        }
    }
};

// State untuk mengelola tab aktif (e.g., "profile" atau "security")
const activeTab = reactive({ current: "profile" });

// Bersihkan data-page
onMounted(() => {
    const appDiv = document.getElementById("app");
    if (appDiv) {
        appDiv.removeAttribute("data-page");
    }
});

function clearProfileError(field) {
    if (form.errors[field]) {
        form.errors[field] = "";
    }
}

function clearPasswordError(field) {
    if (passwordForm.errors[field]) {
        passwordForm.errors[field] = "";
    }
}

// State untuk show/hide password
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
</script>
