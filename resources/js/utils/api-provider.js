import axios from "axios";
import Loading from "@/utils/loading";

const metaToken = document.querySelector('meta[name="csrf-token"]');
const csrfToken = metaToken ? metaToken.getAttribute("content") : "";

const http = axios.create({
    headers: {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
    },
});

http.interceptors.request.use(
    (config) => {
        Loading.start();
        return config;
    },
    (error) => {
        Loading.stop();
        return Promise.reject(error);
    },
);

http.interceptors.response.use(
    (response) => {
        Loading.stop();
        return response;
    },
    (error) => {
        Loading.stop();
        ApiProvider.handleAxiosError(error);
        return Promise.reject(error);
    },
);

const ApiProvider = {
    async get(url, params = null) {
        const response = await http.get(url, { params });
        return response.data;
    },
    async post(url, data = null) {
        const response = await http.post(url, data);
        return response.data;
    },
    async put(url, data = null) {
        const response = await http.put(url, data);
        return response.data;
    },
    async delete(url) {
        const response = await http.delete(url);
        return response.data;
    },
    handleAxiosError(error) {
        const response = error.response;
        const errTitle = "Error";

        if (!response) {
            if (window.Toast?.error) window.Toast.error(errTitle, "Koneksi bermasalah atau server tidak merespon");
            return;
        }

        switch (response.status) {
            case 401:
                if (window.Toast?.error) window.Toast.error(errTitle, "Sesi berakhir. Memuat ulang halaman...");
                setTimeout(() => window.location.reload(), 3000);
                break;
            case 403:
                if (window.Toast?.error) window.Toast.error(errTitle, "Akses ditolak (403)");
                break;
            case 404:
                if (window.Toast?.error) window.Toast.error(errTitle, "Data tidak ditemukan (404)");
                break;
            case 419:
                if (window.Toast?.error) window.Toast.error(errTitle, "Sesi kedaluwarsa. Memuat ulang...");
                setTimeout(() => window.location.reload(), 3000);
                break;
            case 422:
                if (response.data.errors) {
                    Object.values(response.data.errors)
                        .flat()
                        .forEach((msg) => {
                            if (window.Toast?.error) window.Toast.error("Validasi Gagal", msg);
                        });
                } else if (window.Toast?.error) {
                    window.Toast.error("Validasi Gagal", response.data.message);
                }
                break;
            case 500:
                if (window.Toast?.error) window.Toast.error(errTitle, "Terjadi kesalahan internal server (500)");
                break;
            default:
                if (window.Toast?.error) window.Toast.error(errTitle, response.data.message || "Terjadi kesalahan");
                break;
        }
    },
};

export default ApiProvider;
