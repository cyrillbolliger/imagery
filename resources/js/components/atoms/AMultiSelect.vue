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
            <MultiSelect
                :id="id"
                :name="'a'+Math.random().toString(36).substring(8)"
                :options="options"
                :required="required"
                :selected-options="value"
                @select="$emit('input', $event)"
            ></MultiSelect>
        </template>
        <template #helptext v-if="helptext.length">
            {{helptext}}
        </template>
    </AFormGroup>
</template>

<script>
    import SlugifyMixin from "../../mixins/SlugifyMixin";
    import AFormGroup from "../atoms/AFormGroup";
    import {MultiSelect} from 'vue-search-select'

    export default {
        name: "AMultiSelect",
        components: {AFormGroup, MultiSelect},
        mixins: [SlugifyMixin],
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
            helptext: {
                type: String,
                default: ''
            }
        },
        computed: {
            id() {
                return this.slugify(this.label)
            }
        },
    }
</script>

<style scoped>

</style>
