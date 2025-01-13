<template>
    <div class="genre-edit">
        <div class="page-header">
            <h1>Edit Genre</h1>
            <div class="header-actions">
                <router-link :to="{ name: 'admin-genres' }" class="back-btn">
                    Back to Genres
                </router-link>
            </div>
        </div>

        <div v-if="loading" class="loading">Loading genre details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <form v-else @submit.prevent="handleSubmit" class="edit-form">
            <div class="form-main">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        v-model="formData.name"
                        required
                        class="form-control"
                        :disabled="submitting"
                    />
                </div>

                <div class="form-actions">
                    <button
                        type="submit"
                        class="submit-btn"
                        :disabled="submitting"
                    >
                        {{ submitting ? "Saving Changes..." : "Save Changes" }}
                    </button>
                    <router-link
                        :to="{ name: 'admin-genres' }"
                        class="cancel-btn"
                    >
                        Cancel
                    </router-link>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { genres } from "@/api";
import { handleApiError } from "@/api";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const submitting = ref(false);
const error = ref(null);

const formData = ref({
    name: "",
});

const handleSubmit = async () => {
    try {
        submitting.value = true;
        await genres.update(route.params.id, formData.value);
        router.push({ name: "admin-genres" });
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await genres.getById(route.params.id);
        formData.value = {
            name: response.data.name,
        };
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.genre-edit {
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.back-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.back-btn:hover {
    background: #5a6268;
}

.form-main {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #555;
}

.form-control {
    width: 100%;
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

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.submit-btn,
.cancel-btn {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.2s;
}

.submit-btn {
    background: #28a745;
    color: white;
}

.submit-btn:hover:not(:disabled) {
    background: #218838;
}

.submit-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.cancel-btn {
    background: #dc3545;
    color: white;
    text-decoration: none;
}

.cancel-btn:hover {
    background: #c82333;
}

.loading,
.error-message {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #666;
}

.error-message {
    background: #f8d7da;
    color: #dc3545;
}
</style>
