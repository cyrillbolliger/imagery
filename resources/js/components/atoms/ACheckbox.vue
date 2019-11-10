<template>
    <div class="form-check">
        <input
            :checked="value"
            :id="id"
            @input="$emit('input', 'on' === $event.target.value)"
            class="form-check-input"
            type="checkbox">
        <label :for="id" class="form-check-label">
            {{label}}
        </label>
    </div>
</template>

<script>
    import Slugify from "../../mixins/Slugify";

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
        mixins: [Slugify]
    }
</script>

<style scoped>

</style>
