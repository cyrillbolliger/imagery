<template>
    <li class="m-menu-item pb-2 pb-md-3">
        <router-link :active-class="active" :to="to" class="m-menu-item__link" exact>
            <div class="d-flex">
                <i :class="iconClass" class="m-menu-item__icon" v-if="icon !== ''"></i>
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
            to: {String, required: true},
            active: {String, default: 'active'},
            icon: {String, default: ''},
        },
        computed: {
            iconClass() {
                return "mdi mdi-" + this.icon;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .m-menu-item {
        &__link {
            color: $gray-200;

            &:hover, &:focus {
                color: $primary;
                outline: none;
                text-decoration: none;

                .m-menu-item__label {
                    text-decoration: underline;
                }
            }

            &.active {
                color: $primary;
            }
        }


        &__icon {
            font-size: 1.5em;
        }
    }
</style>
