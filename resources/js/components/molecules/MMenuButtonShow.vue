<template>
    <transition name="bounce">
        <AIconNamed @clicked="open" icon="menu" v-if="!menuOpen">{{$t('nav.show')}}</AIconNamed>
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
        },
        data() {
            return {
                menuOpen: this.$store.getters['menu/isOpen']
            }
        },
        watch: {
            isOpen(value) {
                if (value) {
                    this.menuOpen = value;
                } else {
                    return setTimeout(() => this.menuOpen = value, 500);
                }
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
