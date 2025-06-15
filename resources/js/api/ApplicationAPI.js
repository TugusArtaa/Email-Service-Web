// Import axios untuk HTTP request
import axios from "axios";

// Base URL dari environment variable
const baseUrl = import.meta.env.VITE_APP_URL;

// Mengambil daftar aplikasi dengan filter pencarian, halaman, urutan, dan arah urutan
export async function fetchApplications({
    search = "",
    page = 1,
    orderBy = "created_at",
    orderDirection = "desc",
} = {}) {
    // Menyusun parameter query untuk request
    const params = new URLSearchParams();
    if (search) params.append("search", search);
    if (page) params.append("page", page);
    if (orderBy) params.append("orderBy", orderBy);
    if (orderDirection) params.append("orderDirection", orderDirection);

    // Request GET ke endpoint aplikasi
    return axios.get(`${baseUrl}/applications?${params.toString()}`);
}

// Membuat aplikasi baru dengan data yang diberikan
export async function createApplication(data) {
    return axios.post(`${baseUrl}/application`, data);
}

// Menghapus beberapa aplikasi berdasarkan array id
export async function deleteApplications(ids) {
    return axios.delete(`${baseUrl}/applications/delete`, {
        data: { ids },
    });
}

// Mengambil detail aplikasi berdasarkan id
export async function fetchApplicationDetail(id) {
    return axios.get(`${baseUrl}/applications/${id}`);
}

// Regenerasi secret key aplikasi berdasarkan id
export async function regenerateSecretKey(id) {
    return axios.post(`${baseUrl}/applications/application-status-change`, {
        id,
        status: "ubah-secret-key",
    });
}

// Mengubah status aplikasi (enabled/disabled) berdasarkan id
export async function changeApplicationStatus(id, status) {
    return axios.post(`${baseUrl}/applications/application-status-change`, {
        id,
        status,
    });
}
