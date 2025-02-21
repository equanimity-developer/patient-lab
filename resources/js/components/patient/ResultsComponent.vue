<template>
  <div class="results-container">
    <h2>Your Results</h2>

    <div v-if="loading" class="loading">
      Loading results...
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="results.length === 0" class="no-results">
      No results found.
    </div>

    <div v-else class="results-list">
      <div v-for="result in results" :key="result.id" class="result-item">
        <h3>{{ result.title }}</h3>
        <p>{{ result.description }}</p>
        <p class="date">Date: {{ formatDate(result.created_at) }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ResultsComponent',
  data() {
    return {
      results: [],
      loading: true,
      error: null
    }
  },
  created() {
    this.fetchResults();
  },
  methods: {
    async fetchResults() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('/api/results', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        this.results = response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Error fetching results';
        if (error.response?.status === 401) {
          this.$router.push('/login');
        }
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    }
  }
}
</script>

<style scoped>
.results-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 1rem;
}

.loading, .no-results {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.results-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.result-item {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.result-item h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.date {
  color: #666;
  font-size: 0.9rem;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  padding: 0.75rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}
</style>
