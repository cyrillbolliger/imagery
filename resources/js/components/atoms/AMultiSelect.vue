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
                    :is-error="validation && $v.value.$error"
                    @select="onInput"
                    v-if="!loading"
            />
            <div class="d-flex justify-content-center"
                 v-if="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </template>
        <template #helptext v-if="helptext.length || (validation && $v.value.$error)">
            {{helptext}}
            <div v-if="validation && $v.value.$error">
                <div v-if="validation.messages">
                    <div v-for="(message, key) in validation.messages">
                        <span class="text-danger" v-if="false === $v.value[key]">{{message}}</span>
                    </div>
                </div>
                <div v-else>
                    <span class="text-danger">{{validation.message}}</span>
                </div>
            </div>
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
            },
            loading: {
                type: Boolean,
                default: false
            },
            validation: {
                default: null,
                type: Object
            },
        },
        computed: {
            id() {
                return this.slugify(this.label)
            }
        },
        validations() {
            if (this.validation) {
                return {
                    value: this.validation.rules
                }
            }
        },
        methods: {
            onInput(event) {
                this.$emit('input', event);

                if (this.validation) {
                    this.$v.value.$touch();
                }
            }
        },
    }
</script>

<style scoped>

</style>
