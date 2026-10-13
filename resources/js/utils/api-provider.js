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

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

http.interceptors.request.use(
    async (config) => {
        Loading.start();

        const method = config.method?.toLowerCase();

        await sleep(500);

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
        try {
            const response = await http.get(url, { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    },
    async post(url, data = null) {
        try {
            const response = await http.post(url, data);
            return response.data;
        } catch (error) {
            throw error;
        }
    },
    async put(url, data = null) {
        try {
            const response = await http.put(url, data);
            return response.data;
        } catch (error) {
            throw error;
        }
    },
    async delete(url) {
        try {
            const response = await http.delete(url);
            return response.data;
        } catch (error) {
            throw error;
        }
    },
    handleAxiosError(error) {
        const response = error.response;
        const lang = window.langApiProvider ?? {};
        const errTitle = window.langCustomAlert?.error ?? "Error";

        if (!response) {
            Toast.error(
                errTitle,
                lang.networkError ?? "Network Error or Server Down",
            );
            return;
        }

        switch (response.status) {
            case 401:
                Toast.error(errTitle, lang.invalidSession ?? "Invalid Session");
                setTimeout(() => window.location.reload(), 4000);
                break;
            case 403:
                Toast.error(errTitle, lang.accessDenied ?? "Access Denied");
                break;
            case 404:
                Toast.error(errTitle, lang.notFound ?? "Data not found");
                break;
            case 419:
                Toast.error(
                    errTitle,
                    lang.pageExpired ?? "Page Expired. Refreshing...",
                );
                setTimeout(() => window.location.reload(), 4000);
                break;
            case 422:
                if (response.data.errors) {
                    Object.values(response.data.errors)
                        .flat()
                        .forEach((msg) => {
                            Toast.error(
                                lang.validationError ?? "Validation Error",
                                msg,
                                4000,
                            );
                        });
                } else {
                    Toast.error(
                        lang.validationError ?? "Validation Error",
                        response.data.message,
                    );
                }
                break;
            case 500:
                Toast.error(
                    lang.serverError ?? "Server Error",
                    lang.internalServerError ?? "Internal Server Error",
                );
                break;
            default:
                Toast.error(
                    errTitle,
                    response.data.message ||
                        (lang.unknownError ?? "Unknown Error"),
                );
                break;
        }
    },
};

export default ApiProvider;
