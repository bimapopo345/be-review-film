import { defineStore } from "pinia";
import { auth } from "@/api";
import router from "@/router";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        token: localStorage.getItem("token") || null,
        user: JSON.parse(localStorage.getItem("user") || "null"),
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === "admin",
        isVerified: (state) => state.user?.email_verified_at != null,
    },

    actions: {
        async login(credentials) {
            try {
                this.loading = true;
                this.error = null;
                const response = await auth.login(credentials);
                const { token, user } = response.data;

                this.token = token;
                this.user = user;

                localStorage.setItem("token", token);
                localStorage.setItem("user", JSON.stringify(user));

                router.push("/");
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            try {
                this.loading = true;
                this.error = null;
                const response = await auth.register(userData);
                const { token, user } = response.data;

                this.token = token;
                this.user = user;

                localStorage.setItem("token", token);
                localStorage.setItem("user", JSON.stringify(user));

                router.push("/generate-otp");
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Registration failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                this.loading = true;
                await auth.logout();
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                router.push("/login");
                this.loading = false;
            }
        },

        async generateOtp(email) {
            try {
                this.loading = true;
                this.error = null;
                await auth.generateOtp({ email });
                router.push("/verify-email");
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to generate OTP";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async verifyEmail(otp) {
            try {
                this.loading = true;
                this.error = null;
                const response = await auth.verifyEmail({ otp });
                this.user = response.data.user;
                localStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );
                router.push("/");
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Email verification failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateProfile(userData) {
            try {
                this.loading = true;
                this.error = null;
                const response = await auth.updateUser(userData);
                this.user = response.data.user;
                localStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Profile update failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            try {
                this.loading = true;
                this.error = null;
                const response = await auth.getMe();
                this.user = response.data.user;
                localStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to fetch user data";
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
