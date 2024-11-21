<script setup>
import Layout from "./Layout.vue";
import { ref, watch } from "vue";
import Table from "../components/TableIntegrasi.vue";
import Pagination from "../components/TablePagination.vue";
import Search from "../components/Search.vue";
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { useFetch, onClickOutside, useStorage } from '@vueuse/core'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

// add data
const mail = ref({
    to: '',
    subject: '',
    content: '',
    priority: 0,
    attachment: [],
});
const secret = ref('');

const thead = ref(["", "Application name", "Recipient Email", "Subject", "Status", "Date", "Action"]);
const logs = ref([]);
const fetchData = ref(false);
const currentPage = ref(1);

const search = ref('');
const orderBy = useStorage('integrasi-orderBy', 'desc');
const date = ref('');
const baseUrl = import.meta.env.VITE_APP_URL;

// dropdown order
const orderDropdown = ref(false);
const modalOrder = ref(null);
const btnOrder = ref(null);
onClickOutside(modalOrder, event => {
    if (event.target !== btnOrder.value) {
        orderDropdown.value = false;
    }
});

// dropdown delete date
const dateDropdown = ref(false);
const modalDate = ref(null);
const btnDate = ref(null);
onClickOutside(modalDate, event => {
    if (event.target !== btnDate.value) {
        dateDropdown.value = false;
    }
});

// modal delete date
const showDeleteDate = ref(false);
const modalDeleteDate = ref(null);
onClickOutside(modalDeleteDate, event => {
    showDeleteDate.value = false;
});

// modal add email
const showAddModal = ref(false);
const modalAdd = ref(null);
onClickOutside(modalAdd, event => {
    showAddModal.value = false;
});

// ketika klik pagination ganti data
const handlePageChange = (page) => {
    if (page === logs.value.current_page) {
        return;
    }
    refreshData(page);
};

// ketika search data
const handleSearch = (query) => {
    search.value = query;
    refreshData();
};

// proses delete by id
const pageInertia = usePage()

const form = useForm({
    ids: [],
    _token: pageInertia.props.csrf_token,
})

// ketika checkbox data
const handleCheckbox = (checked) => {
    form.ids = checked;
};

function refreshData(page = 1) {
    // fetch data
    currentPage.value = page;
    const { data, isFetching } = useFetch(`${baseUrl}/api/email-logs?orderBy=${orderBy.value}&search=${search.value}&page=${page}`, { refetch: true }).get().json()
    // Akses data langsung
    watch(data, (newData) => {
        if (newData) {
            logs.value = [];
            logs.value = newData.data.emailLogs;
        }
    });
    // watch isFetching changes
    watch(isFetching, (newIsFetching) => {
        fetchData.value = newIsFetching;
    });
}

function refreshDataWithoutFetchLoad(){
    // fetch data
    const { data } = useFetch(`${baseUrl}/api/email-logs?orderBy=${orderBy.value}&search=${search.value}&page=${currentPage.value}`, { refetch: true }).get().json()
    // Akses data langsung
    watch(data, (newData) => {
        if (newData) {
            logs.value = [];
            logs.value = newData.data.emailLogs;
        }
    });
}

// Refresh data every 5 seconds without fetching
setInterval(() => {
    refreshDataWithoutFetchLoad();
}, 2000);

function handleRefresh() {
    refreshData();
}

function handleDeleteCheckbox() {
    form.delete(`${baseUrl}/integrasi/delete`)
    // kosongkan ids
    form.ids = [];
    refreshData();
}

const formDate = useForm({
    start_date: '',
    end_date: '',
    _token: pageInertia.props.csrf_token,
})

