<script setup>
import Layout from "./Layout.vue";
import { ref, watch } from "vue";
import Table from "../components/TableIntegrasi.vue";
import Pagination from "../components/TablePagination.vue";
import Search from "../components/Search.vue";
import { useFetch, onClickOutside, useStorage } from '@vueuse/core'

const thead = ref(["Application name", "Recipient Email", "Subject", "Status", "Date", "Action"]);
const logs = ref([]);
const fetchData = ref(false);

const search = ref('');
const orderBy = useStorage('integrasi-orderBy', 'desc');
const baseUrl = import.meta.env.VITE_APP_URL;

const orderDropdown = ref(false);
const modalOrder = ref(null);
const btnOrder = ref(null);
onClickOutside(modalOrder, event => {
    if (event.target !== btnOrder.value) {
        orderDropdown.value = false;
    }
});

// ketika klik pagination ganti data
const handlePageChange = (page) => {
    if (page === logs.value.current_page) {
        return;
    }
    refreshData(page);
};

function refreshData(page = 1) {
    // fetch data
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

// ketika search data
const handleSearch = (query) => {
    search.value = query;
    refreshData();
};

// fetch data pertama kali
refreshData();
</script>

<template>
    <Layout>
        <div class="relative w-full px-3 py-5 overflow-x-auto bg-white shadow-md sm:rounded-lg">
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
                                <button type="button"
                                    class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                    <svg class="h-3.5 w-3.5 mr-2 text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 12h14m-7 7V5" />
                                    </svg>
                                    New Email
                                </button>
                                <div class="flex items-center w-full space-x-3 md:w-auto">
                                    <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown"
                                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-700 rounded-lg md:w-auto focus:outline-none hover:bg-red-800 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        type="button">
                                        <svg class="mr-1.5 w-4 h-4" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                        Delete by Date
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
                                                <button href="#" @click="orderBy = 'desc'; refreshData()"
                                                    :class="{ 'text-green-700 bg-green-200 hover:bg-green-300': orderBy === 'desc'}"
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
            <Table :thead="thead" :fetch="fetchData" :logs="logs" />
            <!-- table footer -->
            <Pagination @page-change="handlePageChange" :current="logs.current_page" :last="logs.last_page"
                :from="logs.from" :to="logs.to" :total="logs.total" />
        </div>
    </Layout>
</template>