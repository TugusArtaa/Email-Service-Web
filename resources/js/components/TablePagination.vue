<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    current: Number,
    last: Number,
    from: Number,
    to: Number,
    total: Number,
});

const pages = computed(() => {
    const range = [];
    let from, end;

    if (props.current <= 3) {
        from = 1;
        end = Math.min(5, props.last);
    } else if (props.current >= props.last - 2) {
        from = Math.max(1, props.last - 4);
        end = props.last;
    } else {
        from = props.current - 2;
        end = props.current + 2;
    }

    for (let i = from; i <= end; i++) {
        range.push(i);
    }

    return range;
});

const prev = computed(() => {
    return props.current > 1 ? props.current - 1 : 1;
});

const next = computed(() => {
    return props.current < props.last ? props.current + 1 : props.last;
});

const isCurrent = (page) => {
    return page === props.current;
};

const emit = defineEmits(['page-change']);
</script>

<template>
    <div class="flex items-center justify-between mt-5">
        <span class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ from ?? 0 }}</span> to <span
                class="font-semibold text-gray-900 dark:text-white">{{ to ?? 0 }}</span> of <span
                class="font-semibold text-gray-900 dark:text-white">{{ total }}</span> Entries
        </span>
        <nav aria-label="Page navigation example">
            <ul class="flex items-center h-8 -space-x-px text-sm">
                <li>
                    <button @click="emit('page-change', prev)" class="flex items-center justify-center h-8 px-3 leading-tight text-gray-500 bg-white border border-gray-300 ms-0 border-e-0 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <span class="sr-only">Previous</span>
                        <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                        </svg>
                    </button>
                </li>
                <li v-for="page in pages" :key="page">
                    <button @click="emit('page-change', page)" :class="isCurrent(page) ? 'bg-green-600 text-white hover:bg-green-700 hover:text-white' : 'bg-white'" class="flex items-center justify-center h-8 px-3 leading-tight text-gray-500 border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        {{ page }}
                    </button>
                </li>
                <li>
                    <button @click="emit('page-change', next)" class="flex items-center justify-center h-8 px-3 leading-tight text-gray-500 bg-white border border-gray-300 border-s-0 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <span class="sr-only">Next</span>
                        <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>