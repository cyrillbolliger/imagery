<!-- component inspired by: https://codepen.io/zsherman/pen/bRrMoQ -->

<template>
    <div class="a-loader">
        <div class="a-loader__bar a-loader__bar--1"></div>
        <div class="a-loader__bar a-loader__bar--2"></div>
    </div>
</template>

<script>
    export default {
        name: "ALoaderBar"
    }
</script>

<style lang="scss" scoped>
    .a-loader {
        background: lighten($primary, 50);
        height: 3px;
        position: relative;
        width: 100%;
        overflow: hidden;
        animation: start .3s ease-in;

        &__bar {
            position: absolute;
            background: $primary;
            transition: transform .2s linear;
            left: 0;
            top: 0;
            bottom: 0;
            width: 100%;
            animation: progressLinearMovement 2.5s infinite;

            &--2 {
                left: -100%;
                animation-delay: .7s;
            }

        }
    }

    @keyframes progressLinearMovement {
        0% {
            left: -100%;
        }

        50% {
            left: 100%;
        }

        100% {
            left: 100%;
        }
    }
</style>
