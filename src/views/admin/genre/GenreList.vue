<template>
    <div class="genre-list">
        <div class="page-header">
            <h1>Manage Genres</h1>
            <button @click="showCreateModal = true" class="create-btn">
                Add New Genre
            </button>
        </div>

        <div v-if="loading" class="loading">Loading genres...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <div v-else class="genre-table-container">
            <table class="genre-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="genre in genresList" :key="genre.id">
                        <td>{{ genre.name }}</td>
                        <td>
                            <div class="action-buttons">
                                <button
                                    @click="editGenre(genre)"
                                    class="edit-btn"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="confirmDelete(genre)"
                                    class="delete-btn"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showCreateModal || showEditModal"
            class="modal-overlay"
            @click="closeModal"
        >
            <div class="modal-content" @click.stop>
                <h2>{{ showEditModal ? "Edit Genre" : "Create New Genre" }}</h2>
                <form @submit.prevent="handleSubmit" class="genre-form">
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

                    <div class="modal-actions">
                        <button
                            type="submit"
                            class="submit-btn"
                            :disabled="submitting"
                        >
                            {{
                                submitting
                                    ? "Saving..."
                                    : showEditModal
                                    ? "Update"
                                    : "Create"
                            }}
                        </button>
                        <button
                            type="button"
                            class="cancel-btn"
                            @click="closeModal"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="modal-overlay"
            @click="showDeleteModal = false"
        >
            <div class="modal-content" @click.stop>
                <h2>Delete Genre</h2>
                <p>
                    Are you sure you want to delete "{{ genreToDelete?.name }}"?
                </p>
                <div class="modal-actions">
                    <button
                        @click="deleteGenre"
                        class="delete-btn"
                        :disabled="submitting"
                    >
                        {{ submitting ? "Deleting..." : "Delete" }}
                    </button>
                    <button @click="showDeleteModal = false" class="cancel-btn">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { genres } from "@/api";
import { handleApiError } from "@/api";

const genresList = ref([]);
const loading = ref(false);
const error = ref(null);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const submitting = ref(false);
const genreToDelete = ref(null);

const formData = ref({
    name: "",
});

const resetForm = () => {
    formData.value = {
        name: "",
    };
};

const editGenre = (genre) => {
    formData.value = {
        name: genre.name,
    };
    genreToDelete.value = genre;
    showEditModal.value = true;
};

const confirmDelete = (genre) => {
    genreToDelete.value = genre;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    genreToDelete.value = null;
    resetForm();
};

const handleSubmit = async () => {
    try {
        submitting.value = true;
        if (showEditModal.value) {
            await genres.update(genreToDelete.value.id, formData.value);
        } else {
            await genres.create(formData.value);
        }

        // Refresh genre list
        const response = await genres.getAll();
        genresList.value = response.data;

        closeModal();
    } catch (err) {
        handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

const deleteGenre = async () => {
    if (!genreToDelete.value) return;

    try {
        submitting.value = true;
        await genres.delete(genreToDelete.value.id);

        // Remove from list
        genresList.value = genresList.value.filter(
            (g) => g.id !== genreToDelete.value.id
        );

        closeModal();
    } catch (err) {
        handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await genres.getAll();
        genresList.value = response.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.genre-list {
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.create-btn {
    background: #28a745;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.create-btn:hover {
    background: #218838;
}

.genre-table-container {
    overflow-x: auto;
}

.genre-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.genre-table th,
.genre-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.genre-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.edit-btn,
.delete-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.edit-btn {
    background: #4a90e2;
    color: white;
}

.edit-btn:hover {
    background: #357abd;
}

.delete-btn {
    background: #dc3545;
    color: white;
}

.delete-btn:hover {
    background: #c82333;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
}

.modal-content h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.genre-form {
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

.modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.submit-btn,
.cancel-btn {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
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

.cancel-btn {
    background: #dc3545;
    color: white;
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
