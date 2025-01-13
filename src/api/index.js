import axios from "axios";

// Auth API
export const auth = {
    register: (data) => axios.post("/auth/register", data),
    login: (data) => axios.post("/auth/login", data),
    logout: () => axios.post("/auth/logout"),
    generateOtp: (data) => axios.post("/auth/generate-otp-code", data),
    verifyEmail: (data) => axios.post("/auth/verification-email", data),
    getMe: () => axios.get("/me"),
    updateUser: (data) => axios.put("/user", data),
};

// Genre API
export const genres = {
    getAll: () => axios.get("/genre"),
    getById: (id) => axios.get(`/genre/${id}`),
    create: (data) => axios.post("/genre", data),
    update: (id, data) => axios.put(`/genre/${id}`, data),
    delete: (id) => axios.delete(`/genre/${id}`),
};

// Movie API
export const movies = {
    getAll: () => axios.get("/movie"),
    getById: (id) => axios.get(`/movie/${id}`),
    create: (data) => {
        const formData = new FormData();
        for (const [key, value] of Object.entries(data)) {
            if (key === "poster" && value instanceof File) {
                formData.append("poster", value);
            } else {
                formData.append(key, value);
            }
        }
        return axios.post("/movie", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    update: (id, data) => {
        const formData = new FormData();
        for (const [key, value] of Object.entries(data)) {
            if (key === "poster" && value instanceof File) {
                formData.append("poster", value);
            } else {
                formData.append(key, value);
            }
        }
        return axios.post(`/movie/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    delete: (id) => axios.delete(`/movie/${id}`),
};

// Cast API
export const casts = {
    getAll: () => axios.get("/cast"),
    getById: (id) => axios.get(`/cast/${id}`),
    create: (data) => {
        const formData = new FormData();
        for (const [key, value] of Object.entries(data)) {
            if (key === "photo" && value instanceof File) {
                formData.append("photo", value);
            } else {
                formData.append(key, value);
            }
        }
        return axios.post("/cast", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    update: (id, data) => {
        const formData = new FormData();
        for (const [key, value] of Object.entries(data)) {
            if (key === "photo" && value instanceof File) {
                formData.append("photo", value);
            } else {
                formData.append(key, value);
            }
        }
        return axios.post(`/cast/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    delete: (id) => axios.delete(`/cast/${id}`),
};

// Cast Movie API
export const castMovies = {
    getAll: () => axios.get("/cast-movie"),
    getById: (id) => axios.get(`/cast-movie/${id}`),
    create: (data) => axios.post("/cast-movie", data),
    update: (id, data) => axios.put(`/cast-movie/${id}`, data),
    delete: (id) => axios.delete(`/cast-movie/${id}`),
};

// Review API
export const reviews = {
    create: (data) => axios.post("/review", data),
};

// Error Handler
export const handleApiError = (error) => {
    if (error.response) {
        // Server responded with error
        const { status, data } = error.response;

        switch (status) {
            case 401:
                // Unauthorized - clear auth state and redirect to login
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                window.location.href = "/login";
                break;
            case 403:
                // Forbidden - user doesn't have required permissions
                alert("You do not have permission to perform this action");
                break;
            case 422:
                // Validation error
                return data.errors || data.message;
            default:
                // Other errors
                alert(data.message || "An error occurred");
        }
    } else if (error.request) {
        // Request made but no response received
        alert("Unable to connect to the server");
    } else {
        // Error in request configuration
        alert("An error occurred while making the request");
    }

    return null;
};
