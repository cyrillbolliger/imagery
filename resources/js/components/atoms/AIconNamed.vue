<template>
    <button
        :class="modifierClass"
        @click="$emit('clicked')"
        class="a-icon-named btn btn-link"
    >
        <i :class="iconClass" class="a-icon-named__icon"></i>
        <span class="a-icon-named__label"><slot></slot></span>
    </button>
</template>

<script>
    export default {
        name: "AIconNamed",
        props: {
            icon: {String, default: 'insert-emoticon'},
            modifier: {String, default: ''},
        },
        computed: {
            iconClass() {
                return `mdi mdi-${this.icon}`;
            },
            modifierClass() {
                return `a-icon-named--${this.modifier}`;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .a-icon-named {
        padding: 0;
        color: $primary;

        &:hover, &:focus {
            color: darken($primary, 20);
            outline: none;
            text-decoration: none;

            .a-icon-named__label {
                text-decoration: underline;
            }
        }

        &__label {
            font-size: 0.7em;
            display: block;
            margin-top: -0.5em;
        }

        &__icon {
            font-size: 2.5em;
            display: block;
        }

        &--light {
            color: $gray-500;

            &:hover, &:focus {
                color: lighten($gray-500, 20);
            }
        }
    }
</style>
