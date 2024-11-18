<script setup>
import { ref, watch, Suspense } from "vue";
import Layout from "./Layout.vue";
import Table from "../components/TableApp.vue";
import Search from "../components/Search.vue";
import TablePagination from "../components/TablePagination.vue";
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { useFetch, onClickOutside, useStorage } from '@vueuse/core'

// get data from API
const baseUrl = import.meta.env.VITE_APP_URL;
const orderBy = useStorage('orderBy', 'created_at')
const orderDirection = useStorage('orderDirection', 'desc')
const url = ref(`${baseUrl}/api/applications?orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`);
const applications = ref({ data: [] })

const search = ref('')
const checked = ref([])
const page = ref(1)

const orderDropdown = ref(false)
const btnOrder = ref(null)
const modalOrder = ref(null)
const deleteModal = ref(null)
const showDeleteModal = ref(false)

// add modal
const addModal = ref(null)
const showAddModal = ref(false)
const appName = ref('')
const appDescription = ref('')
const appPassword = ref('')

const { isFetching, error, data } = useFetch(url, { refetch: true }).get().json()

// Akses data langsung
watch(data, (newData) => {
    if (newData) {
        applications.value = newData.data.applications;
    }
});

const handlePageChange = (page) => {
    if (page === applications.value.current_page) {
        return;
    }
    url.value = `${baseUrl}/api/applications?page=${page}&search=${search.value}&orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`;
    applications.value = { data: [] };
};

const handleSearch = (query) => {
    if (query === search.value) {
        return;
    }
    url.value = `${baseUrl}/api/applications?search=${query}&page=1&orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`;
    search.value = query;
    applications.value = { data: [] };
};

function order(newOrderBy, newOrderDirection) {
    if (orderBy.value === newOrderBy && orderDirection.value === newOrderDirection) {
        return;
    }
    orderBy.value = newOrderBy;
    orderDirection.value = newOrderDirection;
    url.value = `${baseUrl}/api/applications?page=${page}&search=${search.value}&orderBy=${newOrderBy}&orderDirection=${newOrderDirection}`
    applications.value = { data: [] };
}

onClickOutside(modalOrder, event => {
    if (event.target !== btnOrder.value) {
        orderDropdown.value = false;
    }
});

onClickOutside(deleteModal, event => {
    showDeleteModal.value = false;
})

const thead = ref(["", "No", "Application name", "Secret Key", "Created At", "Aksi"]);

// proses delete
const pageInertia = usePage()

const form = useForm({
    ids: checked.value,
    _token: pageInertia.props.csrf_token,
})

function deleteApp() {
    form.delete('/application/delete')
    // reset data
    checked.value = [];
    showDeleteModal.value = false;
    refreshData();
}

function refreshData() {
    // fetch data
    const { data } = useFetch(`${baseUrl}/api/applications?orderBy=${orderBy.value}&orderDirection=${orderDirection.value}`, { refetch: true }).get().json()
    // Akses data langsung
    watch(data, (newData) => {
        if (newData) {
            applications.value = [];
            applications.value = newData.data.applications;
        }
    });
}

const handleCheckbox = (newChecked) => {
    checked.value = newChecked;
    form.ids = checked.value;
    console.log(form)
};

// proses add
const Addform = useForm({
    name: appName.value,
    description: appDescription.value,
    password: appPassword.value,
    _token: pageInertia.props.csrf_token,
})

watch(appName, (newAppName) => {
    Addform.name = newAppName;
})

watch(appDescription, (newAppDescription) => {
    Addform.description = newAppDescription;
})

watch(appPassword, (newAppPassword) => {
    Addform.password = newAppPassword;
})

function addApplication(){
    Addform.post('/application')
    // reset data
    appName.value = '';
    appDescription.value = '';
    appPassword.value = '';
    showAddModal.value = false;
    refreshData();
}
</script>

