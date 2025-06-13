// Import axios untuk HTTP request
import axios from "axios";

// Base URL dari environment variable
const baseUrl = import.meta.env.VITE_APP_URL;

// Mengupdate data profil user dengan data baru
export async function updateProfile(data) {
    return axios.put(`${baseUrl}/profile`, data);
}

// Mengupdate password user dengan data baru
export async function updatePassword(data) {
    return axios.put(`${baseUrl}/profile/password`, data);
}
