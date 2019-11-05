<template>
    <header class="m-header row justify-content-between">
        <h1 class="m-header__title col">
            <slot></slot>
        </h1>
        <MMenuButtonShow class="m-header__menu-show-button"></MMenuButtonShow>
    </header>
</template>

<script>
    import MMenuButtonShow from "./MMenuButtonShow";

    export default {
        name: "MHeader",
        components: {MMenuButtonShow}
    }
</script>

<style lang="scss" scoped>
    .m-header {
        &__menu-show-button {
            margin-top: -1em;
        }
    }
</style>
