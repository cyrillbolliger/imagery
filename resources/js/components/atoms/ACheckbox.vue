<template>
    <div class="form-check">
        <input
            :checked="value"
            :id="id"
            @change="$emit('input', $event.target.checked)"
            class="form-check-input"
            type="checkbox">
        <label :for="id" class="form-check-label">
            {{label}}
        </label>
    </div>
</template>

<script>
    import SlugifyMixin from "../../mixins/SlugifyMixin";

    export default {
        name: "ACheckbox",
        props: {
            label: {
                required: true,
                type: String
            },
            required: {
                default: false,
                type: Boolean
            },
            value: {
                default: false,
                type: Boolean
            }
        },
        computed: {
            id() {
                return this.slugify(this.label)
            }
        },
        mixins: [SlugifyMixin]
    }
</script>

<style scoped>

</style>
