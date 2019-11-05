<template>
    <nav>
        <button @click="close" class="btn btn-link d-none d-lg-block mt-2 mr-3" id="nav-hide" type="button">
            <i class="mdi mdi-close"></i>
            <span>{{$t('nav.hide')}}</span>
        </button>
        <h3 class="mt-3">{{$t('nav.title')}}</h3>
        <ul class="list-unstyled mt-3">
            <NavItem icon="image" to="/">{{$t('route.create_image')}}</NavItem>
            <NavItem icon="collections" to="/images/gallery">{{$t('route.gallery')}}</NavItem>
            <li>
                <NavItem active="" icon="settings" to="/admin/users">{{$t('route.settings')}}</NavItem>
                <ul class="list-unstyled ml-4">
                    <NavItem icon="chevron-right" to="/admin/users">{{$t('route.users')}}</NavItem>
                    <NavItem icon="chevron-right" to="/admin/groups">{{$t('route.groups')}}</NavItem>
                    <NavItem icon="chevron-right" to="/admin/logos">{{$t('route.logos')}}</NavItem>
                </ul>
            </li>
            <NavItem icon="account-box" to="/admin/users/:id">{{$t('route.account')}}</NavItem>
            <NavItem icon="power-settings-new" to="/logout">{{$t('route.logout')}}</NavItem>
        </ul>
    </nav>
</template>

<script>
    import NavItem from "./misc/NavItem";
    import {mapActions} from "vuex";

    export default {
        name: "BaseNav",
        components: {NavItem},
        methods: {
            ...mapActions('menu', ['close']),
        }
    }
</script>

<style lang="scss" scoped>
    nav {
        background: $dark;
        height: 100vh;
    }

    h3 {
        color: $gray-500;
        font-weight: bold;
    }

    #nav-hide {
        position: absolute;
        right: 0;
        color: $gray-500;
        padding: 0;

        &:hover, &:focus {
            text-decoration: none;
            color: $primary;
            outline: none;
        }

        i {
            font-size: 2.5em;
            display: block;
        }

        span {
            font-size: 0.7em;
            display: block;
            margin-top: -0.5em;
        }
    }
</style>
