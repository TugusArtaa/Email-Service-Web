<template>
    <!-- Membuat div dengan lebar penuh dan tinggi yang ditentukan -->
    <div class="w-full h-64 lg:h-80">
        <!-- Menggunakan komponen Bar dari vue-chartjs dengan data dan opsi yang telah disiapkan -->
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
// Import ref dan computed dari Vue untuk reaktivitas data
import { ref, computed } from "vue";
// Import komponen Bar dari vue-chartjs untuk membuat chart batang
import { Bar } from "vue-chartjs";
// Import modul-modul yang diperlukan dari Chart.js
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

// Mendaftarkan modul Chart.js yang akan digunakan pada chart
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

// Mendefinisikan props yang diterima oleh komponen, yaitu data array log email
const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// Membuat data chart secara reaktif berdasarkan props yang diterima
const chartData = computed(() => {
    return {
        labels: ["Terkirim", "Gagal"],
        datasets: [
            {
                label: "",
                backgroundColor: ["rgba(52, 211, 153, 0.8)", "rgb(255, 0, 47)"],
                borderColor: ["rgb(16, 185, 129)", "rgb(225, 29, 72)"],
                borderWidth: 1,
                borderRadius: 6,
                data: [props.data.sent, props.data.failed],
            },
        ],
    };
});

// Opsi konfigurasi chart, seperti tampilan legend, tooltip, dan skala
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
            position: "top",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: {
                    size: 12,
                },
            },
        },
        title: {
            display: false,
        },
        tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.9)",
            titleColor: "#334155",
            bodyColor: "#334155",
            borderColor: "#e2e8f0",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                borderDash: [2, 4],
                color: "rgba(203, 213, 225, 0.4)",
            },
            ticks: {
                precision: 0,
            },
        },
    },
};
</script>
