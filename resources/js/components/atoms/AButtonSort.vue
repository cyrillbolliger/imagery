<template>
    <button
        @click="click"
        class="a-button-sort btn btn-link p-0 d-flex"
    >
        <span class="a-button-sort__label font-weight-bold"><slot></slot></span>
        <span class="a-button-sort__icon">
            <i :class="iconClass" v-if="state"></i>
        </span>
    </button>
</template>

<script>
    export default {
        name: "AButtonSort",
        data() {
            return {
                state: this.direction
            }
        },
        props: {
            direction: {
                default: null,
                validator: function (value) {
                    return [null, 'asc', 'desc'].indexOf(value) !== -1
                }
            }
        },
        computed: {
            iconClass() {
                const direction = 'asc' === this.state ? 'upward' : 'downward';
                return `mdi mdi-arrow-${direction}`;
            }
        },
        methods: {
            click() {
                switch (this.state) {
                    case null:
                        this.state = 'asc';
                        break;
                    case 'asc':
                        this.state = 'desc';
                        break;
                    case 'desc':
                        this.state = null;
                        break;
                }

                this.$emit('directionChanged', this.state);
            }
        },
        watch: {
            direction(value) {
                this.state = value;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .a-button-sort {
        padding: 0;
        color: $primary;

        &__label,
        &__icon {
            font-size: 0.875em;
            text-transform: uppercase;
        }

        &:hover, &:focus {
            color: darken($primary, 20);
            outline: none;
            text-decoration: none;

            .a-button-sort__label {
                text-decoration: underline;
            }
        }

        &__icon {
            width: 1em;
            display: block;
            overflow: hidden;
            padding-left: 0.1em;
        }
    }
</style>
