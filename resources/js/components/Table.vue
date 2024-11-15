<script setup>
import { ref, watch } from 'vue';
import { defineProps, computed } from 'vue';
import TablePagination from './TablePagination.vue';

const props = defineProps({
    data: Object,
    thead: Array,
    title: String,
    isFetching: Boolean,
});

const emit = defineEmits(['checkbox']);
const checked = ref([]);

const handleCheckboxChange = (itemId) => {
    if (checked.value.includes(itemId)) {
        checked.value = checked.value.filter(id => id !== itemId);
    } else {
        checked.value.push(itemId);
    }
};

watch(checked, (newValue) => {
    emit('checkbox', newValue);
});

const incrementedNumbers = computed(() => {
    if (!props.data || !props.data.data) return [];
    return props.data.data.map((_, index) => props.data.from + index);
});
</script>


<template>
        <table class="w-full text-sm text-left text-gray-500 border dark:text-gray-400 rounded-2xl">
            <thead class="text-xs text-gray-700 uppercase border-b bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3" v-for="item in thead" :key="item">
                        {{ item }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="data.data.length === 0 && isFetching == false">
                    <td colspan="5" class="px-6 py-4 text-lg font-bold text-center">Tidak ada data yang ditemukan!</td>
                </tr>
                <tr v-if="isFetching">
                    <td colspan="5" class="px-6 py-4 text-lg font-bold text-center">
                        <div role="status">
                            <svg aria-hidden="true" class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </td>
                </tr>
                <tr v-for="(item, index) in data.data" :key="item.id" 
                    :class="{'bg-red-50 hover:bg-red-100': checked.includes(item.id)}"
                    class="dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <!-- input checkbox -->
                    <td class="px-6 py-4">
                        <input
                        type="checkbox"
                        :value="item.id"
                        v-model="checked"
                        class="w-4 h-4 mt-1.5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    </td>
                    <td class="px-6 py-4">
                        {{ incrementedNumbers[index] }}
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ item.name }}
                    </th>
                    <td class="px-6 py-4">
                        {{ item.secret_key }}
                    </td>
                    <td class="px-6 py-4">
                        {{ new Date(item.created_at).toLocaleString() }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
</template>