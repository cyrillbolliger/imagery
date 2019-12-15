<template>
    <div class="row">
        <transition name="slideLeft">
            <OMenu class="page-nav col-xl-3 sticky-top" v-if="isOpen"></OMenu>
        </transition>
        <main class="page-main col mt-3 mb-3">
            <router-view></router-view>
        </main>
        <OSnackbars></OSnackbars>
    </div>
</template>

<script>
    import OMenu from "./organisms/OMenu";
    import {mapGetters} from "vuex";
    import OSnackbars from "./organisms/OSnackbars";
    import WindowMixin from "../mixins/WindowMixin";

    export default {
        name: "App",
        components: {OSnackbars, OMenu},
        mixins: [WindowMixin],
        computed: {
            ...mapGetters('menu', ['isOpen']),
        },
        watch: {
            isXl(isXl) {
                if (isXl) {
                    this.$store.dispatch('menu/open');
                } else {
                    this.$store.dispatch('menu/close');
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .page-main {
        position: absolute;

        @include media-breakpoint-up(xl) {
            position: static;
        }
    }

    .page-nav {
        animation-duration: 0.5s;
    }
</style>