<template>

    <Head>
        <title>Manajemen aplikasi</title>
    </Head>
    <Layout>
        <div class="relative w-full px-3 py-5 overflow-x-auto bg-white shadow-md sm:rounded-lg">
            <h3 class="mb-3 text-2xl font-bold dark:text-white">Daftar Aplikasi</h3>
            <!-- alert -->
            <div v-if="$page.props.flash.message"
                class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    {{ $page.props.flash.message }}
                </div>
            </div>
            <!-- table header -->
            <section class="flex items-center mb-5">
                <div class="w-full mx-auto">
                    <!-- Start coding here -->
                    <div class="relative w-full sm:rounded-lg">
                        <div
                            class="flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
                            <div class="w-full md:w-1/2">
                                <!-- component search -->
                                <Search @search="handleSearch" />
                            </div>
                            <div
                                class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
                                <button type="button" @click="showAddModal = true"
                                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                    <svg class="h-3.5 w-3.5 mr-2 text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14m-7 7V5" />
                                    </svg>
                                    Add Application
                                </button>
                                <div class="flex items-center w-full space-x-3 md:w-auto">
                                    <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown"
                                        @click="checked.length > 0 && (showDeleteModal = true)"
                                        :class="{ 'cursor-not-allowed': checked.length === 0 }"
                                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        type="button">
                                        <svg class="mr-1.5 w-4 h-4" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                        Mass Delete
                                    </button>
                                    <div class="relative">
                                        <button ref="btnOrder" id="filterDropdownButton"
                                            data-dropdown-toggle="filterDropdown"
                                            @click="orderDropdown = !orderDropdown"
                                            class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-green-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                            type="button">
                                            <svg class="w-4 h-4 mr-2 text-gray-800" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                            </svg>
                                            Order By
                                            <svg :class="orderDropdown ? 'rotate-180' : ''" class="-mr-1 ml-1.5 w-5 h-5"
                                                fill="currentColor" viewbox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path clip-rule="evenodd" fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                            </svg>
                                        </button>
                                        <!-- dropdown -->
                                        <div id="actionsDropdown" v-show="orderDropdown" ref="modalOrder"
                                            class="absolute left-0 right-0 z-10 w-full overflow-hidden bg-white divide-y divide-gray-100 rounded shadow top-11 dark:bg-gray-700 dark:divide-gray-600">
                                            <div class="py-1">
                                                <button href="#" @click="order('created_at', 'desc')"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'created_at' && orderDirection === 'desc' }"
                                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    Date (new-old)</button>
                                                <button href="#" @click="order('created_at', 'asc')"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'created_at' && orderDirection === 'asc' }"
                                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    Date (old-new)</button>
                                                <button href="#" @click="order('name', 'asc')"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'name' && orderDirection === 'asc' }"
                                                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    App name (A-Z)</button>
                                                <button href="#" @click="order('name', 'desc')"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'name' && orderDirection === 'desc' }"
                                                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    App name (Z-A)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- table -->
            <Table :data="applications" :thead="thead" :title="'Daftar Aplikasi'" :isFetching="isFetching"
                @checkbox="handleCheckbox" @refresh="refreshData" />
            <!-- table footer -->
            <TablePagination :current="applications.current_page" :from="applications.from" :to="applications.to"
                :total="applications.total" :last="applications.last_page" @page-change="handlePageChange" />
        </div>
    </Layout>
    <!-- delete modal -->
    <div tabindex="-1" v-show="showDeleteModal"
        class="overflow-y-auto backdrop-blur-[2px] bg-gray-700 bg-opacity-40 flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0">
        <div class="relative w-full max-w-md max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" ref="deleteModal">
                <button @click="showDeleteModal = false" type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                        delete these application?</h3>
                    <button @click="deleteApp()" data-modal-hide="popup-modal" type="button"
                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, I'm sure
                    </button>
                    <button @click="showDeleteModal = false" data-modal-hide="popup-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                        cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- detail -->
    <div v-if="showAddModal" id="crud-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 backdrop-blur-[2px] bg-opacity-40 md:inset-0">
        <div class="relative w-full max-w-md max-h-full p-4">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" ref="addModal">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Application
                    </h3>
                    <button @click="showAddModal = false" type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Application
                                name</label>
                            <input type="text" name="name" id="name" v-model="appName"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Type product name" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="name" id="name" v-model="appPassword"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="•••••••" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Application
                                Description</label>
                            <textarea id="description" rows="4" v-model="appDescription"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Write product description here"></textarea>
                        </div>
                    </div>
                    <button @click="addApplication" data-modal-hide="popup-modal" type="submit"
                        class="text-white bg-yellow-600 hover:bg-yellow-800 mt-3 focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:focus:ring-yellow-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, I'm sure
                    </button>
                    <button @click="showAddModal = false" data-modal-hide="popup-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                        cancel</button>
                </form>
            </div>
        </div>
    </div>
</template>