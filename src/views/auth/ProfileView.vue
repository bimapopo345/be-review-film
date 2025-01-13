<template>
    <div class="profile-container">
        <div class="profile-card">
            <h2>Profile</h2>

            <div v-if="!isEditing" class="profile-info">
                <div class="info-group">
                    <label>Name:</label>
                    <span>{{ user?.name }}</span>
                </div>
                <div class="info-group">
                    <label>Email:</label>
                    <span>{{ user?.email }}</span>
                    <span v-if="isVerified" class="verified-badge"
                        >Verified</span
                    >
                    <router-link v-else to="/generate-otp" class="verify-link">
                        Verify Email
                    </router-link>
                </div>
                <div class="info-group">
                    <label>Username:</label>
                    <span>{{ user?.username }}</span>
                </div>
                <div class="info-group">
                    <label>Phone:</label>
                    <span>{{ user?.phone }}</span>
                </div>
                <div class="info-group">
                    <label>Role:</label>
                    <span>{{ user?.role }}</span>
                </div>

                <button @click="startEditing" class="edit-btn">
                    Edit Profile
                </button>
            </div>

            <form v-else @submit.prevent="handleSubmit" class="profile-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        v-model="formData.name"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        v-model="formData.username"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input
                        type="tel"
                        id="phone"
                        v-model="formData.phone"
                        required
                        class="form-control"
                        :disabled="loading"
                    />
                </div>

                <div v-if="error" class="error-message">
                    {{ error }}
                </div>

                <div class="button-group">
                    <button
                        type="submit"
                        class="submit-btn"
                        :disabled="loading"
                    >
                        {{ loading ? "Saving..." : "Save Changes" }}
                    </button>
                    <button
                        type="button"
                        class="cancel-btn"
                        @click="cancelEditing"
                        :disabled="loading"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const auth = useAuthStore();
const isEditing = ref(false);

const user = computed(() => auth.user);
const loading = computed(() => auth.loading);
const error = computed(() => auth.error);
const isVerified = computed(() => auth.isVerified);

const formData = ref({
    name: "",
    username: "",
    phone: "",
});

const startEditing = () => {
    formData.value = {
        name: user.value?.name || "",
        username: user.value?.username || "",
        phone: user.value?.phone || "",
    };
    isEditing.value = true;
};

const cancelEditing = () => {
    isEditing.value = false;
    formData.value = {
        name: user.value?.name || "",
        username: user.value?.username || "",
        phone: user.value?.phone || "",
    };
};

const handleSubmit = async () => {
    try {
        await auth.updateProfile(formData.value);
        isEditing.value = false;
    } catch (err) {
        handleApiError(err);
    }
};

onMounted(async () => {
    try {
        await auth.fetchUser();
    } catch (err) {
        handleApiError(err);
    }
});
</script>

<style scoped>
.profile-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 80vh;
    padding: 2rem 1rem;
}

.profile-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
}

.profile-card h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

.profile-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-group {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.info-group label {
    font-weight: bold;
    color: #555;
    min-width: 100px;
}

.verified-badge {
    background-color: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.verify-link {
    color: #4a90e2;
    text-decoration: none;
    font-size: 0.9rem;
}

.verify-link:hover {
    text-decoration: underline;
}

.profile-form {
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

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.edit-btn,
.submit-btn,
.cancel-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.edit-btn {
    background-color: #4a90e2;
    color: white;
    width: 100%;
}

.edit-btn:hover {
    background-color: #357abd;
}

.submit-btn {
    background-color: #28a745;
    color: white;
    flex: 1;
}

.submit-btn:hover:not(:disabled) {
    background-color: #218838;
}

.cancel-btn {
    background-color: #dc3545;
    color: white;
    flex: 1;
}

.cancel-btn:hover:not(:disabled) {
    background-color: #c82333;
}

.submit-btn:disabled,
.cancel-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.error-message {
    color: #dc3545;
    text-align: center;
    font-size: 0.9rem;
}
</style>
