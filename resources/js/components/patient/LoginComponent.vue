<template>
  <div class="login-container">
    <form @submit.prevent="login" class="login-form">
      <h2>Patient Login</h2>
      
      <div class="form-group">
        <label for="login">Login</label>
        <input 
          type="text" 
          id="login" 
          v-model="form.login" 
          required 
          class="form-control"
          placeholder="Enter your login"
        >
      </div>

      <div class="form-group">
        <label for="password">Birth Date (YYYY-MM-DD)</label>
        <input 
          type="text" 
          id="password" 
          v-model="form.password" 
          required 
          class="form-control"
          placeholder="YYYY-MM-DD"
          maxlength="10"
          @input="formatBirthDate"
        >
      </div>

      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Loading...' : 'Login' }}
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginComponent',
  data() {
    return {
      form: {
        login: '',
        password: ''
      },
      error: null,
      loading: false
    }
  },
  methods: {
    formatBirthDate() {
      // Remove any non-digit characters
      let value = this.form.password.replace(/\D/g, '');
      
      // Format as YYYY-MM-DD
      if (value.length > 0) {
        // Handle year
        if (value.length > 4) {
          value = value.slice(0, 4) + '-' + value.slice(4);
        }
        // Handle month
        if (value.length > 7) {
          value = value.slice(0, 7) + '-' + value.slice(7);
        }
        // Limit to 10 characters (YYYY-MM-DD)
        value = value.slice(0, 10);
      }
      
      this.form.password = value;
    },
    async login() {
      this.loading = true;
      this.error = null;
      
      // Validate date format before submitting
      const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
      if (!dateRegex.test(this.form.password)) {
        this.error = 'Please enter a valid date in YYYY-MM-DD format';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/login', this.form);
        localStorage.setItem('token', response.data.token);
        this.$router.push('/patient/results');
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred during login';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-control {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary {
  background-color: #4CAF50;
  color: white;
}

.btn-primary:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.alert {
  padding: 0.75rem;
  border-radius: 4px;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style> 