<template>
  <div class="login-container">
    <form @submit.prevent="login" class="login-form">
      <h2>{{ $t('auth.login.title') }}</h2>

      <div class="form-group">
        <label for="login">{{ $t('auth.login.loginLabel') }}</label>
        <input
          type="text"
          id="login"
          v-model="form.login"
          required
          class="form-control"
          :placeholder="$t('auth.login.loginPlaceholder')"
        >
      </div>

      <div class="form-group">
        <label for="password">{{ $t('auth.login.birthDateLabel') }}</label>
        <input
          type="text"
          id="password"
          v-model="form.password"
          required
          class="form-control"
          :placeholder="$t('auth.login.birthDatePlaceholder')"
          maxlength="10"
          @input="formatBirthDate"
        >
      </div>

      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? $t('auth.login.loading') : $t('auth.login.submitButton') }}
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';
import { formatDateString, isValidDateFormat} from "../../utils/dateUtils.js";

export default {
  name: 'LoginComponent',
  created() {
    if (localStorage.getItem('token')) {
      this.$router.push('/patient/results');
    }
  },
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
      this.form.password = formatDateString(this.form.password);
    },
    async login() {
      this.loading = true;
      this.error = null;

      if (!isValidDateFormat(this.form.password)) {
        this.error = this.$t('auth.login.errors.invalidDate');
        this.loading = false;
        return;
      }

      try {
        const response = await axios.post('/api/login', this.form);
        localStorage.setItem('token', response.data.token);
        this.$router.push('/patient/results');
      } catch (error) {
        this.error = error.response?.data?.message || this.$t('auth.login.errors.default');
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
