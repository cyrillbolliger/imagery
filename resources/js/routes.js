import UserIndex from './components/pages/UserIndex.vue';
import ImageCreate from './components/pages/ImageCreate.vue';
import GroupIndex from "./components/pages/GroupIndex";
import LogoIndex from "./components/pages/LogoIndex";
import ImageGallery from "./components/pages/ImageGallery";
import UserLogout from "./components/pages/UserLogout";
import LogoDownload from "./components/pages/LogoDownload";

export const routes = [
    {
        path: '',
        component: ImageCreate
    },
    {
        path: '/images/gallery',
        component: ImageGallery,
        props: (route) => ({query: route.query.q}),
        name: 'gallery'
    },
    {
        path: '/admin/users/create',
        component: UserIndex,
        props: {create: true},
        name: 'usersCreate'
    },
    {
        path: '/admin/users/:userId',
        component: UserIndex,
        props: (route) => ({userId: route.params.userId, activation: route.query.activation}),
        name: 'usersEdit'
    },
    {
        path: '/admin/users',
        component: UserIndex,
        name: 'usersAll'
    },
    {
        path: '/admin/groups/create',
        component: GroupIndex,
        props: {create: true},
        name: 'groupsCreate'
    },
    {
        path: '/admin/groups/:groupId',
        component: GroupIndex,
        props: true,
        name: 'groupsEdit'
    },
    {
        path: '/admin/groups',
        component: GroupIndex,
        name: 'groupsAll'
    },
    {
        path: '/admin/logos/create',
        component: LogoIndex,
        props: {create: true},
        name: 'logosCreate'
    },
    {
        path: '/admin/logos/:logoId',
        component: LogoIndex,
        props: true,
        name: 'logosEdit'
    },
    {
        path: '/admin/logos',
        component: LogoIndex,
        name: 'logosAll'
    },
    {
        path: '/logos/download',
        component: LogoDownload,
        name: 'logosDownload'
    },
    {
        path: '/logout',
        component: UserLogout,
    },
    {
        path: '/home',
        redirect: '/'
    }
    // {path: '*', component: NotFound}
];
