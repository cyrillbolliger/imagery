<template>
    <AFormGroup>
        <template #label>
            <label :for="id" class="col-form-label">
                {{label}}
                <span v-if="required">*</span>
            </label>
        </template>
        <template #input>
            <!-- set random name to disable chrome's wrong autofill guess -->
            <ModelSelect
                :id="id"
                :name="'a'+Math.random().toString(36).substring(8)"
                :options="options"
                :required="required"
                :value="value"
                @input="$emit('input', $event)"
                class="form-control"></ModelSelect>
        </template>
    </AFormGroup>
</template>

<script>
    import Slugify from "../../mixins/Slugify";
    import AFormGroup from "../atoms/AFormGroup";
    import {ModelSelect} from 'vue-search-select'

    export default {
        name: "ASelect",
        components: {AFormGroup, ModelSelect},
        props: {
            label: {
                required: true,
                type: String
            },
            options: {
                required: true,
            },
            required: {
                default: false,
                type: Boolean
            },
            value: {
                required: true,
            },
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
