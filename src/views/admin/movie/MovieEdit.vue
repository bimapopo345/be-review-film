<template>
    <div class="movie-edit">
        <div class="page-header">
            <h1>Edit Movie</h1>
            <div class="header-actions">
                <router-link :to="{ name: 'admin-movies' }" class="back-btn">
                    Back to Movies
                </router-link>
            </div>
        </div>

        <div v-if="loading" class="loading">Loading movie details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <form v-else @submit.prevent="handleSubmit" class="edit-form">
            <div class="form-layout">
                <div class="form-main">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input
                            type="text"
                            id="title"
                            v-model="formData.title"
                            required
                            class="form-control"
                            :disabled="submitting"
                        />
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea
                            id="description"
                            v-model="formData.description"
                            required
                            class="form-control"
                            rows="6"
                            :disabled="submitting"
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
                                :disabled="submitting"
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
                                :disabled="submitting"
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Genres</label>
                        <div class="genre-grid">
                            <label
                                v-for="genre in genres"
                                :key="genre.id"
                                class="genre-checkbox"
                            >
                                <input
                                    type="checkbox"
                                    :value="genre.id"
                                    v-model="formData.genre_ids"
                                    :disabled="submitting"
                                />
                                <span class="checkbox-label">{{
                                    genre.name
                                }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-sidebar">
                    <div class="poster-section">
                        <h3>Movie Poster</h3>
                        <div class="current-poster">
                            <img
                                :src="currentPoster || '/placeholder-movie.jpg'"
                                alt="Movie poster"
                            />
                        </div>
                        <div class="form-group">
                            <label for="poster" class="file-input-label">
                                Change Poster
                                <input
                                    type="file"
                                    id="poster"
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
                            :to="{ name: 'admin-movies' }"
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
import { movies, genres as genresApi } from "@/api";
import { handleApiError } from "@/api";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const submitting = ref(false);
const error = ref(null);
const genres = ref([]);
const currentPoster = ref(null);

const formData = ref({
    title: "",
    description: "",
    release_date: "",
    duration: "",
    genre_ids: [],
    poster: null,
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.poster = file;
        // Create preview URL
        currentPoster.value = URL.createObjectURL(file);
    }
};

const handleSubmit = async () => {
    try {
        submitting.value = true;
        await movies.update(route.params.id, formData.value);
        router.push({ name: "admin-movies" });
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        loading.value = true;
        const [movieResponse, genresResponse] = await Promise.all([
            movies.getById(route.params.id),
            genresApi.getAll(),
        ]);

        const movie = movieResponse.data;
        genres.value = genresResponse.data;

        formData.value = {
            title: movie.title,
            description: movie.description,
            release_date: movie.release_date,
            duration: movie.duration,
            genre_ids: movie.genres.map((g) => g.id),
            poster: null,
        };

        currentPoster.value = movie.poster;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.movie-edit {
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

.poster-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.poster-section h3 {
    margin-bottom: 1rem;
    color: #333;
}

.current-poster {
    margin-bottom: 1rem;
}

.current-poster img {
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.genre-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.genre-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label {
    color: #555;
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
