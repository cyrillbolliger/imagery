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
    export default {
        name: "MSearch",
        data() {
            return {
                term: ''
            }
        },
        methods: {
            search() {
                this.$emit('search', this.term);
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
