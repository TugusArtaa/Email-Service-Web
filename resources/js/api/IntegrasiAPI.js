// Import axios untuk HTTP request
import axios from "axios";

// Base URL dari environment variable
const baseUrl = import.meta.env.VITE_APP_URL;

// Mengambil daftar log email dengan filter urutan, pencarian, halaman, dan tanggal
export async function fetchEmailLogs({ orderBy, search, page, date }) {
    // Menyusun parameter query untuk request
    const params = new URLSearchParams();
    if (orderBy) params.append("orderBy", orderBy);
    if (search) params.append("search", search);
    if (page) params.append("page", page);
    if (date) params.append("date", date);

    const url = `${baseUrl}/email-logs?${params.toString()}`;
    // Request GET ke endpoint email logs
    return axios.get(url);
}

// Menghapus beberapa log email berdasarkan array id, bisa override baseUrl jika diperlukan
export async function deleteEmailLogs(ids, baseUrlOverride = null) {
    const url = `${baseUrlOverride || baseUrl}/integrasi/delete`;
    return axios.delete(url, { data: { ids } });
}

// Mengekstrak log email berdasarkan id
export async function extractEmailLog(id) {
    return axios.post(`${baseUrl}/email-queue/extract`, { id });
}

// Mengirim ulang email berdasarkan formData yang diberikan
export async function retrySendEmail(formData) {
    return axios.post(`${baseUrl}/api/email-queue/send`, formData);
}

// Mengupload file excel untuk pengiriman email massal
export async function uploadExcel(formData) {
    return axios.post(`${baseUrl}/api/email-queue/sendExcel`, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
}
