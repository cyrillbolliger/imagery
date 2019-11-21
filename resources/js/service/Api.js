import axios from 'axios';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (!token) {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

export default () => {
    return axios.create({
        baseURL: '/api/1/',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token.content
        }
    });
}
