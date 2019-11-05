<template>
    <button @click="open" class="btn btn-link" id="nav-show" type="button" v-if="!isOpen">
        <i class="mdi mdi-menu"></i>
        <span>{{$t('nav.show')}}</span>
    </button>
</template>

<script>
    import {mapActions, mapGetters} from "vuex";

    export default {
        name: "NavShowButton",
        methods: {
            ...mapActions('menu', ['open']),
        },
        computed: {
            ...mapGetters('menu', ['isOpen']),
        }
    }
</script>

<style lang="scss" scoped>
    #nav-show {
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
