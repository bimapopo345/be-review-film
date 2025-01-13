<template>
    <div class="verify-container">
        <div class="verify-card">
            <h2>Verify Email</h2>
            <div class="info-message" v-if="!loading && !success">
                Please enter the OTP code sent to your email address.
            </div>

            <form
                @submit.prevent="handleSubmit"
                class="verify-form"
                v-if="!success"
            >
                <div class="form-group">
                    <label for="otp">OTP Code</label>
                    <div class="otp-input-container">
                        <input
                            type="text"
                            id="otp"
                            v-model="otp"
                            required
                            class="form-control"
                            :disabled="loading"
                            maxlength="6"
                            placeholder="Enter 6-digit code"
                        />
                    </div>
                </div>

                <div v-if="error" class="error-message">
                    {{ error }}
                </div>

                <button type="submit" class="submit-btn" :disabled="loading">
                    {{ loading ? "Verifying..." : "Verify Email" }}
                </button>

                <div class="resend-section">
                    <p>Didn't receive the code?</p>
                    <button
                        type="button"
                        class="resend-btn"
                        @click="handleResend"
                        :disabled="loading"
                    >
                        Resend OTP
                    </button>
                </div>
            </form>

            <div v-if="success" class="success-container">
                <div class="success-message">
                    <i class="checkmark">✓</i>
                    <h3>Email Verified Successfully!</h3>
                    <p>
                        Your email has been verified. You can now access all
                        features.
                    </p>
                </div>
                <router-link to="/" class="home-link">
                    Go to Homepage
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const auth = useAuthStore();
const otp = ref("");
const success = ref(false);

const loading = computed(() => auth.loading);
const error = computed(() => auth.error);

const handleSubmit = async () => {
    try {
        await auth.verifyEmail(otp.value);
        success.value = true;
    } catch (err) {
        handleApiError(err);
    }
};

const handleResend = async () => {
    try {
        const email = auth.user?.email;
        if (!email) {
            throw new Error("Email not found. Please try again.");
        }
        await auth.generateOtp(email);
        alert("New OTP code has been sent to your email.");
    } catch (err) {
        handleApiError(err);
    }
};
</script>

<style scoped>
.verify-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 1rem;
}

.verify-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.verify-card h2 {
    text-align: center;
    margin-bottom: 1rem;
    color: #333;
}

.info-message {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
}

.verify-form {
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

.otp-input-container {
    display: flex;
    justify-content: center;
}

.form-control {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1.5rem;
    text-align: center;
    letter-spacing: 0.5rem;
    width: 100%;
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

.resend-section {
    text-align: center;
    margin-top: 1rem;
}

.resend-section p {
    margin-bottom: 0.5rem;
    color: #666;
}

.resend-btn {
    background: none;
    border: none;
    color: #4a90e2;
    cursor: pointer;
    font-size: 0.9rem;
    text-decoration: underline;
}

.resend-btn:hover:not(:disabled) {
    color: #357abd;
}

.resend-btn:disabled {
    color: #ccc;
    cursor: not-allowed;
}

.success-container {
    text-align: center;
}

.success-message {
    margin-bottom: 2rem;
}

.checkmark {
    color: #28a745;
    font-size: 3rem;
    line-height: 1;
    display: block;
    margin-bottom: 1rem;
}

.success-message h3 {
    color: #28a745;
    margin-bottom: 0.5rem;
}

.success-message p {
    color: #666;
}

.home-link {
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.home-link:hover {
    background-color: #218838;
}
</style>
