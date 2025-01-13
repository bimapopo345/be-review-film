<template>
    <div class="cast-detail">
        <div v-if="loading" class="loading">Loading cast details...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <template v-else-if="cast">
            <div class="cast-header">
                <div class="cast-photo">
                    <img
                        :src="cast.photo || '/placeholder-cast.jpg'"
                        :alt="cast.name"
                    />
                </div>
                <div class="cast-info">
                    <h1>{{ cast.name }}</h1>
                    <p class="cast-bio">{{ cast.bio }}</p>
                    <div class="movie-count">
                        {{ cast.cast_movies?.length || 0 }} movies
                    </div>
                </div>
            </div>

            <div class="movies-section">
                <h2>Movies</h2>

                <div v-if="cast.cast_movies?.length" class="movies-grid">
                    <div
                        v-for="castMovie in cast.cast_movies"
                        :key="castMovie.id"
                        class="movie-card"
                    >
                        <img
                            :src="
                                castMovie.movie.poster ||
                                '/placeholder-movie.jpg'
                            "
                            :alt="castMovie.movie.title"
                            class="movie-poster"
                        />
                        <div class="movie-info">
                            <h3>{{ castMovie.movie.title }}</h3>
                            <p class="movie-year">
                                {{ castMovie.movie.release_date }}
                            </p>
                            <p class="cast-role">Role: {{ castMovie.role }}</p>
                            <div class="movie-meta">
                                <span class="duration"
                                    >{{ castMovie.movie.duration }} min</span
                                >
                                <span
                                    class="rating"
                                    v-if="castMovie.movie.reviews?.length"
                                >
                                    ★
                                    {{
                                        calculateAverageRating(
                                            castMovie.movie.reviews
                                        )
                                    }}
                                </span>
                            </div>
                            <div class="movie-genres">
                                <span
                                    v-for="genre in castMovie.movie.genres"
                                    :key="genre.id"
                                    class="genre-tag"
                                >
                                    {{ genre.name }}
                                </span>
                            </div>
                            <p class="movie-description">
                                {{
                                    truncateText(
                                        castMovie.movie.description,
                                        150
                                    )
                                }}
                            </p>
                            <router-link
                                :to="{
                                    name: 'movie-detail',
                                    params: { id: castMovie.movie.id },
                                }"
                                class="view-details"
                            >
                                View Movie Details
                            </router-link>
                        </div>
                    </div>
                </div>

                <div v-else class="no-movies">
                    No movies available for this cast member.
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { casts } from "@/api";
import { handleApiError } from "@/api";

const route = useRoute();
const cast = ref(null);
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
        const response = await casts.getById(route.params.id);
        cast.value = response.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.cast-detail {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.cast-header {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

.cast-photo img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.cast-info h1 {
    margin-bottom: 1rem;
    color: #333;
}

.cast-bio {
    margin-bottom: 1rem;
    line-height: 1.6;
    color: #444;
}

.movie-count {
    color: #666;
    font-size: 1.1rem;
}

.movies-section {
    margin-bottom: 3rem;
}

.movies-section h2 {
    margin-bottom: 1.5rem;
    color: #333;
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
}

.movie-year {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.cast-role {
    color: #4a90e2;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.movie-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: #666;
    font-size: 0.9rem;
}

.rating {
    color: #f0ad4e;
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
    font-size: 0.9rem;
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
    .cast-detail {
        padding: 1rem;
    }

    .cast-header {
        grid-template-columns: 1fr;
    }

    .cast-photo {
        max-width: 300px;
        margin: 0 auto;
    }

    .movies-grid {
        grid-template-columns: 1fr;
    }
}
</style>
