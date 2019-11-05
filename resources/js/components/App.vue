<template>
    <div class="row">
        <OMenu class="col-lg-3 sticky-top" v-if="isOpen"></OMenu>
        <router-view></router-view>
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

<style scoped>

</style>
