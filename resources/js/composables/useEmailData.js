// Untuk mengelola dan memproses data email, fitur pencarian, penyortiran, dan paginasi.
import { ref, computed } from "vue";

// Fungsi `useEmailData` menerima data sebagai parameter, yang berisi daftar log email.
export function useEmailData(data) {
    const sortBy = ref("created_at");
    const sortOrder = ref("desc");
    const searchQuery = ref("");
    const currentPage = ref(1);
    const itemsPerPage = 8;

    // `emailStats`: Statistik email yang dihitung berdasarkan data, termasuk jumlah email terkirim, gagal, dan total.
    const emailStats = computed(() => ({
        sent: data.filter((log) => log.status === "success").length,
        failed: data.filter((log) => log.status === "failed").length,
        total: data.length,
    }));

    // `filteredData`: Data yang difilter berdasarkan `searchQuery`.
    const filteredData = computed(() => {
        const query = searchQuery.value.toLowerCase();
        return data.filter((item) => {
            const searchableFields = [
                item.application.name,
                JSON.parse(item.request).to,
                JSON.parse(item.request).subject,
                item.status === "success" ? "terkirim" : "gagal",
            ].map((field) => field.toLowerCase());

            return searchableFields.some((field) => field.includes(query));
        });
    });

    // `sortedData`: Data yang difilter dan disortir berdasarkan `sortBy` dan `sortOrder`.
    const sortedData = computed(() => {
        return [...filteredData.value].sort((a, b) => {
            const getValue = (item) => {
                switch (sortBy.value) {
                    case "application":
                        return item.application.name.toLowerCase();
                    case "created_at":
                        return new Date(item.created_at);
                    default:
                        return "";
                }
            };

            const aValue = getValue(a);
            const bValue = getValue(b);

            return sortOrder.value === "asc"
                ? aValue > bValue
                    ? 1
                    : -1
                : aValue < bValue
                ? 1
                : -1;
        });
    });

    // `paginatedData`: Data yang dipisahkan per halaman berdasarkan `currentPage` dan `itemsPerPage`.
    const paginatedData = computed(() => {
        const startIndex = (currentPage.value - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return sortedData.value.slice(startIndex, endIndex);
    });

    // `totalPages`: Menghitung jumlah total halaman berdasarkan jumlah data yang sudah disortir.
    const totalPages = computed(() =>
        Math.ceil(sortedData.value.length / itemsPerPage)
    );

    // `displayRange`: Menghitung rentang data yang ditampilkan di halaman saat ini.
    const displayRange = computed(() => ({
        start: (currentPage.value - 1) * itemsPerPage + 1,
        end: Math.min(
            currentPage.value * itemsPerPage,
            sortedData.value.length
        ),
        total: sortedData.value.length,
    }));

    // `toggleSort`: Fungsi untuk mengubah kolom atau urutan penyortiran.
    const toggleSort = (field) => {
        if (sortBy.value === field) {
            sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
        } else {
            sortBy.value = field;
            sortOrder.value = "asc";
        }
    };

    // `handleSearch`: Fungsi untuk mengatur halaman kembali ke halaman 1 saat pencarian dilakukan.
    const handleSearch = () => {
        currentPage.value = 1;
    };

    // Mengembalikan variabel dan fungsi untuk digunakan di komponen Vue.
    return {
        sortBy,
        sortOrder,
        searchQuery,
        currentPage,
        emailStats,
        filteredData,
        sortedData,
        paginatedData,
        totalPages,
        displayRange,
        toggleSort,
        handleSearch,
    };
}
