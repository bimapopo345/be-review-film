<template>
    <div class="cast-edit">
        <div class="page-header">
            <h1>Edit Cast Member</h1>
            <div class="header-actions">
                <router-link :to="{ name: 'admin-casts' }" class="back-btn">
                    Back to Cast List
                </router-link>
            </div>
        </div>

        <div v-if="loading" class="loading">Loading cast details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <form v-else @submit.prevent="handleSubmit" class="edit-form">
            <div class="form-layout">
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

                    <div class="form-group">
                        <label for="bio">Biography</label>
                        <textarea
                            id="bio"
                            v-model="formData.bio"
                            required
                            class="form-control"
                            rows="6"
                            :disabled="submitting"
                        ></textarea>
                    </div>
                </div>

                <div class="form-sidebar">
                    <div class="photo-section">
                        <h3>Cast Photo</h3>
                        <div class="current-photo">
                            <img
                                :src="currentPhoto || '/placeholder-cast.jpg'"
                                alt="Cast photo"
                            />
                        </div>
                        <div class="form-group">
                            <label for="photo" class="file-input-label">
                                Change Photo
                                <input
                                    type="file"
                                    id="photo"
                                    @change="handleFileChange"
                                    accept="image/*"
                                    class="file-input"
                                    :disabled="submitting"
                                />
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button
                            type="submit"
                            class="submit-btn"
                            :disabled="submitting"
                        >
                            {{
                                submitting
                                    ? "Saving Changes..."
                                    : "Save Changes"
                            }}
                        </button>
                        <router-link
                            :to="{ name: 'admin-casts' }"
                            class="cancel-btn"
                        >
                            Cancel
                        </router-link>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { casts } from "@/api";
import { handleApiError } from "@/api";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const submitting = ref(false);
const error = ref(null);
const currentPhoto = ref(null);

const formData = ref({
    name: "",
    bio: "",
    photo: null,
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.photo = file;
        // Create preview URL
        currentPhoto.value = URL.createObjectURL(file);
    }
};

const handleSubmit = async () => {
    try {
        submitting.value = true;
        await casts.update(route.params.id, formData.value);
        router.push({ name: "admin-casts" });
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await casts.getById(route.params.id);
        const cast = response.data;

        formData.value = {
            name: cast.name,
            bio: cast.bio,
            photo: null,
        };

        currentPhoto.value = cast.photo;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.cast-edit {
    padding: 2rem;
    max-width: 1200px;
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

.form-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

.form-main {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.photo-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.photo-section h3 {
    margin-bottom: 1rem;
    color: #333;
}

.current-photo {
    margin-bottom: 1rem;
}

.current-photo img {
    width: 100%;
    height: auto;
    border-radius: 4px;
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

.file-input-label {
    display: block;
    padding: 0.75rem;
    background: #4a90e2;
    color: white;
    text-align: center;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.file-input-label:hover {
    background: #357abd;
}

.file-input {
    display: none;
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.submit-btn,
.cancel-btn {
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

@media (max-width: 768px) {
    .form-layout {
        grid-template-columns: 1fr;
    }
}
</style>
