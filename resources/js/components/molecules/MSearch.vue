<template>
    <div class="m-search">
        <input
            :placeholder="$t('search.search')"
            @keyup="search"
            class="m-search__input form-control"
            type="text"
            v-model="term"
        />
        <i class="m-search__icon mdi mdi-search"></i>
    </div>
</template>

<script>
    import debounce from 'lodash/debounce';

    export default {
        name: "MSearch",
        data() {
            return {
                term: this.initialTerm,
            }
        },

        props: {
            debounceDelay: {
                type: Number,
                default: 250,
            },
            initialTerm: {
                type: String,
                default: '',
            },
        },

        methods: {
            search() {
                const action = () => this.$emit('search', this.term);
                debounce(action, this.debounceDelay)();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .m-search {
        width: 100%;
        position: relative;
        margin-right: 3em;

        &__input:focus + &__icon {
            color: $primary;
        }

        &__icon {
            position: absolute;
            right: 0.25em;
            top: 0.125em;
            color: $gray-500;
            font-size: 2em;
            transition: color ease-in-out 0.15s;
        }
    }
</style>