function handleDeleteDate() {
    const formattedDate = date.value.map(d => {
        const dt = new Date(d);
        const year = dt.getFullYear();
        const month = String(dt.getMonth() + 1).padStart(2, '0');
        const day = String(dt.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    });
    formDate.start_date = formattedDate[0];
    formDate.end_date = formattedDate[1];
    console.log(formDate.start_date, formDate.end_date);
    formDate.delete(`${baseUrl}/integrasi/delete-date`);
    // kosongkan date
    date.value = '';
    showDeleteDate.value = false;
    refreshData();
}

const formAdd = useForm({
    secret: '',
    mail: [{
        to: '',
        subject: '',
        content: '',
        priority: 0,
        attachment: [],
    }],
    _token: pageInertia.props.csrf_token,
})

function handleAddEmail() {
    // console.log(formAdd.mail.to);
    formAdd.post(`${baseUrl}/integrasi/send`,
        {
            onSuccess: () => {
                Inertia.reload({ preserveState: true });
            }
        }
    );
    formAdd.reset();
    formAdd.secret = '';
    showAddModal.value = false;
}

// fetch data pertama kali
refreshData();
</script>

<template>

    <Head>
        <title>Integrasi</title>
    </Head>
    <Layout>
        <div class="relative w-full px-3 py-5 bg-white shadow-md sm:rounded-lg">
            <!-- alert success -->
            <div v-if="$page.props.flash.success"
                class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    {{ $page.props.flash.success }}
                </div>
            </div>
            <!-- alert error -->
            <div v-if="$page.props.flash.error"
                class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    {{ $page.props.flash.error }}
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
                                    New Email
                                </button>
                                <div class="flex items-center w-full space-x-3 md:w-auto">
                                    <button @click="handleDeleteCheckbox" id="actionsDropdownButton"
                                        data-dropdown-toggle="actionsDropdown"
                                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        type="button">
                                        <svg class="mr-1.5 w-4 h-4" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                        Delete by Checkbox
                                    </button>
                                    <div class="relative">
                                        <button @click="dateDropdown = !dateDropdown" ref="btnDate"
                                            d="actionsDropdownButton" data-dropdown-toggle="actionsDropdown"
                                            class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                            type="button">
                                            <svg class="mr-1.5 w-4 h-4" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                                viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                            </svg>
                                            Delete by Date
                                        </button>
                                        <div v-show="dateDropdown" ref="modalDate"
                                            class="absolute flex flex-col items-center p-2 bg-white shadow -left-1/3 top-12 gap-y-2">
                                            <VueDatePicker v-model="date" range inline auto-apply
                                                :enable-time-picker="false" />
                                            <div class="flex gap-2">
                                                <button @click="showDeleteDate = true" type="button"
                                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-blue-800">Delete</button>
                                                <button @click="date = ''" type="button"
                                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
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
                                                <button href="#" @click="orderBy = 'desc'; refreshData()"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'desc' }"
                                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    Date (new-old)</button>
                                                <button href="#" @click="orderBy = 'asc'; refreshData();"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'asc' }"
                                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                                    Date (old-new)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- table body -->
            <Table :thead="thead" :fetch="fetchData" :logs="logs" @checkbox="handleCheckbox" @refresh="handleRefresh" />
            <!-- table footer -->
            <Pagination @page-change="handlePageChange" :current="logs.current_page" :last="logs.last_page"
                :from="logs.from" :to="logs.to" :total="logs.total" />
        </div>
        <!-- modal delete date -->
        <div v-show="showDeleteDate"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 bg-opacity-40 md:inset-0 backdrop-blur-[2px]">
            <div class="relative w-full max-w-2xl max-h-full p-4">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" ref="modalDeleteDate">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Delete logs by date
                        </h3>
                        <button @click="showDeleteDate = false" type="button"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="default-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 space-y-4md:p-5">
                        <div class="">
                            <p class="text-lg font-medium">Are you sure you want to delete these logs</p>
                            <p>From: {{ date && date[0] ? new Date(date[0]).toLocaleDateString('en-US', {
                                year:
                                    'numeric', month: 'long', day: 'numeric'
                            }) : 'N/A' }}</p>
                            <p>To: {{ date && date[1] ? new Date(date[1]).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long', day: 'numeric'
                            }) : 'N/A' }}</p>
                            <button @click="handleDeleteDate" data-modal-hide="default-modal" type="button"
                                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-blue-800 mt-5">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal add email -->
        <div v-if="showAddModal" id="crud-modal"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-gray-700 backdrop-blur-[2px] bg-opacity-40 md:inset-0">
            <div class="relative w-full max-w-md max-h-full p-4">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" ref="detailModal">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Add Email
                        </h3>
                        <button @click="showAddModal = false" type="button"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Secret
                                </label>
                                <input type="text" name="name" id="name" v-model="formAdd.secret"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name">
                            </div>
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Subject
                                </label>
                                <input type="text" name="name" id="name" v-model="formAdd.mail[0].subject"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name">
                            </div>
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    To
                                </label>
                                <input type="text" name="name" id="name" v-model="formAdd.mail[0].to"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name">
                            </div>
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Priority
                                </label>
                                <input type="text" name="name" id="name" v-model="formAdd.mail[0].priority"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name">
                            </div>
                            <div class="col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Attachment
                                </label>
                                <input type="text" name="name" id="name" v-model="formAdd.mail[0].attachment[0]"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Type product name">
                            </div>
                            <div class="col-span-2">
                                <label for="description"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Content</label>
                                <textarea id="description" rows="4" v-model="formAdd.mail[0].content"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Write product description here"></textarea>
                            </div>
                            <button @click="handleAddEmail" type="button" class="text-white w-fit bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>