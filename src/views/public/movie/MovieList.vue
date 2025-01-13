<template>
    <div class="movie-list">
        <h1>Movies</h1>

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

        <div v-else-if="filteredMovies.length" class="movie-grid">
            <div
                v-for="movie in filteredMovies"
                :key="movie.id"
                class="movie-card"
            >
                <img
                    :src="movie.poster || '/placeholder-movie.jpg'"
                    :alt="movie.title"
                    class="movie-poster"
                />
                <div class="movie-info">
                    <h3>{{ movie.title }}</h3>
                    <p class="movie-year">{{ movie.release_date }}</p>
                    <div class="movie-genres">
                        <span
                            v-for="genre in movie.genres"
                            :key="genre.id"
                            class="genre-tag"
                        >
                            {{ genre.name }}
                        </span>
                    </div>
                    <p class="movie-description">
                        {{ truncateText(movie.description, 100) }}
                    </p>
                    <div class="movie-actions">
                        <router-link
                            :to="{
                                name: 'movie-detail',
                                params: { id: movie.id },
                            }"
                            class="view-details"
                        >
                            View Details
                        </router-link>
                        <button
                            v-if="isVerified"
                            @click="showReviewModal(movie)"
                            class="review-btn"
                        >
                            Write Review
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="no-results">
            No movies found matching your criteria.
        </div>

        <!-- Review Modal -->
        <div v-if="showModal" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <h3>Write Review for {{ selectedMovie?.title }}</h3>
                <form @submit.prevent="submitReview" class="review-form">
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select
                            id="rating"
                            v-model="reviewForm.rating"
                            required
                            class="form-control"
                        >
                            <option value="">Select rating</option>
                            <option v-for="n in 5" :key="n" :value="n">
                                {{ n }} stars
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea
                            id="comment"
                            v-model="reviewForm.comment"
                            required
                            class="form-control"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="modal-actions">
                        <button
                            type="submit"
                            class="submit-btn"
                            :disabled="submitting"
                        >
                            {{ submitting ? "Submitting..." : "Submit Review" }}
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
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { movies, genres as genresApi, reviews } from "@/api";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const auth = useAuthStore();
const isVerified = computed(() => auth.isVerified);

const moviesList = ref([]);
const genres = ref([]);
const loading = ref(false);
const error = ref(null);
const searchQuery = ref("");
const selectedGenre = ref("");
const showModal = ref(false);
const selectedMovie = ref(null);
const submitting = ref(false);

const reviewForm = ref({
    rating: "",
    comment: "",
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

const showReviewModal = (movie) => {
    selectedMovie.value = movie;
    showModal.value = true;
    reviewForm.value = {
        rating: "",
        comment: "",
    };
};

const closeModal = () => {
    showModal.value = false;
    selectedMovie.value = null;
};

const submitReview = async () => {
    if (!selectedMovie.value) return;

    try {
        submitting.value = true;
        await reviews.create({
            movie_id: selectedMovie.value.id,
            rating: reviewForm.value.rating,
            comment: reviewForm.value.comment,
        });
        closeModal();
        // Could add success notification here
    } catch (err) {
        handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

const truncateText = (text, length) => {
    if (text.length <= length) return text;
    return text.substring(0, length) + "...";
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
.movie-list {
    padding: 2rem;
}

.movie-list h1 {
    margin-bottom: 2rem;
    color: #333;
}

.filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.search-box {
    flex: 1;
}

.search-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.genre-select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    min-width: 150px;
}

.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.movie-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.movie-card:hover {
    transform: translateY(-5px);
}

.movie-poster {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.movie-info {
    padding: 1.5rem;
}

.movie-info h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

.movie-year {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.movie-genres {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.genre-tag {
    background: #f0f0f0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #666;
}

.movie-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.movie-actions {
    display: flex;
    gap: 1rem;
}

.view-details,
.review-btn {
    flex: 1;
    text-align: center;
    padding: 0.75rem;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.view-details {
    background: #4a90e2;
    color: white;
    text-decoration: none;
}

.view-details:hover {
    background: #357abd;
}

.review-btn {
    background: #28a745;
    color: white;
    border: none;
}

.review-btn:hover {
    background: #218838;
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

.modal-content h3 {
    margin-bottom: 1.5rem;
    color: #333;
}

.review-form {
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
.error-message,
.no-results {
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
