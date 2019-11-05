<template>
    <div class="row">
        <transition name="slideLeft">
            <OMenu class="col-xl-3 sticky-top" v-if="isOpen"></OMenu>
        </transition>
        <main class="page-main col mt-3 mb-3">
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
    import OMenu from "./organisms/OMenu";
    import {mapGetters} from "vuex";

    export default {
        name: "App",
        components: {OMenu},
        computed: {
            ...mapGetters('menu', ['isOpen']),
        }
    }
</script>

<style lang="scss" scoped>
    .page-main {
        position: absolute;

        @include media-breakpoint-up(xl) {
            position: static;
        }
    }
</style>
