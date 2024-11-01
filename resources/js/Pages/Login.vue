<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

const form = useForm({
    email: '',
    password: '',
    _token: page.props.csrf_token,
})

function submit() {
    form.post('/login')
}
</script>

<template>
    <div class="grid h-screen grid-cols-2">
        <!-- head -->

        <Head>
            <title>Login Page</title>
        </Head>
        <!-- navbar -->
        <div
            class="fixed flex items-center p-2 -translate-x-1/2 bg-gray-100 bg-opacity-50 rounded-lg dark:bg-gray-950 dark:bg-opacity-50 backdrop-blur top-5 left-1/2">
            <div class="flex items-center gap-2">
                <img class="object-contain w-7" :src="'/bpd.png'" />
                <div class="flex items-center gap-5 text-black dark:text-white">
                    <h2 class="text-md">S-MEBB ADMIN DASHBOARD</h2>
                </div>
            </div>
        </div>
        <!-- form -->
        <div class="flex flex-col justify-center w-full p-32 bg-white dark:bg-gray-900">
            <h1 class="text-[#019966] font-bold text-3xl text-center dark:text-green-500">Welcome Back</h1>
            <h2 class="mt-1 text-sm text-center text-gray-600 dark:text-gray-400">Enter your username and password to
                sign in</h2>
            <form @submit.prevent="submit" class="flex flex-col gap-3 mt-8">
                <div v-if="form.errors.invalid"
                    class="bg-red-100 gap-1 flex items-center text-red-800 text-sm font-medium me-2 px-2.5 py-2 rounded dark:bg-red-900 dark:text-red-300">
                    <svg class="w-5 h-5 text-red-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ form.errors.invalid }}</span>
                </div>
                <div>
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" id="email" v-model="form.email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                        placeholder="name@example.com" required />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</div>
                </div>
                <div>
                    <label for="password"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <input type="password" id="password" v-model="form.password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                        placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;" required />
                    <div v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</div>
                </div>
                <label class="inline-flex items-center my-3 cursor-pointer w-fit">
                    <input type="checkbox" value="" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                    </div>
                    <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">Remember me</span>
                </label>
                <button type="submit"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    Login</button>
            </form>
        </div>
        <!-- sidebar -->
        <div class="w-full bg-[url('/img/login.png')] flex justify-center items-center">
            <div class="flex gap-2">
                <img class="object-contain w-24" :src="'/bpd.png'" />
                <div class="flex flex-col justify-center gap-1 text-white">
                    <h1 class="text-6xl font-bold">S-MEBB</h1>
                    <h2 class="italic">Service Mail Engine Bank BPD Bali</h2>
                </div>
            </div>
        </div>
    </div>
</template>