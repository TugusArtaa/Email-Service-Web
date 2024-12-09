<script setup>
import { reactive } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Layout from "./Layout.vue";

const page = usePage();
const user = page.props.auth.user;

// Notification state
const notification = reactive({
    show: false,
    type: "",
    message: "",
    timeout: null,
});

const showNotification = (type, message) => {
    if (notification.timeout) {
        clearTimeout(notification.timeout);
    }

    notification.show = true;
    notification.type = type;
    notification.message = message;

    notification.timeout = setTimeout(() => {
        notification.show = false;
    }, 3000);
};

// Form for profile update
const form = useForm({
    name: user.name,
    email: user.email,
});

// Form for password update
const passwordForm = useForm({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
});

// Update profile information
const updateProfile = () => {
    form.put("/profile", {
        preserveScroll: true,
        onSuccess: () => {
            showNotification("success", "Profile updated successfully!");
        },
    });
};

// Update password
const updatePassword = () => {
    passwordForm.put("/profile/password", {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showNotification("success", "Password updated successfully!");
        },
    });
};

// Logout function
const logout = () => {
    const logoutForm = useForm({});
    logoutForm.post("/logout");
};
</script>

<template>
    <Head>
        <title>Profil</title>
    </Head>
    <Layout>
        <div class="max-w-4xl mx-auto">
            <!-- Notification Alert -->
            <div v-if="notification.show" class="mb-6">
                <div
                    class="p-4 rounded-lg flex items-center gap-3"
                    :class="
                        notification.type === 'success'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                    "
                >
                    <!-- Success Icon -->
                    <svg
                        v-if="notification.type === 'success'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <!-- Error Icon -->
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <div>
                        <p class="font-medium">{{ notification.message }}</p>
                    </div>
                </div>
            </div>

            <!-- Profile Header -->
            <div class="p-6 bg-white rounded-lg shadow-sm mb-6">
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img
                            :src="'/bpd.png'"
                            alt="Profile"
                            class="w-24 h-24 rounded-full border-4 border-green-100"
                        />
                        <span
                            class="absolute bottom-1 right-1 w-5 h-5 bg-green-400 border-4 border-white rounded-full"
                        ></span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ user.name }}
                        </h2>
                        <p class="text-gray-500">{{ user.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Personal Information
                    </h3>
                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Full Name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Enter your full name"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Email Address</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Enter your email"
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>
                        <button
                            type="submit"
                            class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Change Password
                    </h3>
                    <form @submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Current Password</label
                            >
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Enter current password"
                            />
                            <p
                                v-if="passwordForm.errors.current_password"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >New Password</label
                            >
                            <input
                                v-model="passwordForm.new_password"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Enter new password"
                            />
                            <p
                                v-if="passwordForm.errors.new_password"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ passwordForm.errors.new_password }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Confirm New Password</label
                            >
                            <input
                                v-model="passwordForm.new_password_confirmation"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                placeholder="Confirm new password"
                            />
                        </div>
                        <button
                            type="submit"
                            class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                            :disabled="passwordForm.processing"
                        >
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="md:col-span-2 bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Account Actions
                    </h3>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-medium text-gray-800">
                                    Logout from Account
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Sign out from your current session
                                </p>
                            </div>
                            <form @submit.prevent="logout">
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
