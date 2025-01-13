<template>
    <div class="admin-movie-list">
        <div class="page-header">
            <h1>Manage Movies</h1>
            <button @click="showCreateModal = true" class="create-btn">
                Add New Movie
            </button>
        </div>

        <div class="filters">
            <div class="search-box">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search movies..."
                    class="search-input"
                    @input="handleSearch"
                />
            </div>

            <div class="genre-filter">
                <select
                    v-model="selectedGenre"
                    @change="handleGenreFilter"
                    class="genre-select"
                >
                    <option value="">All Genres</option>
                    <option
                        v-for="genre in genres"
                        :key="genre.id"
                        :value="genre.id"
                    >
                        {{ genre.name }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="loading">Loading movies...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <div v-else class="movie-table-container">
            <table class="movie-table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Release Date</th>
                        <th>Duration</th>
                        <th>Genres</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="movie in filteredMovies" :key="movie.id">
                        <td>
                            <img
                                :src="movie.poster || '/placeholder-movie.jpg'"
                                :alt="movie.title"
                                class="movie-thumbnail"
                            />
                        </td>
                        <td>{{ movie.title }}</td>
                        <td>{{ movie.release_date }}</td>
                        <td>{{ movie.duration }} min</td>
                        <td>
                            <div class="genre-tags">
                                <span
                                    v-for="genre in movie.genres"
                                    :key="genre.id"
                                    class="genre-tag"
                                >
                                    {{ genre.name }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button
                                    @click="editMovie(movie)"
                                    class="edit-btn"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="confirmDelete(movie)"
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
                <h2>{{ showEditModal ? "Edit Movie" : "Create New Movie" }}</h2>
                <form @submit.prevent="handleSubmit" class="movie-form">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input
                            type="text"
                            id="title"
                            v-model="formData.title"
                            required
                            class="form-control"
                        />
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea
                            id="description"
                            v-model="formData.description"
                            required
                            class="form-control"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="release_date">Release Date</label>
                            <input
                                type="date"
                                id="release_date"
                                v-model="formData.release_date"
                                required
                                class="form-control"
                            />
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration (minutes)</label>
                            <input
                                type="number"
                                id="duration"
                                v-model="formData.duration"
                                required
                                class="form-control"
                                min="1"
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="genres">Genres</label>
                        <div class="genre-checkboxes">
                            <label
                                v-for="genre in genres"
                                :key="genre.id"
                                class="checkbox-label"
                            >
                                <input
                                    type="checkbox"
                                    :value="genre.id"
                                    v-model="formData.genre_ids"
                                />
                                {{ genre.name }}
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="poster">Poster Image</label>
                        <input
                            type="file"
                            id="poster"
                            @change="handleFileChange"
                            accept="image/*"
                            class="form-control"
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
                <h2>Delete Movie</h2>
                <p>
                    Are you sure you want to delete "{{
                        movieToDelete?.title
                    }}"?
                </p>
                <div class="modal-actions">
                    <button
                        @click="deleteMovie"
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
import { ref, computed, onMounted } from "vue";
import { movies, genres as genresApi } from "@/api";
import { handleApiError } from "@/api";

const moviesList = ref([]);
const genres = ref([]);
const loading = ref(false);
const error = ref(null);
const searchQuery = ref("");
const selectedGenre = ref("");
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const submitting = ref(false);
const movieToDelete = ref(null);

const formData = ref({
    title: "",
    description: "",
    release_date: "",
    duration: "",
    genre_ids: [],
    poster: null,
});

const filteredMovies = computed(() => {
    let filtered = [...moviesList.value];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (movie) =>
                movie.title.toLowerCase().includes(query) ||
                movie.description.toLowerCase().includes(query)
        );
    }

    if (selectedGenre.value) {
        filtered = filtered.filter((movie) =>
            movie.genres.some((genre) => genre.id === selectedGenre.value)
        );
    }

    return filtered;
});

const handleSearch = () => {
    // Debounce could be added here if needed
};

const handleGenreFilter = () => {
    // Additional filter logic could be added here
};

const resetForm = () => {
    formData.value = {
        title: "",
        description: "",
        release_date: "",
        duration: "",
        genre_ids: [],
        poster: null,
    };
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.poster = file;
    }
};

const editMovie = (movie) => {
    formData.value = {
        title: movie.title,
        description: movie.description,
        release_date: movie.release_date,
        duration: movie.duration,
        genre_ids: movie.genres.map((g) => g.id),
        poster: null,
    };
    showEditModal.value = true;
};

const confirmDelete = (movie) => {
    movieToDelete.value = movie;
    showDeleteModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    movieToDelete.value = null;
    resetForm();
};

const handleSubmit = async () => {
    try {
        submitting.value = true;
        if (showEditModal.value) {
            await movies.update(movieToDelete.value.id, formData.value);
        } else {
            await movies.create(formData.value);
        }

        // Refresh movie list
        const response = await movies.getAll();
        moviesList.value = response.data;

        closeModal();
    } catch (err) {
        handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

const deleteMovie = async () => {
    if (!movieToDelete.value) return;

    try {
        submitting.value = true;
        await movies.delete(movieToDelete.value.id);

        // Remove from list
        moviesList.value = moviesList.value.filter(
            (m) => m.id !== movieToDelete.value.id
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
        const [moviesResponse, genresResponse] = await Promise.all([
            movies.getAll(),
            genresApi.getAll(),
        ]);
        moviesList.value = moviesResponse.data;
        genres.value = genresResponse.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.admin-movie-list {
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

.filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.search-input,
.genre-select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.search-input {
    flex: 1;
}

.genre-select {
    min-width: 150px;
}

.movie-table-container {
    overflow-x: auto;
}

.movie-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.movie-table th,
.movie-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.movie-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.movie-thumbnail {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.genre-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.genre-tag {
    background: #f0f0f0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #666;
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
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.movie-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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

.genre-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
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
