<template>
    <header class="m-header row justify-content-between align-items-start">
        <h1 class="m-header__title col">
            <slot/>
        </h1>
        <MMenuButtonShow class="m-header__menu-show-button pt-1 pr-2"/>
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
