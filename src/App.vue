<template>
    <div class="app">
        <nav class="navbar">
            <div class="nav-brand">Movie Review</div>
            <div class="nav-links">
                <!-- Public Routes -->
                <router-link to="/">Home</router-link>
                <router-link to="/movies">Movies</router-link>
                <router-link to="/casts">Casts</router-link>
                <router-link to="/genres">Genres</router-link>

                <!-- Auth Routes -->
                <template v-if="!isAuthenticated">
                    <router-link to="/login">Login</router-link>
                    <router-link to="/register">Register</router-link>
                </template>

                <!-- Authenticated Routes -->
                <template v-else>
                    <router-link to="/profile">Profile</router-link>
                    <!-- Admin Routes -->
                    <template v-if="isAdmin">
                        <div class="dropdown">
                            <button class="dropdown-btn">Admin</button>
                            <div class="dropdown-content">
                                <router-link to="/admin/movies"
                                    >Movies</router-link
                                >
                                <router-link to="/admin/casts"
                                    >Casts</router-link
                                >
                                <router-link to="/admin/genres"
                                    >Genres</router-link
                                >
                                <router-link to="/admin/cast-movies"
                                    >Cast Movies</router-link
                                >
                            </div>
                        </div>
                    </template>
                    <button @click="handleLogout" class="logout-btn">
                        Logout
                    </button>
                </template>
            </div>
        </nav>

        <main class="main-content">
            <router-view></router-view>
        </main>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const isAuthenticated = computed(() => {
    return !!localStorage.getItem("token");
});

const isAdmin = computed(() => {
    const user = JSON.parse(localStorage.getItem("user") || "{}");
    return user.role === "admin";
});

const handleLogout = async () => {
    try {
        await axios.post("/auth/logout");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        router.push("/login");
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>

<style>
.app {
    font-family: Arial, sans-serif;
}

.navbar {
    background-color: #333;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.nav-brand {
    font-size: 1.5rem;
    font-weight: bold;
}

.nav-links {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.nav-links a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.nav-links a:hover {
    background-color: #444;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-btn {
    background-color: #444;
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #333;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a {
    display: block;
    padding: 0.5rem 1rem;
}

.logout-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.logout-btn:hover {
    background-color: #c82333;
}

.main-content {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
</style>
