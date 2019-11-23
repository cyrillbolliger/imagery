import UserIndex from './components/pages/UserIndex.vue';
import ImageCreate from './components/pages/ImageCreate.vue';
import GroupIndex from "./components/pages/GroupIndex";

export const routes = [
    {path: '', component: ImageCreate},
    {path: '/admin/users/create', component: UserIndex, props: {create: true}, name: 'usersCreate'},
    {path: '/admin/users/:userId', component: UserIndex, props: true, name: 'usersEdit'},
    {path: '/admin/users', component: UserIndex, name: 'usersAll'},
    {path: '/admin/groups', component: GroupIndex}
    // {path: '*', component: NotFound}
];
