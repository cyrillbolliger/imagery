<template>
    <li class="pb-2 pb-md-3">
        <router-link :active-class="active" :to="to" class="nav-item" exact>
            <div class="d-flex">
                <i :class="iconClass" v-if="icon !== ''"></i>
                <span class="pl-2 align-self-center">
                    <slot></slot>
                </span>
            </div>
        </router-link>
    </li>
</template>

<script>
    export default {
        name: "NavItem",
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
    .nav-item {
        color: $gray-200;

        &:hover, &:focus {
            color: $primary;
            outline: none;
            text-decoration: none;

            span {
                text-decoration: underline;
            }
        }

        &.active {
            color: $primary;
        }

        i {
            font-size: 1.5em;
        }
    }
</style>
