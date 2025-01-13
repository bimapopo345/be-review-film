<template>
    <div class="genre-detail">
        <div v-if="loading" class="loading">Loading genre details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <template v-else-if="genre">
            <div class="genre-header">
                <h1>{{ genre.name }}</h1>
                <p class="movie-count">
                    {{ genre.movies?.length || 0 }} movies in this genre
                </p>
            </div>

            <div v-if="genre.movies?.length" class="movies-grid">
                <div
                    v-for="movie in genre.movies"
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
                        <p class="movie-description">
                            {{ truncateText(movie.description, 150) }}
                        </p>
                        <div class="movie-meta">
                            <span class="duration"
                                >{{ movie.duration }} min</span
                            >
                            <span class="rating" v-if="movie.reviews?.length">
                                ★ {{ calculateAverageRating(movie.reviews) }}
                            </span>
                        </div>
                        <router-link
                            :to="{
                                name: 'movie-detail',
                                params: { id: movie.id },
                            }"
                            class="view-details"
                        >
                            View Details
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else class="no-movies">
                No movies available in this genre yet.
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { genres } from "@/api";
import { handleApiError } from "@/api";

const route = useRoute();
const genre = ref(null);
const loading = ref(false);
const error = ref(null);

const truncateText = (text, length) => {
    if (!text) return "";
    if (text.length <= length) return text;
    return text.substring(0, length) + "...";
};

const calculateAverageRating = (reviews) => {
    if (!reviews?.length) return "N/A";
    const total = reviews.reduce((sum, review) => sum + review.rating, 0);
    return (total / reviews.length).toFixed(1);
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await genres.getById(route.params.id);
        genre.value = response.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.genre-detail {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.genre-header {
    text-align: center;
    margin-bottom: 3rem;
}

.genre-header h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.movie-count {
    color: #666;
    font-size: 1.1rem;
}

.movies-grid {
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
    font-size: 1.2rem;
}

.movie-year {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.movie-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
    font-size: 0.9rem;
}

.movie-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.rating {
    color: #f0ad4e;
}

.view-details {
    display: inline-block;
    background: #4a90e2;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.2s;
    width: 100%;
    text-align: center;
}

.view-details:hover {
    background: #357abd;
}

.loading,
.error-message,
.no-movies {
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
    .genre-detail {
        padding: 1rem;
    }

    .genre-header h1 {
        font-size: 2rem;
    }

    .movies-grid {
        grid-template-columns: 1fr;
    }
}
</style>
