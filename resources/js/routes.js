import UserIndex from './components/pages/UserIndex.vue';
import ImageCreate from './components/pages/ImageCreate.vue';

export const routes = [
    {path: '', component: ImageCreate},
    {path: '/admin/users', component: UserIndex},
    // {path: '*', component: NotFound}
];
