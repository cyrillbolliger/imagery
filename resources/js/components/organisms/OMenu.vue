<template>
    <nav class="o-menu">
        <AIconNamed
            @clicked="close"
            class="o-menu__close mt-2 mr-3"
            icon="close"
            modifier="light"
        >{{$t('nav.hide')}}
        </AIconNamed>
        <h3 class="o-menu__title mt-3">{{$t('nav.title')}}</h3>
        <ul class="o-menu__item-list list-unstyled mt-3">
            <MMenuItem icon="image" to="/">{{$t('route.create_image')}}</MMenuItem>
            <MMenuItem icon="collections" to="/images/gallery">{{$t('route.gallery')}}</MMenuItem>
            <li>
                <MMenuItem active="" icon="settings" to="/admin/users">{{$t('route.settings')}}</MMenuItem>
                <ul class="list-unstyled ml-4">
                    <MMenuItem icon="chevron-right" to="/admin/users">{{$t('route.users')}}</MMenuItem>
                    <MMenuItem icon="chevron-right" to="/admin/groups">{{$t('route.groups')}}</MMenuItem>
                    <MMenuItem icon="chevron-right" to="/admin/logos">{{$t('route.logos')}}</MMenuItem>
                </ul>
            </li>
            <MMenuItem icon="account-box" to="/admin/users/:id">{{$t('route.account')}}</MMenuItem>
            <MMenuItem icon="power-settings-new" to="/logout">{{$t('route.logout')}}</MMenuItem>
        </ul>
    </nav>
</template>

<script>
    import MMenuItem from "../molecules/MMenuItem";
    import {mapActions} from "vuex";

    export default {
        name: "OMenu",
        components: {MMenuItem},
        methods: {
            ...mapActions('menu', ['close']),
        }
    }
</script>

<style lang="scss" scoped>
    .o-menu {
        background: $dark;
        height: 100vh;

        &__close {
            position: absolute;
            right: 0;
        }
    }


    .o-menu__title {
        color: $gray-500;
        font-weight: bold;
    }
</style>
