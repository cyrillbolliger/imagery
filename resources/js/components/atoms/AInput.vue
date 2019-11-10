<template>
    <AFormGroup>
        <template #label>
            <label :for="id" class="col-form-label">
                {{label}}
                <span v-if="required">*</span>
            </label>
        </template>
        <template #input>
            <input
                :id="id"
                :required="required"
                :type="type"
                :value="value"
                @input="$emit('input', $event.target.value)"
                class="form-control">
        </template>
    </AFormGroup>
</template>

<script>
    import Slugify from "../../mixins/Slugify";
    import AFormGroup from "../atoms/AFormGroup";

    export default {
        name: "AInput",
        components: {AFormGroup},
        props: {
            label: {
                required: true,
                type: String
            },
            type: {
                default: 'text',
                type: String
            },
            required: {
                default: false,
                type: Boolean
            },
            value: {
                default: '',
                type: String
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
