<template>
    <div class="otp-container">
        <div class="otp-card">
            <h2>Generate OTP Code</h2>
            <div class="info-message" v-if="!loading">
                Please generate an OTP code to verify your email address.
            </div>

            <form @submit.prevent="handleSubmit" class="otp-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        v-model="email"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div v-if="error" class="error-message">
                    {{ error }}
                </div>

                <div v-if="success" class="success-message">
                    OTP code has been sent to your email. Please check your
                    inbox.
                </div>

                <button type="submit" class="submit-btn" :disabled="loading">
                    {{ loading ? "Generating OTP..." : "Generate OTP" }}
                </button>

                <div v-if="success" class="navigation-links">
                    <router-link to="/verify-email" class="verify-link">
                        Proceed to Email Verification
                    </router-link>
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
const email = ref(auth.user?.email || "");
const success = ref(false);

const loading = computed(() => auth.loading);
const error = computed(() => auth.error);

const handleSubmit = async () => {
    try {
        await auth.generateOtp(email.value);
        success.value = true;
    } catch (err) {
        handleApiError(err);
    }
};
</script>

<style scoped>
.otp-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 1rem;
}

.otp-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.otp-card h2 {
    text-align: center;
    margin-bottom: 1rem;
    color: #333;
}

.info-message {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
}

.otp-form {
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

.success-message {
    color: #28a745;
    text-align: center;
    font-size: 0.9rem;
    padding: 0.5rem;
    background-color: #d4edda;
    border-radius: 4px;
}

.navigation-links {
    text-align: center;
    margin-top: 1rem;
}

.verify-link {
    color: #4a90e2;
    text-decoration: none;
    font-weight: bold;
}

.verify-link:hover {
    text-decoration: underline;
}
</style>
