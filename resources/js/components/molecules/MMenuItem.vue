<template>
    <li class="m-menu-item pb-2 pb-md-3">
        <router-link :active-class="active" :to="to" class="m-menu-item__link" :exact="exact">
            <div class="d-flex">
                <i v-if="materialIcon !== ''" :class="materialIconClass" class="m-menu-item__icon"></i>
                <i v-if="svgIcon !== ''" :style="svgIconStyle" class="m-menu-item__icon m-menu-item__icon--svg"></i>
                <span class="m-menu-item__label pl-2 align-self-center">
                    <slot></slot>
                </span>
            </div>
        </router-link>
    </li>
</template>

<script>
    export default {
        name: "MMenuItem",
        props: {
            to: {type: String, required: true},
            active: {type: String, default: 'active'},
            materialIcon: {type: String, default: ''},
            svgIcon: {type: String, default: ''},
            exact: {type: Boolean, default: true},
        },
        computed: {
            materialIconClass() {
                return "mdi mdi-" + this.materialIcon;
            },
            svgIconStyle() {
                return `background-image: url(${this.svgIcon})`;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .m-menu-item__icon--svg--active {
        filter: brightness(0.5) sepia(1) hue-rotate(28deg) saturate(6);
    }

    .m-menu-item {
        &__link {
            color: $gray-200;

            &:hover, &:focus {
                color: $secondary;
                outline: none;
                text-decoration: none;

                .m-menu-item__label {
                    text-decoration: underline;
                }

                .m-menu-item__icon--svg {
                    @extend .m-menu-item__icon--svg--active;
                }
            }

            &.active {
                color: $secondary;

                .m-menu-item__icon--svg {
                    @extend .m-menu-item__icon--svg--active;
                }
            }
        }


        &__icon {
            font-size: 1.5em;

            &--svg {
                width: 1em;
                height: 1em;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                filter: brightness(95%);
            }
        }
    }
</style>
