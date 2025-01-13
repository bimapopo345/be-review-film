<template>
    <div class="home">
        <section class="hero">
            <h1>Welcome to Movie Review</h1>
            <p>Discover, review, and share your favorite movies</p>
        </section>

        <section class="featured-movies" v-if="!loading">
            <h2>Featured Movies</h2>
            <div v-if="error" class="error-message">
                {{ error }}
            </div>

            <div v-else-if="movies.length" class="movie-grid">
                <div v-for="movie in movies" :key="movie.id" class="movie-card">
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
                No movies available at the moment.
            </div>
        </section>

        <section class="quick-links">
            <h2>Quick Links</h2>
            <div class="links-grid">
                <router-link to="/movies" class="quick-link">
                    <h3>Browse Movies</h3>
                    <p>Explore our full collection of movies</p>
                </router-link>
                <router-link to="/genres" class="quick-link">
                    <h3>Movie Genres</h3>
                    <p>Find movies by your favorite genre</p>
                </router-link>
                <router-link to="/casts" class="quick-link">
                    <h3>Cast Members</h3>
                    <p>Discover talented actors and actresses</p>
                </router-link>
                <router-link
                    v-if="isAuthenticated && !isVerified"
                    to="/generate-otp"
                    class="quick-link verify"
                >
                    <h3>Verify Email</h3>
                    <p>Verify your email to write reviews</p>
                </router-link>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { movies } from "@/api";
import { useAuthStore } from "@/stores/auth";
import { handleApiError } from "@/api";

const moviesList = ref([]);
const loading = ref(false);
const error = ref(null);

const auth = useAuthStore();
const isAuthenticated = computed(() => auth.isAuthenticated);
const isVerified = computed(() => auth.isVerified);

onMounted(async () => {
    try {
        loading.value = true;
        const response = await movies.getAll();
        moviesList.value = response.data.slice(0, 6); // Show only first 6 movies
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.home {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.hero {
    text-align: center;
    padding: 3rem 1rem;
    background: linear-gradient(to right, #4a90e2, #357abd);
    color: white;
    border-radius: 8px;
    margin-bottom: 3rem;
}

.hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.featured-movies,
.quick-links {
    margin-bottom: 3rem;
}

.featured-movies h2,
.quick-links h2 {
    margin-bottom: 1.5rem;
    color: #333;
}

.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
    height: 300px;
    object-fit: cover;
}

.movie-info {
    padding: 1rem;
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

.view-details {
    display: inline-block;
    background: #4a90e2;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.view-details:hover {
    background: #357abd;
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.quick-link {
    display: block;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.quick-link:hover {
    transform: translateY(-5px);
}

.quick-link h3 {
    color: #4a90e2;
    margin-bottom: 0.5rem;
}

.quick-link p {
    color: #666;
    font-size: 0.9rem;
}

.quick-link.verify {
    background: #28a745;
}

.quick-link.verify h3,
.quick-link.verify p {
    color: white;
}

.error-message {
    color: #dc3545;
    text-align: center;
    padding: 1rem;
    background: #f8d7da;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.no-movies {
    text-align: center;
    padding: 2rem;
    color: #666;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>
