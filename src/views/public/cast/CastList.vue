<template>
    <div class="cast-list">
        <h1>Cast Members</h1>

        <div class="search-box">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search cast members..."
                class="search-input"
                @input="handleSearch"
            />
        </div>

        <div v-if="loading" class="loading">Loading cast members...</div>

        <div v-else-if="error" class="error-message">
            {{ error }}
        </div>

        <div v-else-if="filteredCasts.length" class="cast-grid">
            <router-link
                v-for="cast in filteredCasts"
                :key="cast.id"
                :to="{ name: 'cast-detail', params: { id: cast.id } }"
                class="cast-card"
            >
                <img
                    :src="cast.photo || '/placeholder-cast.jpg'"
                    :alt="cast.name"
                    class="cast-photo"
                />
                <div class="cast-info">
                    <h3>{{ cast.name }}</h3>
                    <p class="cast-bio">{{ truncateText(cast.bio, 100) }}</p>
                    <div class="movie-count">
                        {{ cast.cast_movies?.length || 0 }} movies
                    </div>
                    <div class="view-details">View Details →</div>
                </div>
            </router-link>
        </div>

        <div v-else class="no-results">
            {{
                searchQuery
                    ? "No cast members found matching your search."
                    : "No cast members available."
            }}
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { casts } from "@/api";
import { handleApiError } from "@/api";

const castsList = ref([]);
const loading = ref(false);
const error = ref(null);
const searchQuery = ref("");

const filteredCasts = computed(() => {
    if (!searchQuery.value) return castsList.value;

    const query = searchQuery.value.toLowerCase();
    return castsList.value.filter(
        (cast) =>
            cast.name.toLowerCase().includes(query) ||
            cast.bio.toLowerCase().includes(query)
    );
});

const truncateText = (text, length) => {
    if (!text) return "";
    if (text.length <= length) return text;
    return text.substring(0, length) + "...";
};

const handleSearch = () => {
    // Debounce could be added here if needed
};

onMounted(async () => {
    try {
        loading.value = true;
        const response = await casts.getAll();
        castsList.value = response.data;
    } catch (err) {
        error.value = handleApiError(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.cast-list {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.cast-list h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

.search-box {
    margin-bottom: 2rem;
}

.search-input {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    display: block;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.cast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.cast-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}

.cast-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.cast-photo {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

.cast-info {
    padding: 1.5rem;
}

.cast-info h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

.cast-bio {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
    font-size: 0.9rem;
}

.movie-count {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.view-details {
    color: #4a90e2;
    font-weight: bold;
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

@media (max-width: 768px) {
    .cast-list {
        padding: 1rem;
    }

    .cast-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
}
</style>
