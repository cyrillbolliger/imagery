<template>
    <nav class="o-menu">
        <AIconNamed
            @clicked="close"
            class="o-menu__close mt-2 mr-3"
            icon="close"
            modifier="light"
            v-if="!this.isXl"
        >{{$t('nav.hide')}}
        </AIconNamed>
        <h3 class="o-menu__title mt-3">{{$t('nav.title')}}</h3>
        <ul class="o-menu__item-list list-unstyled mt-3">
            <MMenuItem materialIcon="image" to="/">{{$t('route.create_image')}}</MMenuItem>
            <MMenuItem :exact="false" materialIcon="collections" to="/images/gallery">{{$t('route.gallery')}}</MMenuItem>
            <li v-if="isAdmin">
                <MMenuItem active="" materialIcon="settings" to="/admin/users">{{$t('route.settings')}}</MMenuItem>
                <ul class="list-unstyled ml-4">
                    <MMenuItem :exact="false" materialIcon="chevron-right" to="/admin/logos">{{$t('route.logos')}}</MMenuItem>
                    <MMenuItem :exact="false" materialIcon="chevron-right" to="/admin/groups">{{$t('route.groups')}}</MMenuItem>
                    <MMenuItem :exact="false" materialIcon="chevron-right" to="/admin/users">{{$t('route.users')}}</MMenuItem>
                </ul>
            </li>
            <MMenuItem :to="`/admin/users/${id}`" materialIcon="account-box">{{$t('route.profile')}}</MMenuItem>
            <MMenuItem :to="`/logos/download`" svgIcon="/images/logo-icon.svg">{{$t('route.logos_download')}}</MMenuItem>
            <MMenuItem materialIcon="power-settings-new" to="/logout">{{$t('route.logout')}}</MMenuItem>
        </ul>
        <ul class="o-menu__item-list o-menu__item-list--small o-menu__item-list--horizontal list-unstyled mt-3">
            <MMenuItem :exact="false" class="pr-4" to="/translate">{{$t('route.translate')}}</MMenuItem>
            <MMenuItem :exact="false" class="pr-4" to="/feedback">{{$t('route.feedback')}}</MMenuItem>
        </ul>
    </nav>
</template>

<script>
    import MMenuItem from "../molecules/MMenuItem";
    import {mapActions, mapGetters} from "vuex";
    import WindowMixin from "../../mixins/WindowMixin";

    export default {
        name: "OMenu",
        components: {MMenuItem},
        mixins: [WindowMixin],
        methods: {
            ...mapActions('menu', ['close']),
        },
        computed: {
            ...mapGetters('user', ['isAdmin', 'id']),
        },
        watch: {
            '$route'() {
                if (!this.isXl) {
                    this.$store.dispatch('menu/close');
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .o-menu {
        background: $dark;
        min-height: 100vh;
        max-width: 350px;
        box-shadow: 0 0 1rem 0 rgba(0, 0, 0, 0.5);
        position: fixed;

        @include media-breakpoint-up(xl) {
            max-width: none;
            box-shadow: none;
            position: sticky;
        }

        &__close {
            position: absolute;
            right: 0;
        }

        &__item-list {
            &--small {
                font-size: $font-size-sm;
            }

            &--horizontal {
                display: flex;
            }
        }
    }


    .o-menu__title {
        color: $gray-500;
        font-weight: bold;
    }
</style>
