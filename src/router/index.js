import { createRouter, createWebHistory } from "vue-router";

// Auth Views
import LoginView from "@/views/auth/LoginView.vue";
import RegisterView from "@/views/auth/RegisterView.vue";
import OtpGenerateView from "@/views/auth/OtpGenerateView.vue";
import OtpVerifyView from "@/views/auth/OtpVerifyView.vue";
import ProfileView from "@/views/auth/ProfileView.vue";

// Admin Views
import AdminGenreList from "@/views/admin/genre/GenreList.vue";
import AdminGenreEdit from "@/views/admin/genre/GenreEdit.vue";
import AdminMovieList from "@/views/admin/movie/MovieList.vue";
import AdminMovieEdit from "@/views/admin/movie/MovieEdit.vue";
import AdminCastList from "@/views/admin/cast/CastList.vue";
import AdminCastEdit from "@/views/admin/cast/CastEdit.vue";
import AdminCastMovieList from "@/views/admin/cast-movie/CastMovieList.vue";
import AdminCastMovieEdit from "@/views/admin/cast-movie/CastMovieEdit.vue";

// Public Views
import HomeView from "@/views/HomeView.vue";
import MovieList from "@/views/public/movie/MovieList.vue";
import MovieDetail from "@/views/public/movie/MovieDetail.vue";
import CastList from "@/views/public/cast/CastList.vue";
import CastDetail from "@/views/public/cast/CastDetail.vue";
import GenreList from "@/views/public/genre/GenreList.vue";
import GenreDetail from "@/views/public/genre/GenreDetail.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
        },
        // Auth Routes
        {
            path: "/login",
            name: "login",
            component: LoginView,
            meta: { requiresGuest: true },
        },
        {
            path: "/register",
            name: "register",
            component: RegisterView,
            meta: { requiresGuest: true },
        },
        {
            path: "/generate-otp",
            name: "generate-otp",
            component: OtpGenerateView,
            meta: { requiresAuth: true },
        },
        {
            path: "/verify-email",
            name: "verify-email",
            component: OtpVerifyView,
            meta: { requiresAuth: true },
        },
        {
            path: "/profile",
            name: "profile",
            component: ProfileView,
            meta: { requiresAuth: true },
        },
        // Admin Routes
        {
            path: "/admin/genres",
            name: "admin-genres",
            component: AdminGenreList,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/genres/:id/edit",
            name: "admin-genres-edit",
            component: AdminGenreEdit,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/movies",
            name: "admin-movies",
            component: AdminMovieList,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/movies/:id/edit",
            name: "admin-movies-edit",
            component: AdminMovieEdit,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/casts",
            name: "admin-casts",
            component: AdminCastList,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/casts/:id/edit",
            name: "admin-casts-edit",
            component: AdminCastEdit,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/cast-movies",
            name: "admin-cast-movies",
            component: AdminCastMovieList,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        {
            path: "/admin/cast-movies/:id/edit",
            name: "admin-cast-movies-edit",
            component: AdminCastMovieEdit,
            meta: { requiresAuth: true, requiresAdmin: true },
        },
        // Public Routes
        {
            path: "/movies",
            name: "movies",
            component: MovieList,
        },
        {
            path: "/movies/:id",
            name: "movie-detail",
            component: MovieDetail,
        },
        {
            path: "/casts",
            name: "casts",
            component: CastList,
        },
        {
            path: "/casts/:id",
            name: "cast-detail",
            component: CastDetail,
        },
        {
            path: "/genres",
            name: "genres",
            component: GenreList,
        },
        {
            path: "/genres/:id",
            name: "genre-detail",
            component: GenreDetail,
        },
    ],
});

// Navigation Guards
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("token");
    const user = JSON.parse(localStorage.getItem("user") || "{}");
    const isAdmin = user.role === "admin";

    // Routes that require guest access
    if (to.meta.requiresGuest && isAuthenticated) {
        return next("/");
    }

    // Routes that require authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next("/login");
    }

    // Routes that require admin access
    if (to.meta.requiresAdmin && !isAdmin) {
        return next("/");
    }

    next();
});

export default router;
