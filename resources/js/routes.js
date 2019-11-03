import UserIndex from './components/admin/users/UserIndex.vue';
import ImageCreate from './components/images/ImageCreate.vue';

export const routes = [
    {path: '', component: ImageCreate},
    {path: '/admin/users', component: UserIndex},
];
