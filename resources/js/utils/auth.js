import router from '@/router';

export const handleLogout = () => {
  localStorage.removeItem('token');
  router.push('/login');
};

export const getAuthToken = () => {
  return localStorage.getItem('token');
};

export const isAuthenticated = () => {
  return !!getAuthToken();
};

export const setAuthToken = (token) => {
  localStorage.setItem('token', token);
};
