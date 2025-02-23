import { mount } from '@vue/test-utils';
import LoginComponent from '../../components/patient/LoginComponent.vue';
import { describe, it, expect, vi } from 'vitest';
import axios from 'axios';

// Mock axios for API calls
vi.mock('axios');

describe('LoginComponent.vue', () => {
    it('renders login form correctly', () => {
        const wrapper = mount(LoginComponent);
        expect(wrapper.find('h2').text()).toBe('Logowanie');
        expect(wrapper.find('input[type="text"]').exists()).toBe(true);
        expect(wrapper.find('input[type="password"]').exists()).toBe(true);
    });

    it('logs in successfully and redirects', async () => {
        const mockRouterPush = vi.fn();
        axios.post.mockResolvedValue({ data: { token: 'fake-jwt-token' } });

        const wrapper = mount(LoginComponent, {
            global: {
                mocks: { $router: { push: mockRouterPush } },
            },
        });

        await wrapper.find('input[type="text"]').setValue('JohnDoe');
        await wrapper.find('input[type="password"]').setValue('1983-04-12');
        await wrapper.find('form').trigger('submit.prevent');

        expect(axios.post).toHaveBeenCalledWith('http://localhost:8000/api/login', {
            login: 'JohnDoe',
            password: '1983-04-12',
        });

        expect(localStorage.getItem('jwt')).toBe('fake-jwt-token');
        expect(mockRouterPush).toHaveBeenCalledWith('/results');
    });

    it('shows an error on failed login', async () => {
        axios.post.mockRejectedValue(new Error('Invalid credentials'));

        const wrapper = mount(LoginComponent);
        await wrapper.find('form').trigger('submit.prevent');

        expect(wrapper.text()).toContain('Błąd logowania');
    });
});
