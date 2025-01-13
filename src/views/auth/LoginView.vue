<template>
    <div class="login-container">
        <div class="login-card">
            <h2>Login</h2>
            <form @submit.prevent="handleSubmit" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        v-model="formData.email"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        v-model="formData.password"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div v-if="error" class="error-message">
                    {{ error }}
                </div>

                <button type="submit" class="submit-btn" :disabled="loading">
                    {{ loading ? "Logging in..." : "Login" }}
                </button>

                <div class="form-footer">
                    <p>
                        Don't have an account?
                        <router-link to="/register">Register here</router-link>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const auth = useAuthStore();

const formData = ref({
    email: "",
    password: "",
});

const loading = computed(() => auth.loading);
const error = computed(() => auth.error);

const handleSubmit = async () => {
    try {
        await auth.login(formData.value);
    } catch (err) {
        handleApiError(err);
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 1rem;
}

.login-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.login-card h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: bold;
    color: #555;
}

.form-control {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.submit-btn {
    background-color: #4a90e2;
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
    background-color: #357abd;
}

.submit-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.error-message {
    color: #dc3545;
    text-align: center;
    font-size: 0.9rem;
}

.form-footer {
    text-align: center;
    margin-top: 1rem;
}

.form-footer a {
    color: #4a90e2;
    text-decoration: none;
}

.form-footer a:hover {
    text-decoration: underline;
}
</style>
