<template>
    <div class="movie-detail">
        <div v-if="loading" class="loading">Loading movie details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <template v-else-if="movie">
            <div class="movie-header">
                <div class="movie-poster">
                    <img
                        :src="movie.poster || '/placeholder-movie.jpg'"
                        :alt="movie.title"
                    />
                </div>
                <div class="movie-info">
                    <h1>{{ movie.title }}</h1>
                    <div class="movie-meta">
                        <span class="release-date">{{
                            movie.release_date
                        }}</span>
                        <span class="duration">{{ movie.duration }} min</span>
                        <div class="genres">
                            <span
                                v-for="genre in movie.genres"
                                :key="genre.id"
                                class="genre-tag"
                            >
                                {{ genre.name }}
                            </span>
                        </div>
                    </div>
                    <p class="description">{{ movie.description }}</p>

                    <div class="rating-section">
                        <div class="average-rating">
                            <h3>Average Rating</h3>
                            <div class="rating-display">
                                <span class="rating-number">{{
                                    averageRating
                                }}</span>
                                <span class="rating-count"
                                    >({{
                                        movie.reviews?.length || 0
                                    }}
                                    reviews)</span
                                >
                            </div>
                        </div>

                        <button
                            v-if="isVerified"
                            @click="showReviewModal = true"
                            class="write-review-btn"
                        >
                            Write a Review
                        </button>
                        <router-link
                            v-else-if="isAuthenticated"
                            to="/generate-otp"
                            class="verify-email-btn"
                        >
                            Verify Email to Review
                        </router-link>
                        <router-link v-else to="/login" class="login-btn">
                            Login to Review
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="cast-section">
                <h2>Cast</h2>
                <div class="cast-grid">
                    <div
                        v-for="castMovie in movie.cast_movies"
                        :key="castMovie.id"
                        class="cast-card"
                    >
                        <img
                            :src="
                                castMovie.cast.photo || '/placeholder-cast.jpg'
                            "
                            :alt="castMovie.cast.name"
                            class="cast-photo"
                        />
                        <div class="cast-info">
                            <h4>{{ castMovie.cast.name }}</h4>
                            <p>{{ castMovie.role }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reviews-section">
                <h2>Reviews</h2>
                <div v-if="movie.reviews?.length" class="reviews-list">
                    <div
                        v-for="review in movie.reviews"
                        :key="review.id"
                        class="review-card"
                    >
                        <div class="review-header">
                            <div class="reviewer-info">
                                <span class="reviewer-name">{{
                                    review.user.name
                                }}</span>
                                <span class="review-date">{{
                                    formatDate(review.created_at)
                                }}</span>
                            </div>
                            <div class="rating-stars">
                                {{ "★".repeat(review.rating)
                                }}{{ "☆".repeat(5 - review.rating) }}
                            </div>
                        </div>
                        <p class="review-comment">{{ review.comment }}</p>
                    </div>
                </div>
                <div v-else class="no-reviews">
                    No reviews yet. Be the first to review this movie!
                </div>
            </div>
        </template>

        <!-- Review Modal -->
        <div
            v-if="showReviewModal"
            class="modal-overlay"
            @click="showReviewModal = false"
        >
            <div class="modal-content" @click.stop>
                <h3>Write Review</h3>
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
                            @click="showReviewModal = false"
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
import { useRoute } from "vue-router";
import { movies, reviews } from "@/api";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const route = useRoute();
const auth = useAuthStore();

const movie = ref(null);
const loading = ref(false);
const error = ref(null);
const showReviewModal = ref(false);
const submitting = ref(false);

const reviewForm = ref({
    rating: "",
    comment: "",
});

const isAuthenticated = computed(() => auth.isAuthenticated);
const isVerified = computed(() => auth.isVerified);

const averageRating = computed(() => {
    if (!movie.value?.reviews?.length) return "N/A";
    const total = movie.value.reviews.reduce(
        (sum, review) => sum + review.rating,
        0
    );
    return (total / movie.value.reviews.length).toFixed(1);
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const submitReview = async () => {
    try {
        submitting.value = true;
        await reviews.create({
            movie_id: movie.value.id,
            rating: reviewForm.value.rating,
            comment: reviewForm.value.comment,
        });

        // Refresh movie data to show new review
        const response = await movies.getById(route.params.id);
        movie.value = response.data;

        showReviewModal.value = false;
        reviewForm.value = { rating: "", comment: "" };
    } catch (err) {
        handleApiError(err);
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await movies.getById(route.params.id);
        movie.value = response.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.movie-detail {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.movie-header {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.movie-poster img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.movie-info h1 {
    margin-bottom: 1rem;
    color: #333;
}

.movie-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    color: #666;
}

.genres {
    display: flex;
    gap: 0.5rem;
}

.genre-tag {
    background: #f0f0f0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.description {
    margin-bottom: 2rem;
    line-height: 1.6;
    color: #444;
}

.rating-section {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.average-rating {
    text-align: center;
}

.rating-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.rating-number {
    font-size: 2rem;
    font-weight: bold;
    color: #4a90e2;
}

.rating-count {
    color: #666;
}

.write-review-btn,
.verify-email-btn,
.login-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
    text-decoration: none;
}

.write-review-btn {
    background: #28a745;
    color: white;
    border: none;
}

.write-review-btn:hover {
    background: #218838;
}

.verify-email-btn,
.login-btn {
    background: #4a90e2;
    color: white;
    display: inline-block;
}

.verify-email-btn:hover,
.login-btn:hover {
    background: #357abd;
}

.cast-section,
.reviews-section {
    margin-bottom: 3rem;
}

.cast-section h2,
.reviews-section h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.cast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}

.cast-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.cast-photo {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.cast-info {
    padding: 1rem;
    text-align: center;
}

.cast-info h4 {
    margin-bottom: 0.5rem;
    color: #333;
}

.cast-info p {
    color: #666;
    font-size: 0.9rem;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.review-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.reviewer-info {
    display: flex;
    flex-direction: column;
}

.reviewer-name {
    font-weight: bold;
    color: #333;
}

.review-date {
    font-size: 0.9rem;
    color: #666;
}

.rating-stars {
    color: #ffc107;
    font-size: 1.2rem;
}

.review-comment {
    color: #444;
    line-height: 1.5;
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
.no-reviews {
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
    .movie-header {
        grid-template-columns: 1fr;
    }

    .movie-poster {
        max-width: 300px;
        margin: 0 auto;
    }
}
</style>
