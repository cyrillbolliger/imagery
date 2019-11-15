import UserIndex from './components/pages/UserIndex.vue';
import ImageCreate from './components/pages/ImageCreate.vue';
import GroupIndex from "./components/pages/GroupIndex";

export const routes = [
    {path: '', component: ImageCreate},
    {path: '/admin/users', component: UserIndex},
    {path: '/admin/groups', component: GroupIndex}
    // {path: '*', component: NotFound}
];
