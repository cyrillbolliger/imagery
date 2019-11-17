<template>
    <div
        :class="use || manage ? 'a-group-select--active' : ''"
        class="a-group-select"
    >
        <span>
            {{name}}
            <i class="mdi mdi-visibility" v-if="use"></i>
            <i class="mdi mdi-edit" v-if="manage"></i>
        </span>
        <div>
            <input
                :checked="use"
                :disabled="useDisabled"
                :id="useId"
                @change="$emit('use', $event.target.checked)"
                class="a-group-select__input"
                type="checkbox">
            <label :for="useId">
                {{$t('role.use')}}
            </label>
            <input
                :checked="manage"
                :disabled="manageDisabled"
                :id="manageId"
                @change="$emit('manage', $event.target.checked)"
                class="a-group-select__input"
                type="checkbox">
            <label :for="manageId">
                {{$t('role.manage')}}
            </label>
        </div>
    </div>
</template>

<script>
    import SlugifyMixin from "../../mixins/SlugifyMixin";

    export default {
        name: "AGroupSelect",
        mixins: [SlugifyMixin],

        data() {
            return {
                id: 0
            }
        },

        props: {
            name: {
                required: true,
                type: String
            },
            use: {
                required: true,
                type: Boolean
            },
            manage: {
                required: true,
                type: Boolean
            },
            useDisabled: {
                required: true,
                type: Boolean
            },
            manageDisabled: {
                required: true,
                type: Boolean
            }
        },

        computed: {
            useId() {
                return `use-${this.id}`
            },
            manageId() {
                return `manage-${this.id}`
            },
        },

        created() {
            this.$store.dispatch('counter/increment');
            this.id = this.$store.getters['counter/get'];
        },
    }
</script>

<style lang="scss" scoped>
    .a-group-select {
        width: 100%;
        display: flex;
        justify-content: space-between;

        &__input {
            margin-left: 1em;
        }

        &--active {
            color: $primary;
        }
    }
</style>
