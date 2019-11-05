<template>
    <transition name="bounce">
        <AIconNamed @clicked="open" icon="menu" v-if="!isOpen">{{$t('nav.show')}}</AIconNamed>
    </transition>
</template>

<script>
    import {mapActions, mapGetters} from "vuex";

    export default {
        name: "MMenuButtonShow",
        methods: {
            ...mapActions('menu', ['open']),
        },
        computed: {
            ...mapGetters('menu', ['isOpen']),
        }
    }
</script>

<style lang="scss" scoped>

</style>
