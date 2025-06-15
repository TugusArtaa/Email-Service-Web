import axios from "axios";
const baseUrl = import.meta.env.VITE_APP_URL;

// Mengambil daftar aplikasi yang menunggu persetujuan dengan filter pencarian, halaman, urutan, dan arah urutan
export async function fetchApproveApplications({
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

    // Request GET ke endpoint aplikasi yang menunggu persetujuan
    return axios.get(`${baseUrl}/applications/approve?${params.toString()}`);
}

// Melakukan approve aplikasi berdasarkan id
export async function approveApplication(id) {
    return axios.post(`${baseUrl}/applications/approve-application`, {
        id,
    });
}

// Mengubah status aplikasi (misal: reject atau status lain) berdasarkan id dan status baru
export async function changeApplicationStatus(id, status) {
    return axios.post(`${baseUrl}/applications/application-status-change`, {
        id,
        status,
    });
}
