<template>
    <div class="row">
        <BaseNav class="col-lg-3 sticky-top" v-if="isOpen"></BaseNav>
        <router-view></router-view>
    </div>
</template>

<script>
    import BaseNav from "./BaseNav";
    import {mapGetters} from "vuex";

    export default {
        name: "App",
        components: {BaseNav},
        computed: {
            ...mapGetters('menu', ['isOpen']),
        }
    }
</script>

<style scoped>

</style>
