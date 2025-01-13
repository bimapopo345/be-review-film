<template>
    <div class="genre-list">
        <h1>Movie Genres</h1>

        <div v-if="loading" class="loading">Loading genres...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <div v-else-if="genres.length" class="genre-grid">
            <router-link
                v-for="genre in genres"
                :key="genre.id"
                :to="{ name: 'genre-detail', params: { id: genre.id } }"
                class="genre-card"
            >
                <div class="genre-content">
                    <h2>{{ genre.name }}</h2>
                    <div class="movie-count">
                        {{ genre.movies?.length || 0 }} movies
                    </div>
                    <div class="view-details">View Movies →</div>
                </div>
            </router-link>
        </div>

        <div v-else class="no-genres">No genres available at the moment.</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { genres as genresApi } from "@/api";
import { handleApiError } from "@/api";

const genres = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(async () => {
    try {
        loading.value = true;
        const response = await genresApi.getAll();
        genres.value = response.data;
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
    max-width: 1200px;
    margin: 0 auto;
}

.genre-list h1 {
    margin-bottom: 2rem;
    color: #333;
    text-align: center;
}

.genre-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.genre-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}

.genre-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.genre-content {
    padding: 2rem;
    text-align: center;
}

.genre-content h2 {
    margin-bottom: 1rem;
    color: #333;
    font-size: 1.5rem;
}

.movie-count {
    color: #666;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.view-details {
    color: #4a90e2;
    font-weight: bold;
}

.loading,
.error-message,
.no-genres {
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
    .genre-list {
        padding: 1rem;
    }

    .genre-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
}
</style>
