<template>
    <div class="o-snackbars">
        <MSnackbar
            :key="snackbar.id"
            :snackbar="snackbar"
            v-for="snackbar in queue"
        ></MSnackbar>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import MSnackbar from "../molecules/MSnackbar";

    export default {
        name: "OSnackbars",
        components: {MSnackbar},
        computed: {
            ...mapGetters({
                queue: 'snackbar/queue'
            })
        }
    }
</script>

<style lang="scss" scoped>
    .o-snackbars {
        z-index: $zindex-popover;
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 800px;
    }
</style>
